<?php

use \Sincco\Sfphp\Response;

class IndexController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Expedientes\Contratos');
		$view = $this->newView('Expedientes\ContratosTabla');
		$view->cuadrillas = $this->getModel('Catalogos\Cuadrillas')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function urgentes() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Expedientes\Contratos');
		$view = $this->newView('Expedientes\ContratosUrgentesTabla');
		$view->cuadrillas = $this->getModel('Catalogos\Cuadrillas')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function noejecutados() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Expedientes\Contratos');
		$view = $this->newView('Expedientes\NoEjecutadosTabla');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function terminados() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Expedientes\Contratos');
		$view = $this->newView('Expedientes\TerminadosTabla');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function procesados() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTableProcesados($_GET);
		$view = $this->newView('Expedientes\ProcesadosTabla');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->contratos = $data;
		$view->render();
	}

	public function mapa() {
		$this->helper('UsersAccount')->checkLogin();
		$view = $this->newView('Expedientes\BusquedaMapa');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();	
	}

#----------------
#----------- API
#----------------
	public function apiData() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTable($_GET, 0);
		if (count($data) > 98) {
			$count = 500000; #$model->getCount($_GET);
		} else {
			$count = count($data);
		}
		new Response('json', ['total'=>$count, 'rows'=>$data]);
	}

	public function apiDataUrgentes() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTableUrgentes($_GET);
		$count = $model->getCountUrgentes($_GET);
		new Response('json', ['total'=>$count[0]['total'], 'rows'=>$data]);
	}

	public function apiDataTerminados() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTableTerminados($_GET);
		$count = $model->getCountTerminados($_GET);
		new Response('json', ['total'=>$count[0]['total'], 'rows'=>$data]);
	}

	public function apiDataRevisados() {
                $model = $this->getModel('Expedientes\Contratos');
                $data = $model->getTableRevisados($_GET);
                $count = $model->getCountRevisados($_GET);
                new Response('json', ['total'=>$count[0]['total'], 'rows'=>$data]);
        }


	public function apiDataNoEjecutados() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTableNoEjecutados($_GET);
		$count = $model->getCountNoEjecutados($_GET);
		new Response('json', ['total'=>$count[0]['total'], 'rows'=>$data]);
	}

	public function apiGetContratoTerminado() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTableTerminados(['search'=>$this->getParams('contrato')]);
		$count = $model->getCountTerminados(['search'=>$this->getParams('contrato')]);
		new Response('json', ['total'=>$count[0]['total'], 'rows'=>$data]);
	}

	public function apiAdjuntos() {
		$contrato = $this->getParams('contrato');
		$files = scandir(PATH_ROOT . '/_expedientes/' . $contrato);
		array_shift($files);
		array_shift($files);
		$adjuntos = array();
		foreach ($files as $adjunto) {
			array_push($adjuntos, str_replace(' ', '%20', $adjunto));
		}
		new Response('json', [ 'respuesta'=>count($adjuntos), 'adjuntos'=>$adjuntos ]);
	}

	public function apiAdjuntosVentas() {
		$contrato = $this->getParams('contrato');
		$files = glob(PATH_ROOT . '/_expedientes/' . $contrato . '/venta*');
		$adjuntos = array();
		foreach ($files as $adjunto) {
			array_push($adjuntos, str_replace(PATH_ROOT . '/_expedientes/' . $contrato . '/', '', str_replace(' ', '%20', $adjunto)));
		}
		new Response('json', [ 'respuesta'=>count($adjuntos), 'adjuntos'=>$adjuntos ]);
	}

	public function apiAdjuntosAvanzada() {
		$contrato = $this->getParams('contrato');
		$files = glob(PATH_ROOT . '/_expedientes/' . $contrato . '/vanzada*');
		$adjuntos = array();
		foreach ($files as $adjunto) {
			array_push($adjuntos, str_replace(PATH_ROOT . '/_expedientes/' . $contrato . '/', '', str_replace(' ', '%20', $adjunto)));
		}
		new Response('json', [ 'respuesta'=>count($adjuntos), 'adjuntos'=>$adjuntos ]);
	}

	public function apiAsignados() {
		$cuadrilla = $this->getParams('cuadrilla');
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTable($_GET, $cuadrilla);
		$count = $model->getTable([], $cuadrilla);
		$count = count($count);
		new Response('json', ['total'=>$count, 'rows'=>$data]);
	}

	public function apiAsignar() {
		$cuadrilla = $this->getParams('cuadrilla');
		$contratos = $this->getParams('contratos');
		try {
			$respuesta = $this->getModel('Expedientes\Contratos')->asignar($cuadrilla, $contratos);
			new Response('json', ['respuesta'=>true]);
		} catch (Exception $e) {
			new Response('json', ['respuesta'=>false]);
		}
	}

	public function apiGetContratoAsignado() {
		$model = $this->getModel('Expedientes\Contratos');
		//$count = $model->getCount($_GET, $this->getParams('cuadrilla'));
		//$count = array_pop($count);
		new Response('json', ['data'=>$model->getContratoAsignado($this->getParams('contrato'), $this->getParams('cuadrilla'))]);
	}

	public function apiGetContratoHistorial() {
		$model = $this->getModel('Expedientes\Contratos');
		$count = $model->getCount($_GET);
		$count = array_pop($count);
		new Response('json', ['data'=>$model->getContratoHistorial($this->getParams('contrato'))]);
	}

	public function apiSetEstatus() {
		$model = $this->getModel('Expedientes\Contratos');
		$respuesta = $model->setEstatus($this->getParams('contrato'), $this->getParams('estatus'), $this->getParams('motivo'));
		new Response('json', ['respuesta'=>$respuesta]);	
	}

	public function apiSetEstatusFecha() {
		$model = $this->getModel('Expedientes\Contratos');
		$respuesta = $model->setEstatusFecha($this->getParams('contrato'), $this->getParams('estatus'), $this->getParams('fecha'), $this->getParams('motivo'));
		new Response('json', ['respuesta'=>$respuesta]);	
	}

	public function apiGPS() {
		$model = $this->getModel('Expedientes\Contratos');
		$respuesta = $model->setGPS($this->getParams('contrato'), $this->getParams('longitud'), $this->getParams('latitud'));
		new Response('json', ['respuesta'=>$respuesta]);
	}

	public function apiCampo() {
		$cuadrillaUsuario = unserialize($_SESSION['user\extra']);
		$model = $this->getModel('Expedientes\Contratos');
		if ($this->getParams('campo') == 'telemetriaMedidor') {
			$medidores = $this->getModel('Almacenes\Medidores')->getTable()->where('serie', $this->getParams('valor'))->getData();
			$medidores = array_pop($medidores);
			$cuadrilla = $this->getModel('Catalogos\Cuadrillas')->getById($cuadrillaUsuario['cuadrilla']['cuadrilla']);
			$cuadrilla = array_pop($cuadrilla);
			if ($cuadrilla['idEmpresa'] == $medidores['empresaId']) {
				$respuesta = $model->setCampo($this->getParams('contrato'), $this->getParams('campo'), $this->getParams('valor'));
				new Response('json', ['respuesta'=>true]);
			} else {
				new Response('json', ['respuesta'=>false]);
			}
		} else {
			$respuesta = $model->setCampo($this->getParams('contrato'), $this->getParams('campo'), $this->getParams('valor'));
			new Response('json', ['respuesta'=>true]);		}
	}

	public function apiMateriales() {
		$model = $this->getModel('Expedientes\Contratos');
		$respuesta = $model->setMateriales($this->getParams('contrato'), $this->getParams('materiales'));
		new Response('json', ['respuesta'=>$respuesta]);
	}

	public function apiGetByZone() {
		$data = $this->getModel('Expedientes\Contratos')->getByZone($this->getParams('status'), $this->getParams('limites'));
		new Response('json', ['total'=>count($data), 'data'=>$data]);
	}

}
