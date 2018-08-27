<?php

use \Sincco\Sfphp\Response;

class AvanzadaController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$userData = $this->helper('UsersAccount')->getUserData();
		$view = $this->newView('Gestion\Avanzada');
		$view->nombreUsuario = $userData['userName'];

		$view->render();
	}

	public function apiUpload() {
		$contrato = $this->getParams('contrato');
		if(!is_dir(PATH_ROOT . '/_expedientes/' . $contrato)){
			mkdir(PATH_ROOT . '/_expedientes/' . $contrato, 0777);
			chmod(PATH_ROOT . '/_expedientes/' . $contrato, 0777);
		}
		$imagenes = array_keys($_FILES['file']['name']);
		try{
			foreach ($imagenes as $_imagen) {
				$tmp_name = $_FILES['file']['tmp_name'][$_imagen];
				$extension = explode('.', $_FILES['file']['name'][$_imagen]);
				$extension = array_pop($extension);
				$name = $_imagen . '.png';
				imagepng(imagecreatefromstring(file_get_contents($tmp_name)), PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name);
				$imagen=imagecreatefrompng(PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name);
				$watermarktext="adp.itron.mx\n" . date("Y-m-d H:i") . "\nContrato " . $contrato . "\n" . $_imagen;
				$blanco = imagecolorallocate($imagen, 255, 255, 255);
				$negro = imagecolorallocate($imagen, 0, 0, 0);
				imagettftext($imagen, 30, 0, 21, 30, $negro, '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', $watermarktext);
				imagettftext($imagen, 30, 0, 20, 29, $blanco, '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', $watermarktext);
				imagepng($imagen,PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name);
				chmod(PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name, 0777);
			}
			new Response( 'json', [ 'respuesta'=>true ] );
		} catch (Exception $e) {
			new Response( 'json', [ 'respuesta'=>false ] );
		}
	}

	public function apiGuardar() {
		$contrato = $this->getParams('contrato');
		$files = scandir(PATH_ROOT . '/_expedientes/' . $contrato);
		array_shift($files);
		array_shift($files);
		$adjuntos = array();
		foreach ($files as $adjunto) {

			array_push($adjuntos, str_replace(' ', '%20', $adjunto));
		}
		new Response( 'json', [ 'respuesta'=>count($adjuntos), 'adjuntos'=>$adjuntos ] );
	}

}