<?php

use \Sincco\Sfphp\XML;
use \Sincco\Sfphp\Response;
use \Sincco\Tools\Debug;

/**
 * Dashboard del sistema
 */
class DashboardController extends Sincco\Sfphp\Abstracts\Controller {
	
	/**
	 * Acción por default
	 * @return none
	 */
	public function index() {
		$this->helper( 'UsersAccount' )->checkLogin();

		$view = $this->newView( 'Dashboard' );
		$view->menus = $this->helper( 'UsersAccount' )->createMenus();
		$view->render();
	}

}