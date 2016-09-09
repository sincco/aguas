<?php

use \Sincco\Sfphp\Response;

class IndexController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function index() {
		$model = $this->getModel('Expedientes\Contratos');
		$view = $this->newView('Expedientes\ContratosTabla');
		$view->cuadrillas = $this->getModel('Catalogos\Cuadrillas')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function terminados() {
		$model = $this->getModel('Expedientes\Contratos');
		$view = $this->newView('Expedientes\TerminadosTabla');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function procesados() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTableProcesados($_GET);
		$view = $this->newView('Expedientes\ProcesadosTabla');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->contratos = $data;
		$view->render();
	}

	public function _resize() {
		$directories = scandir(PATH_ROOT . '/_expedientes/' . $this->getParams('contrato'));
		array_shift($directories);
		array_shift($directories);
		$adjuntos = [];
		foreach ($directories as $dir) {
			$files = scandir(PATH_ROOT . '/_expedientes/' .str_replace(' ', '%20', $dir));
			array_shift($files);
			array_shift($files);
			foreach ($files as $file) {
				$fileOld = PATH_ROOT . '/_expedientes/' . $dir . '/' . $file;
				$fileNew = PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/' . $file;
				if (!is_dir(PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/')) {
					mkdir(PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/');
					chmod(PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/', 0777);
				}
				if (!file_exists($fileNew)) {
					$this->helper('Images')->resize($fileOld, $fileNew);
				}
			}
		}
	}

	public function apiData() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTable($_GET);
		$count = $model->getCount($_GET);
		new Response('json', ['total'=>$count[0]['total'], 'rows'=>$data]);
	}

	public function apiDataTerminados() {
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTableTerminados($_GET);
		$count = $model->getCountTerminados($_GET);
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

	public function apiAsignados() {
		$cuadrilla = $this->getParams('cuadrilla');
		$model = $this->getModel('Expedientes\Contratos');
		$data = $model->getTable($_GET, $cuadrilla);
		$count = $model->getCount($_GET, $cuadrilla);
		new Response('json', ['total'=>$count[0]['total'], 'rows'=>$data]);
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
		$count = $model->getCount($_GET, $this->getParams('cuadrilla'));
		$count = array_pop($count);
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

	public function apiGPS() {
		$model = $this->getModel('Expedientes\Contratos');
		$respuesta = $model->setGPS($this->getParams('contrato'), $this->getParams('longitud'), $this->getParams('latitud'));
		new Response('json', ['respuesta'=>$respuesta]);
	}

	public function apiCampo() {
		$model = $this->getModel('Expedientes\Contratos');
		$respuesta = $model->setCampo($this->getParams('contrato'), $this->getParams('campo'), $this->getParams('valor'));
		new Response('json', ['respuesta'=>$respuesta]);
	}

	public function apiMateriales() {
		$model = $this->getModel('Expedientes\Contratos');
		$respuesta = $model->setMateriales($this->getParams('contrato'), $this->getParams('materiales'));
		new Response('json', ['respuesta'=>$respuesta]);
	}

}