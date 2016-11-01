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

	public function zonas() {
		$view = $this->newView('Ventas\ContratosZonas');
		//$view->contratos = $this->getModel('Ventas\Ventas')->getAll();
		//$view->vendedores = $this->getModel('Catalogos\Vendedores')->getAll();
		//$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function apiAsignar() {
		$vendedor = $this->getParams('vendedor');
		$contratos = $this->getParams('contratos');
		try {
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
}