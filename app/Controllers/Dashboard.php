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
		$fechaInicio = (is_null($this->getParams('fechaInicio')) ? date('d/m/Y') : $this->getParams('fechaInicio'));
		$fechaFin = (is_null($this->getParams('fechaFin')) ? date('d/m/Y') : $this->getParams('fechaFin'));

		$fechaInicio = explode('/', $fechaInicio);
		$fechaInicio = $fechaInicio[2] . '-' . $fechaInicio[1] . '-' . $fechaInicio[0];
		$fechaFin = explode('/', $fechaFin);
		$fechaFin = $fechaFin[2] . '-' . $fechaFin[1] . '-' . $fechaFin[0];
		$params = ['desde'=>$fechaInicio, 'hasta'=>$fechaFin];

		$user = unserialize($_SESSION['sincco\login\controller']);
		if (stripos($user['userName'], "adp") !== false) {
			Request::redirect('gestion/visor');
		}
		$this->helper('UsersAccount')->checkLogin();
		$cuadrilla = unserialize($_SESSION['user\extra']);
		$idEmpresa = $this->getModel('Catalogos\Cuadrillas')->getById($cuadrilla['cuadrilla']['cuadrilla']);
		if (isset($cuadrilla['cuadrilla'])) {
			$view = $this->newView('DashboardCuadrilla');
		} else {
			$view = $this->newView('Dashboard');
		}

		$cuadrilla = unserialize($_SESSION['user\empresa']);
		if (isset($cuadrilla['empresa'])) {
			$view = $this->newView('DashboardEmpresa');
			$metricas = New \Sincco\Sfphp\XML(PATH_CONFIG . '/dashboard_empresa_' . $cuadrilla['empresa']['idEmpresa'] . '.xml');
			$id = 0;
			foreach ($metricas->data as $metrica) {
				$id++;
				$graficas[] = ['id'=>$id, 'titulo'=>$metrica['titulo'], 'tipo'=>$metrica['tipo'], 'datos'=>$this->getModel('Aguas')->run($metrica['query'])];
			}
			$view->graficas = $graficas;
		}
		
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->desde = $fechaInicio;
		$view->hasta = $fechaFin;
		$view->botonavance = 'html/download/instalaciones_efectivas' . $idEmpresa[0]['idEmpresa'] . '.csv';

		$view->render();
	}
}
