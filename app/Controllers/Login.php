<?php

use \Sincco\Sfphp\Config\Reader;
use \Sincco\Sfphp\Crypt;
use \Sincco\Sfphp\Request;
use \Sincco\Tools\Login;
use \Sincco\Tools\Tokenizer;

/**
 * Control de acceso al sistema
 */
class LoginController extends Sincco\Sfphp\Abstracts\Controller {
	
	/**
	 * Acción por default
	 * @return none
	 */
	public function index() {
		if(! Login::isLogged()) {
			$view = $this->newView('Login');
			if(file_exists(PATH_ROOT.'/html/img/logo_cliente.jpg'))
				$view->logo = 'html/img/logo_cliente.jpg';
			else
				$view->logo = 'html/img/logo.jpg';
			$view->render();
		} else
			Request::redirect('dashboard');
	}

	/**
	 * Sale del sistema
	 * @return none
	 */
	public function salir() {
		$this->helper('UsersAccount')->logout();
	}

	/**
	 * Peticion de acceso
	 * @return none
	 */
	public function apiLogin() {
		$acceso = FALSE;
		$db = Reader::get('bases');
		$db = $db['default'];
		$db['password'] = trim(Crypt::decrypt($db['password']));
		Login::setDatabase($db);
		$data = $this->getParams('userData');
		$token = Tokenizer::validate($data['_token'], APP_KEY);
		if(isset($token['GENERIC_API'])) {
			if(Login::login($data)) {
				$acceso = TRUE;
			}
		}
		echo json_encode(array('acceso'=>$acceso));
	}
	
	public function encriptar() {
		echo trim(Crypt::encrypt($this->getParams('t')));
	}
}
