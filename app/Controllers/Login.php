<?php

use \Sincco\Sfphp\Config\Reader;
use \Sincco\Sfphp\Request;
use \Sincco\Sfphp\Crypt;
use \Sincco\Tools\Login;

/**
 * Control de acceso al sistema
 */
class LoginController extends Sincco\Sfphp\Abstracts\Controller {
	
	/**
	 * Acción por default
	 * @return none
	 */
	public function index() {
		if(! Login::isLogged() ) {
			$view = $this->newView( 'Login' );
			if( file_exists( PATH_ROOT.'/html/img/logo_cliente.jpg' ) )
				$view->logo = 'html/img/logo_cliente.jpg';
			else
				$view->logo = 'html/img/logo.jpg';
			$view->render();
		} else
			Request::redirect( 'dashboard' );
	}

	/**
	 * Sale del sistema
	 * @return none
	 */
	public function salir() {
		$this->helper( 'UsersAccount' )->logout();
	}

	/**
	 * Peticion de acceso
	 * @return none
	 */
	public function apiLogin() {
		$db = Reader::get( 'bases' );
		$db = $db['default'];
		$db['password'] = trim( Crypt::decrypt( $db['password'] ) );
		Login::setDatabase($db);
		if( Login::login( Request::getParams( 'userData' ) ) )
			$acceso = TRUE;
		else
			$acceso = FALSE;
		echo json_encode( array( 'acceso'=>$acceso ) );
	}

	/**
	 * Crea una nueva cuenta de usuario
	 * @return none
	 */
	public function apiRegisterUser() {
		$db = Reader::get( 'bases' );
		$db = $db['default'];
		$db['password'] = trim( Crypt::decrypt( $db['password'] ) );
		Login::setDatabase( $db );
		var_dump( Login::createUser( array( 'user'=>'ivan', 'email'=>'ivan', 'password'=>'eco123' ) ) );
	}
}