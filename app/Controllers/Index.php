<?php
use \Sincco\Tools\Login;
use \Sincco\Sfphp\Request;

/**
 * Captura de petición al home
 */
class IndexController extends Sincco\Sfphp\Abstracts\Controller {
	/**
	 * Validar si el usuario está loggeado para accesar al dashboard
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
}