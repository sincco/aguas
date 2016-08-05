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
		foreach ($_FILES as $file) {
			$tmp_name = $file["tmp_name"][0];
        	$name = $file["name"][0];
        	move_uploaded_file($tmp_name, PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name);
        	chmod(PATH_ROOT . '/_expedientes/' . $contrato . '/' . $name, 0777);
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