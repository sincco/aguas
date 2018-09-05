<?php

use \Sincco\Sfphp\Config\Reader;
use \Sincco\Sfphp\Request;
use \Sincco\Sfphp\Crypt;
use \Sincco\Tools\Login;
use \Sincco\Tools\Tokenizer;

/**
 * Funciones para manejo de cuentas de usuario
 */
class UsersAccountHelper extends Sincco\Sfphp\Abstracts\Helper {

	public function editUser($data) {
		$db = Reader::get('bases');
		$db = $db[ 'default' ];
		$db[ 'password' ] = trim(Crypt::decrypt($db['password']));
		Login::setDatabase($db);
		return Login::editUser($data);
	}

	public function createUser($data) {
		$db = Reader::get('bases');
		$db = $db[ 'default' ];
		$db[ 'password' ] = trim(Crypt::decrypt($db['password']));
		Login::setDatabase($db);
		return Login::createUser($data);
	}
	/**
	 * Si un usuario no está loggeado se redirige a la pagina de inicio
	 * @return none
	 */
	public function checkLogin() {
		$db = Reader::get('bases');
		$db = $db[ 'default' ];
		$db[ 'password' ] = trim(Crypt::decrypt($db['password']));
		//Login::setDatabase($db);
		//return Login::isLogged();
		if(! $data = Login::isLogged()) {
			Request::redirect('login');
		}
	}

	/**
	 * Termina la sesion del usuario y lo envia a la pagina de login
	 * @return [type] [description]
	 */
	public function logout() {
		Login::logout();
		Request::redirect('login');
	}

	/**
	 * Crea la estructura de menus del sistema y valida si el usuario ya hizo login
	 * @return [type] [description]
	 */
	public function createMenus() {
		$this->checkLogin();
		$mdlMenus = $this->getModel('Menus');
		$menus = $mdlMenus->getAll();
		$response = array();
		foreach ($menus as $menu) {
			if(intval($menu[ 'menuParent' ]) > 0) {
				$response[ $menu[ 'menuParent' ] ][ 'childs' ][] = array(
					'text'=>$menu[ 'menuText' ],
					'url'=>$menu[ 'menuURL' ]
				);
			} else {
				$response[ $menu[ 'menuId' ] ] = array(
					'text'=>$menu[ 'menuText' ],
					'url'=>$menu[ 'menuURL' ],
					'childs'=>array()
				);
			}
		}
		if (intval($_SESSION['user\vendedor'])>0) {
			array_shift($response);
			array_shift($response);
			array_shift($response);
			array_shift($response[0]['childs']);
			array_shift($response[0]['childs']);
		}
		if (intval($_SESSION['user\revisor'])>0) {
			array_pop($response);
			array_pop($response);
			array_pop($response);
		}
		$cuadrilla = unserialize($_SESSION['user\extra']);
		if(isset($cuadrilla['cuadrilla']['cuadrilla'])) {
			array_shift($response);
			array_shift($response);
			array_shift($response);
			#array_pop($response);
			var_dump($response);
		}
		return $response;
	}

	public function getUserData($segment = 'sincco\login\controller') {
		return unserialize($_SESSION[$segment]);
	}

	public function createTokenForApi() {
		var_dump(Login::isLogged());
	}

}
