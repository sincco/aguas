<?php

use \Sincco\Sfphp\Request;
use \Sincco\Sfphp\Response;

/**
 * Autorizaciones con AuthKey de Google
 */
class AuthqrController extends Sincco\Sfphp\Abstracts\Controller {

	private $llave = 'GEAKO2IJW4PKLBXF';

	public function index() {
		$this->helper( 'UsersAccount' )->checkLogin();
		$view = $this->newView( 'AuthQR' );
		$view->menus = $this->helper( 'UsersAccount' )->createMenus();
		$view->render();
	}

	public function apiValidar() {
		$codigo = $this->getParams( 'codigo' );
		$respuesta['valido'] = $this->helper( 'Google2Step' )->validaCodigo($this->llave, $codigo);
		new Response( 'json', $respuesta );
	}

	public function apiObtenerCodigo() {
		$tiempo = floor(time() / 30);
		$codigo = [];
		$codigo['codigo'] = $this->helper( 'Google2Step' )->generaCodigo( $this->llave, $tiempo);
		new Response( 'json', $codigo );
	}

	public function config() {
		$this->helper( 'UsersAccount' )->checkLogin();
		if( $_SESSION[ 'extraPerfil' ] == "1" ) {
			$tiempo = floor(time() / 30);
			$view = $this->newView( 'AuthQRConfig' );
			$view->menus = $this->helper( 'UsersAccount' )->createMenus();
			$view->qrUrl = $this->helper( 'Google2Step' )->urlQR( 'sae', 'suhner.com', $this->llave );
			$view->codigo = $this->helper( 'Google2Step' )->generaCodigo( $this->llave, $tiempo);
			$view->render();
		} else {
			$view = $this->newView( 'Prohibido' );
			$view->menus = $this->helper( 'UsersAccount' )->createMenus();
			$view->render();
		}
	}

}