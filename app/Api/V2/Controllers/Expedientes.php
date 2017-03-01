<?php

use \Sincco\Sfphp\Response;

class ExpedientesController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function info() {
		$model = $this->getModel('Aguas');
		new Response('json', ['data'=>$model->contratos()->where('contrato', $this->getParams('contrato'))->getData()]);
	}
}