<?php

use \Sincco\Sfphp\Response;

class ContratosController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function index() {
		$view = $this->newView('Avanzada\Contratos');
		//$view->contratos = $this->getModel('Avanzada\Avanzada')->getAll();
		$view->revisores = $this->getModel('Catalogos\Revisores')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function vendidos() {
		$view = $this->newView('Avanzada\ContratosVendidos');
		$fechaInicio = (is_null($this->getParams('fechaInicio')) ? date('Y/m/d') : $this->getParams('fechaInicio'));
		$fechaFin = (is_null($this->getParams('fechaFin')) ? date('Y/m/d') : $this->getParams('fechaFin'));
		$view->desde = $fechaInicio;
		$view->hasta = $fechaFin;
		$view->contratos = $this->getModel('Avanzada\Avanzada')->getVendidos(['inicio'=>$fechaInicio,'fin'=>$fechaFin]);
		$view->cuadrillas = $this->getModel('Catalogos\Cuadrillas')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function zonas() {
		$view = $this->newView('Avanzada\ContratosZonas');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->revisores = $this->getModel('Catalogos\Revisores')->getAll();
		$view->render();
	}

	public function apiAsignar() {
		$revisor = $this->getParams('revisor');
		$contratos = $this->getParams('contratos');
		try {
			$this->getModel('Avanzada\Avanzada')->setRegister($contratos);
			$respuesta = $this->getModel('Avanzada\Avanzada')->asignar($revisor, $contratos);
			new Response('json', ['respuesta'=>true]);
		} catch (Exception $e) {
			new Response('json', ['respuesta'=>false]);
		}
	}

	public function apiAsignarMasivo() {
		$revisor = $this->getParams('revisorId');
		$contratos = $this->getParams('contratos');
		try {
			$this->getModel('Avanzada\Avanzada')->setRegisters($contratos,$revisor);
			$respuesta = $this->getModel('Avanzada\Avanzada')->asignar($revisor, $contratos);
			new Response('json', ['respuesta'=>true]);
		} catch (Exception $e) {
			new Response('json', ['respuesta'=>false]);
		}
	}

	public function procesarArchivo() {
		$count = 0;
		$total = 0;
		foreach (glob(PATH_TMP . '/carga-contratos-*') as $file) {
			$data = $this->helper('ExcelParser')->read($file);
			$total += count($data);
			foreach ($data as $row) {
				if ($this->getModel('Avanzada\Avanzada')->setRegister($row)) {
					$count++;
				}
			}
			unlink($file);
		} 
		new Response('json', ['total'=>$total, 'insertados'=>$count]);
	}

	public function getByZone() {
		$data = $this->getModel('Avanzada\Avanzada')->getByZone($this->getParams('limites'));
		new Response('json', ['total'=>count($data), 'data'=>$data]);
	}

	public function apiData() {
		$model = $this->getModel('Avanzada\Avanzada');
		$data = $model->getTable($_GET, 0);
		if (count($data) > 100) {
			$count = 500000; #$model->getCount($_GET);
		} else {
			$count = 500000;count($data);
		}
		new Response('json', ['total'=>$count, 'rows'=>$data]);
	}
}