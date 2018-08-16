<?php

use \Sincco\Sfphp\Response;

class CuadrillasController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Catalogos\Cuadrillas');
		$view = $this->newView('Catalogos\CuadrillasTabla');
		$view->empresas = $this->getModel('Catalogos\Empresas')->getAll();
		$view->cuadrillas = $model->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function api() {
		$cuadrilla = $this->getParams('cuadrilla');
		$model = $this->getModel('Catalogos\Cuadrillas');
		$cuadrillaId = $model->insert($cuadrilla);
		new Response('json', ['respuesta'=>$cuadrillaId]);
	}
}