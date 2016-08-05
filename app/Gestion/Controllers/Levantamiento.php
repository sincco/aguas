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