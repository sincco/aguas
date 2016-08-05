<?php

class ContratosModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT * FROM contratos;';
		return $this->connector->query( $query );
	}

	public function getCount() {
		$query = 'SELECT COUNT(*) total FROM contratos;';
		return $this->connector->query( $query );
	}

	public function getTable($data) {
		if (!isset($data['sort'])) {
			$data['sort'] = 'contrato';
		}
		$query = 'SELECT con.*, IFNULL(ges.estatusId,1) estatusId, IFNULL(pro.descripcion,"Sin Asignar") estatus FROM contratos con LEFT JOIN (SELECT MAX(contrato) contrato, MAX(estatusId) estatusId, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato) ges USING (contrato) LEFT JOIN estatusProceso pro USING (estatusId) ';
		if (isset($data['search'])) {
			$where = 'WHERE con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" ';
			$query .= $where;
		}
		$query .= 'ORDER BY ' . $data['sort'] . ' ' . $data['order'] . ' LIMIT ' . $data['limit'] * 2 . ' OFFSET ' . $data['offset'];
		return $this->connector->query($query);	
	}

	public function getById($data) {
		$query = 'SELECT * FROM contratos WHERE contrato IN (' . $data . ');';
		return $this->connector->query( $query );
	}

	public function getByIds($data) {
		$query = 'SELECT * FROM contratos WHERE contrato IN (' . $data . ');';
		return $this->connector->query( $query );
	}

	public function getContratoAsignado($contrato, $cuadrilla) {
		$query = 'SELECT asg.cuadrilla, con.*
		FROM cuadrillasContratos asg
		INNER JOIN contratos con USING (contrato)
		WHERE asg.cuadrilla = :cuadrilla and asg.contrato = :contrato;';
		return $this->connector->query($query, ['contrato'=>$contrato, 'cuadrilla'=>$cuadrilla]);
	}

	public function getByCuadrilla($data) {
		$query = 'SELECT asg.cuadrilla, con.*
		FROM cuadrillasContratos asg
		INNER JOIN contratos con USING (contrato)
		WHERE asg.cuadrilla = :cuadrilla;';
		return $this->connector->query($query, ['cuadrilla'=>$data]);
	}

	public function totalByCuadrilla($data) {
		$query = 'SELECT COUNT(con.contrato) total
		FROM cuadrillasContratos asg
		INNER JOIN contratos con USING (contrato)
		WHERE asg.cuadrilla = :cuadrilla;';
		return $this->connector->query($query, ['cuadrilla'=>$data]);
	}

	public function getSinAsignar() {
		$query = 'SELECT asg.cuadrilla, asg.contrato, con.propietario, con.municipio, con.colonia
		FROM cuadrillasContratos asg
		RIGHT JOIN contratos con USING (contrato);';
		return $this->connector->query($query);
	}

	public function asignar($cuadrilla, $contratos) {
	//Borrar las asignaciones previas
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = "'" . $contrato['contrato'] . "'";
		}
		$query = 'DELETE FROM cuadrillasContratos WHERE contrato IN (' . implode(',', $values) . ');';
		$this->connector->query($query);
	//Crear estatus de asignación
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = "('" . $contrato['contrato'] . "', 2,'Asignado a cuadrilla " . $cuadrilla . "', NOW())";
		}
		$query = 'INSERT INTO gestionContratos VALUES ' . implode(',', $values) . ';';
		return $this->connector->query($query);
	//Crear nuevas asignaciones
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = '(' . $cuadrilla . ", '" . $contrato['contrato'] . "')";
		}
		$query = 'INSERT INTO cuadrillasContratos VALUES ' . implode(',', $values) . ';';
		return $this->connector->query($query);

	}

	public function update( $set, $where ) {
		$campos = [];
		$condicion = [];
		foreach ( $set as $campo => $valor )
			$campos[] = $campo . "=:" . $campo;
		foreach ( $where as $campo => $valor )
			$condicion[] = $campo . "=:" . $campo;
		$campos = implode( ",", $campos );
		$condicion = implode( " AND ", $condicion );
		$query = 'UPDATE cotizaciones 
			SET ' . $campos . ' WHERE ' . $condicion;
		$parametros = array_merge( $set, $where );
		return $this->connector->query( $query, $parametros );
	}

}