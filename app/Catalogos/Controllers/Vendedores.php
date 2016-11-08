<?php

use \Sincco\Sfphp\Response;

class VendedoresController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Catalogos\Vendedores');
		$view = $this->newView('Catalogos\VendedoresTabla');
		$view->vendedores = $model->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function api() {
		$vendedor = $this->getParams('vendedor');
		$model = $this->getModel('Catalogos\Vendedores');
		$data = array('user'=>$vendedor['usuario'], 'email'=>$vendedor['usuario'], 'password'=>$vendedor['password']);
		$userId = $this->helper('UsersAccount')->createUser($data);
		$vendedor = $this->getParams('vendedor');
		unset($vendedor['usuario']);
		unset($vendedor['password']);
		$vendedor['vendedorId'] = $userId;
		$vendedorId = $model->insert($vendedor);

		new Response('json', ['respuesta'=>$userId]);
	}
}