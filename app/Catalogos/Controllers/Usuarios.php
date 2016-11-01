<?php

use \Sincco\Sfphp\Response;

class UsuariosController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Catalogos\Usuarios');
		$view = $this->newView('Catalogos\UsuariosTabla');
		$view->cuadrillas = $this->getModel('Catalogos\Cuadrillas')->getAll();
		$view->usuarios = $model->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function apiJefes() {
		$user = $this->getParams('userData');
		$model = $this->getModel('Catalogos\Usuarios');
		$previous = $model->getByUserName($user['userName']);
		if (count($previous)) {
			$userId = $previous[0]['userId'];
			$user['userEmail'] = $user['userName'];
			$user['userStatus'] = 'A';
			$data = array('user'=>$user['userName'], 'email'=>$user['userName'], 'password'=>$user['userPassword']);
			if($this->helper('UsersAccount')->editUser($data)){
				$model->update(['nombre'=>$user['nombre'], 'cuadrilla'=>$user['cuadrilla']], ['userId'=>$userId]);

			}
		} else {
			$user['userEmail'] = $user['userName'];
			$user['userStatus'] = 'A';
			$data = array('user'=>$user['userName'], 'email'=>$user['userName'], 'password'=>$user['userPassword']);
			$userId = $this->helper('UsersAccount')->createUser($data);
			if ($userId){
				$user['userId'] = $userId;
				$model->insert($user);
			}
		}
		new Response('json', [ 'respuesta'=>$userId ] );
	}

}