<?php

use \Sincco\Sfphp\Response;

class CuadrillasController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper( 'UsersAccount' )->checkLogin();
		$model = $this->getModel( 'Catalogos\Cuadrillas' );
		$view = $this->newView( 'Catalogos\CuadrillasTabla' );
		$view->cuadrillas = $model->getAll();
		$view->menus = $this->helper( 'UsersAccount' )->createMenus();
		$view->render();
	}

	public function apiAdjuntos() {
		$contrato = $this->getParams( 'contrato' );
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