<?php

class VentasModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = "SELECT asg.contrato, con.altaContrato, con.propietario, con.suministro, con.tarifa, CONCAT(con.via, ' ', con.calle, ' ', con.numOficial) direccion, con.colonia, con.municipio, asg.fechaAsignacion, est.descripcion estatus, ven.nombre
			FROM ventasContratosAsignados asg
			INNER JOIN contratos con USING (contrato)
			INNER JOIN ventasEstatus est USING (estatusId)
			LEFT JOIN vendedores ven USING (vendedorId)
			ORDER BY asg.fechaAsignacion DESC, con.contrato ASC";
		return $this->connector->query($query);
	}

	public function setRegister($data)
	{
		$query = "INSERT INTO ventasContratosAsignados SET contrato = :contrato, vendedorId = :vendedorId, fechaAsignacion=CURDATE(), estatusId=2 ON DUPLICATE KEY UPDATE estatusId=2;";
		$this->connector->query($query, $data);
	}

	public function asignar($vendedor, $contratos) {
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = "'" . $contrato['contrato'] . "'";
		}
		$query = "UPDATE ventasContratosAsignados SET fechaAsignacion=CURDATE(), vendedorId=" . $vendedor .", estatusId=2 WHERE contrato IN (" . implode(',', $values) . ");";
		return $this->connector->query($query);
	}

	public function getContrato($contrato) {
		$query = "SELECT asg.contrato, con.altaContrato, con.propietario, con.suministro, con.tarifa, CONCAT(con.via, ' ', con.calle, ' ', con.numOficial) direccion, con.colonia, con.municipio, asg.fechaAsignacion, est.descripcion estatus, ven.nombre, vis.fechaVisita, vis.clienteNombre, vis.clienteTelefono, vis.clienteCorreo, vis.fechaInstalacion, vis.horarioInstalacion
			FROM ventasContratosAsignados asg
			INNER JOIN contratos con USING (contrato)
			INNER JOIN ventasEstatus est USING (estatusId)
			LEFT JOIN vendedores ven USING (vendedorId)
			LEFT JOIN ventasVisita vis USING (contrato)
			WHERE con.contrato='" . $contrato . "'
			ORDER BY asg.fechaAsignacion DESC, con.contrato ASC";
		return $this->connector->query($query);
	}

	public function setVisita($data) {
		$query = "INSERT INTO ventasVisita SET contrato=:contrato, fechaVisita=CURDATE(), clienteNombre=:clienteNombre, clienteTelefono=:clienteTelefono, clienteCorreo=:clienteCorreo, fechaInstalacion=:fechaInstalacion, horarioInstalacion=:horarioInstalacion;";
		return $this->connector->query($query, $data);
	}

	public function setEstatus($data) {
		$query = "UPDATE ventasContratosAsignados SET estatusId=:estatusId WHERE contrato=:contrato;";
		return $this->connector->query($query, $data);
	}

	public function getByZone($data) {
		$query = 'SELECT con.* FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp ON (con.contrato=tmp.contrato AND tmp.estatusId != 5) WHERE (longitud BETWEEN :east AND :west) AND (latitud BETWEEN :south AND :north) AND con.contrato NOT IN (SELECT contrato FROM ventasContratosAsignados);';
		return $this->connector->query($query, $data);
	}
}