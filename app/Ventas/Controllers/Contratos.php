<?php

use \Sincco\Sfphp\Response;

class ContratosController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function index() {
		$view = $this->newView('Ventas\Contratos');
		$view->contratos = $this->getModel('Ventas\Ventas')->getAll();
		$view->vendedores = $this->getModel('Catalogos\Vendedores')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function vendidos() {
		$view = $this->newView('Ventas\ContratosVendidos');
		$fechaInicio = (is_null($this->getParams('fechaInicio')) ? date('Y/m/d') : $this->getParams('fechaInicio'));
		$fechaFin = (is_null($this->getParams('fechaFin')) ? date('Y/m/d') : $this->getParams('fechaFin'));
		$view->desde = $fechaInicio;
		$view->hasta = $fechaFin;
		$view->contratos = $this->getModel('Ventas\Ventas')->getVendidos(['inicio'=>$fechaInicio,'fin'=>$fechaFin]);
		$view->cuadrillas = $this->getModel('Catalogos\Cuadrillas')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function zonas() {
		$view = $this->newView('Ventas\ContratosZonas');
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->vendedores = $this->getModel('Catalogos\Vendedores')->getAll();
		$view->render();
	}

	public function apiAsignar() {
		$vendedor = $this->getParams('vendedor');
		$contratos = $this->getParams('contratos');
		try {
			$this->getModel('Ventas\Ventas')->setRegister($contratos);
			$respuesta = $this->getModel('Ventas\Ventas')->asignar($vendedor, $contratos);
			new Response('json', ['respuesta'=>true]);
		} catch (Exception $e) {
			new Response('json', ['respuesta'=>false]);
		}
	}

	public function apiAsignarMasivo() {
		$vendedor = $this->getParams('vendedorId');
		$contratos = $this->getParams('contratos');
		try {
			$this->getModel('Ventas\Ventas')->setRegisters($contratos,$vendedor);
			$respuesta = $this->getModel('Ventas\Ventas')->asignar($vendedor, $contratos);
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
				if ($this->getModel('Ventas\Ventas')->setRegister($row)) {
					$count++;
				}
			}
			unlink($file);
		} 
		new Response('json', ['total'=>$total, 'insertados'=>$count]);
	}

	public function getByZone() {
		$data = $this->getModel('Ventas\Ventas')->getByZone($this->getParams('limites'));
		new Response('json', ['total'=>count($data), 'data'=>$data]);
	}
}