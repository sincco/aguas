<?php

class AvanzadaModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = "SELECT asg.contrato, con.altaContrato, con.propietario, con.suministro, con.tarifa, CONCAT(con.via, ' ', con.calle, ' ', con.numOficial) direccion, con.colonia, con.municipio, asg.fechaAsignacion, est.descripcion estatus, ven.nombre
			FROM avanzadaContratosAsignados asg
			INNER JOIN contratos con USING (contrato)
			INNER JOIN avanzadaEstatus est USING (estatusId)
			LEFT JOIN revisores ven USING (revisorId)
			ORDER BY asg.fechaAsignacion DESC, con.contrato ASC";
		return $this->connector->query($query);
	}

	public function getVendidos($data) {
		$query = 'SELECT con.*, vis.fechaInstalacion, vis.horarioInstalacion, CONCAT(con.via, " ", con.calle, " ", con.numOficial) direccion, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) estatusId, IFNULL(tmp.descripcion,"Sin Asignar") estatus, tmp.anexo, tmp.cuadrilla, DATE(tmp.fecha) fechaEstatus, ven.nombre FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato) INNER JOIN avanzadaContratosAsignados asg USING (contrato) INNER JOIN avanzadaVisita vis USING (contrato) LEFT JOIN revisores ven USING (revisorId) WHERE asg.estatusId = 3 AND vis.fechaInstalacion BETWEEN :inicio AND :fin ORDER BY vis.fechaInstalacion DESC, con.contrato ASC;';
		//var_dump($query);
		return $this->connector->query($query, $data);
	}

	public function setRegister($data)
	{
		$query = "SELECT COUNT(*) contratos FROM avanzadaContratosAsignados WHERE contrato='" . $data['contrato'] . "' AND estatusId=3;";
		$previo = $this->connector->query($query);
		if (!$previo[0]['contratos']) {
			$query = "INSERT INTO avanzadaContratosAsignados SET contrato = :contrato, revisorId = :revisorId, fechaAsignacion=CURDATE(), estatusId=2 ON DUPLICATE KEY UPDATE fechaAsignacion=CURDATE(), estatusId=2, revisorId = :revisorIdUpd;";
			$data['revisorIdUpd'] = $data['revisorId'];
			$this->connector->query($query, $data);
		} else {
			return true;
		}
	}

	public function setRegisters($data, $revisorId)
	{
		foreach ($data as $row) {
			$_data['contrato'] = $row['contrato'];
			$query = "SELECT COUNT(*) contratos FROM avanzadaContratosAsignados WHERE contrato='" . $_data['contrato'] . "' AND estatusId=3;";
			$previo = $this->connector->query($query);
			if (!$previo[0]['contratos']) {
				$_data['revisorId'] = $revisorId;
				$query = "INSERT INTO avanzadaContratosAsignados SET contrato = :contrato, revisorId = :revisorId, fechaAsignacion=CURDATE(), estatusId=2 ON DUPLICATE KEY UPDATE fechaAsignacion=CURDATE(), estatusId=2, revisorId = :revisorIdUpd;";
				$_data['revisorIdUpd'] = $_data['revisorId'];
				$this->connector->query($query, $_data);
			}
		}
	}

	public function asignar($revisor, $contratos) {
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = "'" . $contrato['contrato'] . "'";
		}
		$query = "UPDATE avanzadaContratosAsignados SET fechaAsignacion=CURDATE(), revisorId=" . $revisor .", estatusId=2 WHERE contrato IN (" . implode(',', $values) . ");";
		return $this->connector->query($query);
	}

	public function getContrato($contrato) {
		$query = "SELECT asg.contrato, con.altaContrato, con.propietario, con.suministro, con.tarifa, CONCAT(con.via, ' ', con.calle, ' ', con.numOficial) direccion, con.colonia, con.municipio, asg.fechaAsignacion, est.descripcion estatus, ven.nombre, vis.*
			FROM avanzadaContratosAsignados asg
			INNER JOIN contratos con USING (contrato)
			INNER JOIN avanzadaEstatus est USING (estatusId)
			LEFT JOIN revisores ven USING (revisorId)
			LEFT JOIN avanzadaVisita vis USING (contrato)
			WHERE con.contrato='" . $contrato . "'
			ORDER BY asg.fechaAsignacion DESC, con.contrato ASC";
		return $this->connector->query($query);
	}

	public function setVisita($data) {
		# array_shift($data);
		$query = "INSERT INTO avanzadaVisita SET contrato=:contrato, fechaVisita=CURDATE(), observaciones=:observaciones;";
		#var_dump($query,$data);
		return $this->connector->query($query, $data);
	}

	public function setEstatus($data) {
		$query = "UPDATE avanzadaContratosAsignados SET estatusId=:estatusId WHERE contrato=:contrato;";
		#var_dump($query,$data);
		return $this->connector->query($query, $data);
	}

	public function getByZone($data) {
		$query = 'SELECT con.* FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp ON (con.contrato=tmp.contrato AND tmp.estatusId != 5) WHERE (longitud BETWEEN :east AND :west) AND (latitud BETWEEN :south AND :north) AND con.contrato NOT IN (SELECT contrato FROM avanzadaContratosAsignados);';
		return $this->connector->query($query, $data);
	}
}