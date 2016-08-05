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

	public function apiData() {
		$model = $this->getModel('Expedientes\Contratos');
		$count = $model->getCount();
		$count = array_pop($count);
		new Response('json', ['total'=>$count['total'], 'rows'=>$model->getTable($_GET)]);
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
		$asignados = $this->getModel('Expedientes\Contratos')->getByCuadrilla($cuadrilla);
		$count = $this->getModel('Expedientes\Contratos')->totalByCuadrilla($cuadrilla);
		$count = array_pop($count);
		new Response('json', ['total'=>$count['total'], 'rows'=>$asignados]);
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
		$count = $model->getCount();
		$count = array_pop($count);
		new Response('json', ['data'=>$model->getContratoAsignado($this->getParams('contrato'), $this->getParams('cuadrilla'))]);
	}

}