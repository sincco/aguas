<?php

use \Sincco\Sfphp\Response;

class VisitaController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function index() {
		$estatus = $this->getModel('Catalogos\Estatus')->getAll();
		array_shift($estatus);
		array_shift($estatus);
		$view = $this->newView('Ventas\Visita');
		$view->estatus = $estatus;
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function formatos () {
		$contratos = $this->getParams('contratos');
		$ids = "'" . implode("','", $contratos) . "'";
		$contratos = $this->getModel('Expedientes\Contratos')->getByIds($ids);
		$view = $this->newView('Ventas\Formato');
		$view->supervisor = $userData['cuadrilla']['nombre'];
		$view->contratos = $contratos;
		$view->render();
	}

	public function apiGetContrato() {
		$model = $this->getModel('Ventas\Ventas');
		$data = $model->getContrato($this->getParams('contrato'));
		new Response('json', ['total'=>count($data), 'rows'=>$data]);
	}

	public function apiGuardar() {
		$contrato = $this->getParams('contrato');
		$visita = $this->getParams();
		$visita['contrato'] = $contrato;
		unset($visita['estatus']);
		if(trim($visita['fechaInstalacion']) == '') {
			$visita['fechaInstalacion'] = '1900-01-01';
		}
		$visitaId = $this->getModel('Ventas\Ventas')->setVisita($visita);
		$data = [];
		$data['contrato'] = $this->getParams('contrato');
		$data['estatusId'] = $this->getParams('estatus');
		$this->getModel('Ventas\Ventas')->setEstatus($data);
		new Response('json', ['respuesta'=>$visitaId]);
	}
}