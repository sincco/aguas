<?php

use \Sincco\Sfphp\Response;

class AvanzadaController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Catalogos\Avanzada');
		$view = $this->newView('Catalogos\AvanzadaTabla');
		$view->estatus = $model->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function api() {
		$estatus = $this->getParams('avanzada');
		$model = $this->getModel('Catalogos\Avanzada');
		$estatusId = $model->insert($estatus);
		new Response('json', ['respuesta'=>$estatusId]);
	}
}