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
		unset($vendedor['usuario']);
		unset($vendedor['password']);
		$vendedorId = $model->insert($vendedor);
		$vendedor = $this->getParams('vendedor');

		$data = array('user'=>$vendedor['usuario'], 'email'=>$vendedor['usuario'], 'password'=>$vendedor['password']);
		$userId = $this->helper('UsersAccount')->createUser($data);
		
		if ($userId && $vendedorId){
			new Response('json', ['respuesta'=>$vendedorId]);
		}
	}
}