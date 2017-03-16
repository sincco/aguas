<?php

use \Sincco\Sfphp\Response;
use \Sincco\Sfphp\Request;


class ExpedientesController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function info() {
		$model = $this->getModel('Aguas');
		new Response('json', ['data'=>$model->contratos()->where('contrato', $this->getParams('contrato'))->getData()]);
	}

	public function imagenes() {
		$model = $this->getModel('Aguas');
		$fotos = $model->contratosImages()->where('contrato', $this->getParams('contrato'))->getData();
		if (count($fotos) > 0) {
			new Response('json', ['data'=>$fotos]);
		} else {
			$fotos = [];
			$files = scandir(PATH_IMG . $this->getParams('contrato'), SCANDIR_SORT_ASCENDING);
			foreach ($files as $file) {
				if (strlen(trim($file)) > 2 && stripos($file, "MIN") !==false ) {
					$fotos[] = ['fecha'=>date ("Y-m-d", filemtime(PATH_IMG . $this->getParams('contrato') . '/' . $file)), 'foto'=>$file, 'nombre'=>ucwords(str_replace('-', ' ', str_replace('.jpg', '', str_replace('.png', '', substr($file,1)))))];
				}
			}
			new Response('json', ['data'=>$fotos]);
		}
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