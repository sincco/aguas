<?php

use \Sincco\Sfphp\Response;

class ExpedientesController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function info() {
		$model = $this->getModel('Aguas');
		new Response('json', ['data'=>$model->contratos()->where('contrato', $this->getParams('contrato'))->getData()]);
	}

	public function imagenes() {
		$model = $this->getModel('Aguas');
		new Response('json', ['data'=>$model->contratosImages()->where('contrato', $this->getParams('contrato'))->getData()]);
	}

	public function resumen() {
		$pagination = [0];
        $contratos = $this->getModel('Expedientes\Contratos');
        switch (Request::get('method')) {
            case 'GET':
                if (isset($_GET['pagination'])) {
                    $pagination = $_GET['pagination'];
                }
                $filters = [];
                if (isset($_GET['filters'])) {
                    $filters = $_GET['filters'];
                }
                $data = $contratos->getDataFiltered($filters, $pagination);
                $total = $contratos->getTotalDataFiltered($filters);
                new Response('json', ['data'=>$data, 'registros'=>$total, 'extra'=>'']);
                break;
            default:
                new Response('json', ['data'=>false, 'extra'=>'Método ' . Request::get('method') . ' no soportado']);
                break;
        }
	}
}