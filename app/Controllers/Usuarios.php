<?php

use \Sincco\Sfphp\Request;

/**
 * Control de acceso al sistema
 */
class UsuariosController extends Sincco\Sfphp\Abstracts\Controller {

	public function index () {
		$mdlUsuarios = $this->getModel( 'Usuarios' );
		$data = $mdlUsuarios->getAll();
		$view = $this->newView( 'Catalogo\UsuariosTabla' );
		$view->usuarios = $data;
		$view->menus = $this->helper( 'UsersAccount' )->createMenus();
		$view->render();
	}

	public function editar () {
		$mdlUsuarios = $this->getModel( 'Usuarios' );
		$data = $mdlUsuarios->loadByUserName( $this->getParams( 'userName' ) );
		$view = $this->newView( 'UsuariosAlta' );
		$view->usuarios = $data;
		$view->action = "upd";
		$view->menus = $this->helper( 'UsersAccount' )->createMenus();
		$view->render();
	}

	public function agregar() {
		$view = $this->newView( 'UsuariosAlta' );
		$view->action = "ins";
		$view->menus = $this->helper( 'UsersAccount' )->createMenus();
		$view->render();
	}

	public function apiAgregar() {
		$user = $this->getParams( 'userData' );
		if( $user[ 'action' ] == 'ins' ) {
			if( $userId = $this->helper( 'UsersAccount' )->createUser( $user ) ) {
				$mdlUsuarios = $this->getModel( 'Usuarios' );
				$mdlUsuarios->usuarioAEmpresas( $userId );
			}
			echo json_encode( [ 'respuesta'=>$userId ] );
		}
		else 
			echo json_encode( array( 'respuesta'=>$this->helper( 'UsersAccount' )->editUser( $user ) ) );
	}

}