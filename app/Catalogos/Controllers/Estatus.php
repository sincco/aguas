<?php

use \Sincco\Sfphp\Response;

class EstatusController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Catalogos\Estatus');
		$view = $this->newView('Catalogos\EstatusTabla');
		$view->estatus = $model->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function api() {
		$estatus = $this->getParams('estatus');
		$model = $this->getModel('Catalogos\Estatus');
		$estatusId = $model->insert($estatus);
		new Response('json', ['respuesta'=>$estatusId]);
	}
}