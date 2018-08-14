<?php

use \Sincco\Sfphp\Response;

class VisitasController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function lectura() {
		$model = $this->getModel('Aguas');
		$model->contratosLecturas();
		$respuesta = $model->insert([
			'contrato'=>$this->getParams('contrato'),
			'fechaLectura'=>date('Y-m-d'),
			'lecturaMedidor'=>$this->getParams('lecturaMedidor'),
			'observaciones'=>$this->getParams('observaciones')
		]);
		new Response('json', ['response'=>$respuesta]);
	}
}