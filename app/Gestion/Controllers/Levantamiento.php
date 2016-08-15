<?php

use \Sincco\Sfphp\Response;

class LevantamientoController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$userData = $this->helper('UsersAccount')->getUserData('user\extra');
		$cuadrilla = $userData['cuadrilla']['cuadrilla'];
		$view = $this->newView('Gestion\Levantamiento');
		$view->cuadrilla = $cuadrilla;
		$view->menus = $this->helper('UsersAccount')->createMenus();
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
			foreach ($imagenes as $imagen) {
				$tmp_name = $_FILES['file']['tmp_name'][$imagen];
				$extension = explode('.', $_FILES['file']['name'][$imagen]);
				$extension = array_pop($extension);
				$name = $imagen . '.' . $extension;
				move_uploaded_file($tmp_name, PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name);
				chmod(PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name, 0777);
				$imagetobewatermark=imagecreatefrompng(PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name);
				$watermarktext="Muggu";
				$fontsize="15";
				$white = imagecolorallocate($imagetobewatermark, 255, 255, 255);
				imagettftext($imagetobewatermark, $fontsize, 0, 20, 10, $white, 'ARIAL', $watermarktext);
			}
			new Response( 'json', [ 'respuesta'=>true ] );
		} catch (Exception $e) {
			new Response( 'json', [ 'respuesta'=>false ] );
		}

/*
$imagetobewatermark=imagecreatefrompng("images/muggu.png");
$watermarktext="Muggu";
$font="../font/century gothic.ttf";
$fontsize="15";
$white = imagecolorallocate($imagetobewatermark, 255, 255, 255);
*/
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