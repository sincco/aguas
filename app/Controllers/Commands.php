<?php

use \Sincco\Sfphp\XML;
use \Sincco\Sfphp\Response;
use \Sincco\Tools\Debug;
use \Sincco\Sfphp\Request;

class CommandsController extends Sincco\Sfphp\Abstracts\Controller {
	
	public function totalReports() {
		$model = $this->getModel('Aguas');
		$empresas = $model->execute('select * from empresas;')
		foreach ($empresas as $empresa) {
			$data = $model->execute("select e.descripcion Empresa , gc.contrato, con.utilizacion Clave_SIAPA, co.descripcion ,con.latitud,con.longitud
				from contratos con
				inner join gestionContratos gc on con.contrato=gc.contrato
				inner join cuadrillasContratos cc on gc.contrato=cc.contrato
				left join cuadrillas c on cc.cuadrilla=c.cuadrilla
				inner join empresas e on c.idEmpresa=e.idEmpresa
				Inner join cobros co on co.cobro=con.cobro
				where gc.estatusId = 5 and e.idEmpresa=" . $empresa['idEmpresa']);
			$data = fputcsv('html/download/instalaciones_efectivas' . $empresa['idEmpresa'] . '.csv', $data);
		}
		
	}