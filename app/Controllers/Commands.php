<?php

use \Sincco\Sfphp\XML;
use \Sincco\Sfphp\Response;
use \Sincco\Tools\Debug;
use \Sincco\Sfphp\Request;

class CommandsController extends Sincco\Sfphp\Abstracts\Controller {
	
	public function total() {
		echo "Totales";
		$model = $this->getModel('Aguas');
		$empresas = $model->run('select * from empresas;');
		foreach ($empresas as $empresa) {
			$data = $model->run("select e.descripcion Empresa , gc.contrato, con.utilizacion Clave_SIAPA, co.descripcion ,con.latitud,con.longitud
				from contratos con
				inner join gestionContratos gc on con.contrato=gc.contrato
				inner join cuadrillasContratos cc on gc.contrato=cc.contrato
				left join cuadrillas c on cc.cuadrilla=c.cuadrilla
				inner join empresas e on c.idEmpresa=e.idEmpresa
				Inner join cobros co on co.cobro=con.cobro
				where gc.estatusId = 5 and e.idEmpresa=" . $empresa['idEmpresa']);
			if (count($data) > 0) {
				fputcsv('html/download/instalaciones_efectivas' . $empresa['idEmpresa'] . '.csv', array_keys(reset($data)));
				foreach ($data as $row) {
					fputcsv('html/download/instalaciones_efectivas' . $empresa['idEmpresa'] . '.csv', $row);
				}				
			}
		}
		
	}
}