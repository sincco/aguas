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
		$query = "SELECT * FROM contratos WHERE contrato = '" . $data['contrato'] . "';";
		$previous = $this->connector->query($query);
		if (count($previous) > 0) {
			$query = "INSERT INTO ventasContratosAsignados SET contrato = :contrato;";
			return $this->connector->query($query, $data);
		} else {
			return false;
		}
	}

	public function asignar($vendedor, $contratos) {
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = "'" . $contrato[1] . "'";
		}
		$query = "UPDATE ventasContratosAsignados SET fechaAsignacion=CURDATE(), vendedorId=" . $vendedor ." WHERE contrato IN (" . implode(',', $values) . ");";
		return $this->connector->query($query);
	}

	public function getContrato($contrato) {
		$query = "SELECT asg.contrato, con.altaContrato, con.propietario, con.suministro, con.tarifa, CONCAT(con.via, ' ', con.calle, ' ', con.numOficial) direccion, con.colonia, con.municipio, asg.fechaAsignacion, est.descripcion estatus, ven.nombre
			FROM ventasContratosAsignados asg
			INNER JOIN contratos con USING (contrato)
			INNER JOIN ventasEstatus est USING (estatusId)
			LEFT JOIN vendedores ven USING (vendedorId)
			WHERE con.contrato='" . $contrato . "'
			ORDER BY asg.fechaAsignacion DESC, con.contrato ASC";
		return $this->connector->query($query);
	}

	public function setVisita($data) {
		$query = "INSERT INTO ventasVisita SET contrato=:contrato, fechaVisita=CURDATE(), clienteNombre=:clienteNombre, clienteTelefono=:clienteTelefono, clienteCorreo=:clienteCorreo;";
		return $this->connector->query($query, $data);
	}

	public function setEstatus($data) {
		$query = "UPDATE ventasContratosAsignados SET estatusId=:estatusId WHERE contrato=:contrato;";
		return $this->connector->query($query, $data);
	}
}