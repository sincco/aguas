<?php

use \Sincco\Sfphp\Response;

class IndexController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$model = $this->getModel( 'Expedientes\Contratos' );
		$view = $this->newView( 'Expedientes\ContratosTabla' );
		$view->contratos = $model->getAll();
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