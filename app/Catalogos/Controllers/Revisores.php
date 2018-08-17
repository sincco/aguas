<?php

use \Sincco\Sfphp\Response;

class RevisoresController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Catalogos\Revisores');
		$view = $this->newView('Catalogos\RevisoresTabla');
		$view->empresas = $this->getModel('Catalogos\Empresas')->getAll();
		$view->revisores = $model->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function api() {
		$revisor = $this->getParams('revisor');
		$model = $this->getModel('Catalogos\Revisores');
		$data = array('user'=>$revisor['usuario'], 'email'=>$revisor['usuario'], 'password'=>$revisor['password']);
		$userId = $this->helper('UsersAccount')->createUser($data);
		$revisor = $this->getParams('revisor');
		unset($revisor['usuario']);
		unset($revisor['password']);
		$revisor['revisorId'] = $userId;
		$revisorId = $model->insert($revisor);

		new Response('json', ['respuesta'=>$userId]);
	}
}