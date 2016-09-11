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
		$this->helper('UsersAccount')->checkLogin();
		$view = $this->newView('Dashboard');

		$fechaInicio = (is_null($this->getParams('fechaInicio')) ? date('d/m/Y') : $this->getParams('fechaInicio'));
		$fechaFin = (is_null($this->getParams('fechaFin')) ? date('d/m/Y') : $this->getParams('fechaFin'));
		$view->desde = $fechaInicio;
		$view->hasta = $fechaFin;

		$fechaInicio = explode('/', $fechaInicio);
		$fechaInicio = $fechaInicio[2] . '-' . $fechaInicio[1] . '-' . $fechaInicio[0];
		$fechaFin = explode('/', $fechaFin);
		$fechaFin = $fechaFin[2] . '-' . $fechaFin[1] . '-' . $fechaFin[0];
		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin];

		$view->completados = $this->getModel('Dashboard')->completadosCuadrillas($params);
		$view->gestiones = $this->getModel('Dashboard')->gestiones($params);
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}
}