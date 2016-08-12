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
		$query = 'SELECT con.*, IFNULL(ges.estatusId,1) estatusId, IFNULL(pro.descripcion,"Sin Asignar") estatus, IFNULL(cua.cuadrilla," ") cuadrilla FROM contratos con LEFT JOIN (SELECT MAX(contrato) contrato, MAX(estatusId) estatusId, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato) ges USING (contrato) LEFT JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua USING(contrato) ';
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
		$query = 'SELECT asg.cuadrilla, con.*, IFNULL(ges.estatusId,0) estatusId
		FROM cuadrillasContratos asg
		INNER JOIN contratos con USING (contrato)
		LEFT JOIN (SELECT MAX(contrato) contrato, MAX(estatusId) estatusId, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato) ges USING (contrato)
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

	public function getHistorico($contrato) {
		$query = 'SELECT ges.estatusId, ges.anexo, ges.fecha, pro.descripcion estatus FROM gestionContratos INNER JOIN estatusProceso pro USING (estatusId) WHERE contrato = :contrato ORDER BY ges.fecha;';
		return $this->connector->query($query, ['contrato'=>$contrato]);
	}

	public function getDataFiltered($where, $pagination = [0]) {
		$query = 'SELECT con.*, IFNULL(ges.estatus,"Sin Asignar") estatus FROM contratos con LEFT JOIN (SELECT contrato, GROUP_CONCAT( CONCAT( fecha,  " :: ", descripcion ) ORDER BY fecha SEPARATOR  "|" ) estatus FROM gestionContratos INNER JOIN estatusProceso pro USING ( estatusId ) GROUP BY contrato) ges USING (contrato) ';
		$condicion = [];
		if (count($where)) {
			foreach ( $where as $campo => $valor ) {
				if ( $campo == 'contrato' ) {
					$condicion[] = 'con.' . $campo . '=:' . $campo;
				} else {
					$condicion[] = $campo . '=:' . $campo;
				}
			}
		}
		$condicion = implode( ' AND ', $condicion );
		if (trim($condicion) != '') {
			$query .= ' WHERE ' . $condicion;
		}
		$query .= ' LIMIT 1000 OFFSET ' . $pagination[0];
		return $this->connector->query($query, $where);
	}

	public function getTotalDataFiltered($where) {
		$query = 'SELECT COUNT(con.contrato) total FROM contratos con LEFT JOIN (SELECT MAX(contrato) contrato, MAX(estatusId) estatusId, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato) ges USING (contrato) LEFT JOIN estatusProceso pro USING (estatusId) ';
		$condicion = [];
		if (count($where)) {
			foreach ( $where as $campo => $valor ) {
				if ( $campo == 'contrato' ) {
					$condicion[] = 'con.' . $campo . '=:' . $campo;
				} else {
					$condicion[] = $campo . '=:' . $campo;
				}
			}
		}
		$condicion = implode( ' AND ', $condicion );
		if(trim($condicion) != '') {
			$query .= ' WHERE ' . $condicion;
		}
		return $this->connector->query($query, $where);
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
		$this->connector->query($query);
	//Crear nuevas asignaciones
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = '(' . $cuadrilla . ", '" . $contrato['contrato'] . "')";
		}
		$query = 'INSERT INTO cuadrillasContratos VALUES ' . implode(',', $values) . ';';
		return $this->connector->query($query);
	}

	public function setEstatus($contrato, $estatus) {
		$query = 'INSERT INTO gestionContratos VALUES (:contrato, :estatusId, "", NOW());';
		return $this->connector->query($query, ['contrato'=>$contrato, 'estatusId'=>$estatus]);
	}

	public function setGPS($contrato, $longitud, $latitud) {
		$query = 'UPDATE contratos SET longitud = :longitud, latitud = :latitud WHERE contrato = :contrato;';
		return $this->connector->query($query, ['contrato'=>$contrato, 'longitud'=>$longitud, 'latitud'=>$latitud]);
	}

	public function setCampo($contrato, $campo, $valor) {
		$query = 'UPDATE contratos SET ' . $campo . ' = :' . $campo . ' WHERE contrato = :contrato;';
		return $this->connector->query($query, ['contrato'=>$contrato, $campo=>$valor]);
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