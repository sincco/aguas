<?php

use \Sincco\Sfphp\Response;

class FormatosController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$userData = $this->helper('UsersAccount')->getUserData('user\extra');
		$cuadrilla = $userData['cuadrilla']['cuadrilla'];
		$view = $this->newView('Gestion\FormatosTabla');
		$view->cuadrilla = $cuadrilla;
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function imprimir () {
		$contratos = $this->getParams('contratos');
		//var_dump($contratos);
		$view = $this->newView('Gestion\FormatosImpresion');
		$view->contrato = $contratos[0];
		$view->render();
	}

	public function apiAdjuntos() {
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