<?php

use \Sincco\Sfphp\XML;
use \Sincco\Sfphp\Response;
use \Sincco\Tools\Debug;
use \Sincco\Sfphp\Request;
/**
 * Dashboard del sistema
 */
class DashboardController extends Sincco\Sfphp\Abstracts\Controller {
	
	/**
	 * Acción por default
	 * @return none
	 */
	public function index() {
		$user = unserialize($_SESSION['sincco\login\controller']);
		var_dump(stripos("region", $user['userName']));
		if (stripos("region", $user['userName'])) {
			Request::redirect('gestion/visor');
		}
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

		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin, 'cuadrilla'=>1];
		$view->gestionesCuadrilla1 = $this->getModel('Dashboard')->gestionesCuadrilla($params);
		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin, 'cuadrilla'=>2];
		$view->gestionesCuadrilla2 = $this->getModel('Dashboard')->gestionesCuadrilla($params);
		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin, 'cuadrilla'=>3];
		$view->gestionesCuadrilla3 = $this->getModel('Dashboard')->gestionesCuadrilla($params);
		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin, 'cuadrilla'=>4];
		$view->gestionesCuadrilla4 = $this->getModel('Dashboard')->gestionesCuadrilla($params);
		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin, 'cuadrilla'=>8];
		$view->gestionesCuadrilla8 = $this->getModel('Dashboard')->gestionesCuadrilla($params);
		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin, 'cuadrilla'=>9];
		$view->gestionesCuadrilla9 = $this->getModel('Dashboard')->gestionesCuadrilla($params);
		
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}
}
