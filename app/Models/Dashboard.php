<?php

class DashboardModel extends Sincco\Sfphp\Abstracts\Model {

	public function run($query, $params = []) {
		return $this->connector->query( $query, $params );
	}

	public function completadosCuadrillas($params = []) {
		$query = "SELECT cuadrilla, SUM(EnProceso) EnProceso, SUM(NoEjecutado) NoEjecutado, SUM(Terminado) Terminado, SUM(CorregidoSupervision) CorregidoSupervision FROM ( 
		SELECT cua.cuadrilla, ges.estatusId, CASE WHEN ges.estatusId = 3 THEN COUNT(con.contrato) ELSE 0 END EnProceso, CASE WHEN ges.estatusId = 4 THEN COUNT(con.contrato) ELSE 0 END NoEjecutado, CASE WHEN ges.estatusId = 5 THEN COUNT(con.contrato) ELSE 0 END Terminado, CASE WHEN ges.estatusId = 6 THEN COUNT(con.contrato) ELSE 0 END CorregidoSupervision FROM contratos con INNER JOIN cuadrillasContratos cua USING (contrato) INNER JOIN (SELECT contrato, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato) ope USING (contrato) INNER JOIN gestionContratos ges ON (ges.contrato = ope.contrato AND ges.fecha = ope.fecha) INNER JOIN estatusProceso est USING(estatusId) WHERE DATE(ges.fecha) BETWEEN :desde AND :hasta GROUP BY cua.cuadrilla, ges.estatusId
		) tmp GROUP BY cuadrilla;";
		return $this->connector->query($query,$params);
	}

	public function gestiones($params = []) {
		$query = "SELECT est.descripcion, COUNT(con.contrato) contratos FROM contratos con INNER JOIN cuadrillasContratos cua USING (contrato) INNER JOIN (SELECT contrato, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato) ope USING (contrato) INNER JOIN gestionContratos ges ON (ges.contrato = ope.contrato AND ges.fecha = ope.fecha) INNER JOIN estatusProceso est USING(estatusId) WHERE DATE(ges.fecha) BETWEEN :desde AND :hasta GROUP BY ges.estatusId;";
		return $this->connector->query($query,$params);
	}

}