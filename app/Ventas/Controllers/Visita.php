<?php

use \Sincco\Sfphp\Response;

class VisitaController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function index() {
		$estatus = $this->getModel('Catalogos\Estatus')->getAll();
		array_shift($estatus);
		array_shift($estatus);
		$view = $this->newView('Ventas\Visita');
		$view->estatus = $estatus;
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function apiGetContrato() {
		$model = $this->getModel('Ventas\Ventas');
		$data = $model->getContrato($this->getParams('contrato'));
		new Response('json', ['total'=>count($data), 'rows'=>$data]);
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
		$visita = $this->getParams();
		$visita['contrato'] = $contrato;
		unset($visita['estatus']);
		if(trim($data['fechaInstalacion']) == '') {
			$data['fechaInstalacion'] = 'NULL';
		}
		$visitaId = $this->getModel('Ventas\Ventas')->setVisita($visita);
		$data = [];
		$data['contrato'] = $this->getParams('contrato');
		$data['estatusId'] = $this->getParams('estatus');
		$this->getModel('Ventas\Ventas')->setEstatus($data);
		new Response('json', ['respuesta'=>$visitaId]);
	}
}