<?php

class ContratosModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT * FROM contratos;';
		return $this->connector->query( $query );
	}

	public function getCount($data, $cuadrilla = 0) {
		$modCuadrilla = '';
		$query = 'SELECT COUNT(*) total FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato) LEFT JOIN cobros cob USING(cobro) ';
		if (!isset($data['search'])) {
			$data['search']='';
		}
		if (trim($data['search']) != '') {
			$where = 'WHERE con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.colonia like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" OR IFNULL(tmp.descripcion,"Sin Asignar") like "%' . $data['search'] . '%" ';
			$query .= $where;
			if ($cuadrilla > 0) {
				$query .= ' AND (tmp.cuadrilla = ' . $cuadrilla . ') ';
			}
		} else {
			if ($cuadrilla > 0) {
				$query .= ' WHERE tmp.cuadrilla = ' . $cuadrilla . ' ';
			}
		}
		return $this->connector->query($query);
	}

	public function getTable($data, $cuadrilla = 0) {
		$modCuadrilla = '';
		if (!isset($data['sort'])) {
			$data['sort'] = 'contrato';
		}
		$query = 'SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) estatusId, IFNULL(tmp.descripcion,"Sin Asignar") estatus, tmp.anexo, tmp.cuadrilla, DATE(tmp.fecha) fechaEstatus, cob.descripcion cobro, cob.precio, (SELECT COUNT(id) FROM gestionContratos WHERE estatusId=4 and contrato=con.contrato) visitas FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato) LEFT JOIN cobros cob USING(cobro) ';
		if (!isset($data['search'])) {
			$data['search']='';
		}
		if (trim($data['search']) != '') {
			$where = 'WHERE con.contrato = ' . $data['search'] . ' OR IFNULL(tmp.descripcion,"Sin Asignar") like "%' . $data['search'] . '%" ';
			$query .= $where;
			if ($cuadrilla > 0) {
				$query .= ' AND (tmp.cuadrilla = ' . $cuadrilla . ') ';
			}
		} else {
			if ($cuadrilla > 0) {
				$query .= ' WHERE tmp.cuadrilla = ' . $cuadrilla . ' ';
			}
		}
		if (isset($data['limit'])) {
			$query .= 'ORDER BY ' . $data['sort'] . ' ' . $data['order'] . ' LIMIT ' . $data['limit'] . ' OFFSET ' . $data['offset'];
		}
		# var_dump($query);die();
		return $this->connector->query($query);
	}

	public function getTableFormatos($data, $cuadrilla = 0) {
		$modCuadrilla = '';
		if (!isset($data['sort'])) {
			$data['sort'] = 'contrato';
		}
		$query = "
			SELECT 
			    con.*,
			    IFNULL(tmp.descripcion, 'Sin Asignar') estatus,
			    tmp.cuadrilla
			FROM
			    contratos con
			LEFT JOIN (
			    SELECT 
			        ges.contrato,
			            ges.fecha,
			            ges.estatusId,
			            pro.descripcion,
			            ges.anexo,
			            IFNULL(cua.cuadrilla, 'S/A') cuadrilla
			    FROM
			        gestionContratos ges
			    INNER JOIN (
			        SELECT 
			            ges.contrato, MAX(ges.id) id
			        FROM
			            gestionContratos ges
			        GROUP BY ges.contrato
			    ) tmp ON (ges.contrato = tmp.contrato AND ges.id = tmp.id)
			    INNER JOIN estatusProceso pro USING (estatusId)
			    LEFT JOIN cuadrillasContratos cua ON (cua.contrato = tmp.contrato)
			) tmp USING (contrato)
			LEFT JOIN
			    cobros cob USING (cobro) ";
		if (!isset($data['search'])) {
			$data['search']='';
		}
		if (trim($data['search']) != '') {
			$where = 'WHERE con.contrato = ' . $data['search'] . ' ';
			$query .= $where;
			if ($cuadrilla > 0) {
				$query .= ' AND (tmp.cuadrilla = ' . $cuadrilla . ') ';
			}
		} else {
			if ($cuadrilla > 0) {
				$query .= ' WHERE tmp.cuadrilla = ' . $cuadrilla . ' ';
			}
		}
		if (isset($data['limit'])) {
			$query .= 'ORDER BY ' . $data['sort'] . ' ' . $data['order'] . ' LIMIT ' . $data['limit'] . ' OFFSET ' . $data['offset'];
		}
		# var_dump($query);die();
		return $this->connector->query($query);
	}

	public function getCountUrgentes($data, $cuadrilla = 0) {
		$modCuadrilla = '';
		$query = 'SELECT COUNT(*) total FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.fecha) fecha FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.fecha=tmp.fecha) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato) WHERE con.bandera=3 AND IFNULL(tmp.estatusId,1) NOT IN (5) ';
		if (!isset($data['search'])) {
			$data['search']='';
		}
		if (trim($data['search']) != '') {
			$where = 'AND (con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.colonia like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" OR IFNULL(tmp.descripcion,"Sin Asignar") like "%' . $data['search'] . '%") ';
			$query .= $where;
			if ($cuadrilla > 0) {
				$query .= ' AND (cua.cuadrilla = ' . $cuadrilla . ') ';
			}
		} else {
			if ($cuadrilla > 0) {
				$query .= ' WHERE cua.cuadrilla = ' . $cuadrilla . ' ';
			}
		}
		return $this->connector->query($query);
	}

	public function getTableUrgentes($data, $cuadrilla = 0) {
		$modCuadrilla = '';
		if (!isset($data['sort'])) {
			$data['sort'] = 'contrato';
		}
		$query = 'SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) estatusId, IFNULL(tmp.descripcion,"Sin Asignar") estatus, tmp.anexo, tmp.cuadrilla FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.fecha) fecha FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.fecha=tmp.fecha) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato) WHERE con.bandera=3 AND IFNULL(tmp.estatusId,1) NOT IN (5) ';
		if (!isset($data['search'])) {
			$data['search']='';
		}
		if (trim($data['search']) != '') {
			$where = 'AND (con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.colonia like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" OR IFNULL(tmp.descripcion,"Sin Asignar") like "%' . $data['search'] . '%") ';
			$query .= $where;
			if ($cuadrilla > 0) {
				$query .= ' AND (cua.cuadrilla = ' . $cuadrilla . ') ';
			}
		} else {
			if ($cuadrilla > 0) {
				$query .= ' WHERE cua.cuadrilla = ' . $cuadrilla . ' ';
			}
		}
		if (isset($data['limit'])) {
			$query .= 'ORDER BY ' . $data['sort'] . ' ' . $data['order'] . ' LIMIT ' . $data['limit'] * 2 . ' OFFSET ' . $data['offset'];
		}
		return $this->connector->query($query);
	}


	public function getCountTerminados($data) {
		$query = 'SELECT COUNT(*) total FROM contratos con LEFT JOIN (SELECT MAX(contrato) contrato, MAX(estatusId) estatusId, MAX(fecha) fecha FROM gestionContratos  GROUP BY contrato) ges USING (contrato) INNER JOIN estatusProceso pro ON (ges.estatusId = pro.estatusId AND pro.estatusId IN (5,6)) LEFT JOIN cuadrillasContratos cua USING(contrato) LEFT JOIN cobros cob USING(cobro) ';
		if (isset($data['search'])) {
			$where = 'WHERE con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.colonia like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" OR tmp.descripcion like "%' . $data['search'] . '%" ';
			$query .= $where;
		}
		return $this->connector->query($query);
	}

	public function getTableTerminados($data) {
		if (!isset($data['sort'])) {
			$data['sort'] = 'contrato';
		}
		$query = 'SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, IFNULL(ges.estatusId,1) estatusId, IFNULL(pro.descripcion,"Sin Asignar") estatus, DATE_FORMAT(ges.fecha,"%Y-%m-%d") fecha, IFNULL(cua.cuadrilla," ") cuadrilla, cob.descripcion cobro, cob.precio FROM contratos con LEFT JOIN (SELECT MAX(contrato) contrato, MAX(estatusId) estatusId, MAX(fecha) fecha FROM gestionContratos  GROUP BY contrato) ges USING (contrato) INNER JOIN estatusProceso pro ON (ges.estatusId = pro.estatusId AND pro.estatusId IN (5,6)) LEFT JOIN cuadrillasContratos cua USING(contrato) LEFT JOIN cobros cob USING(cobro) ';
		if (isset($data['search'])) {
			$where = 'WHERE con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.colonia like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" ';
			$query .= $where;
		}
		if (isset($data['limit'])) {
			$query .= 'ORDER BY ' . $data['sort'] . ' ' . $data['order'] . ' LIMIT ' . $data['limit'] * 2 . ' OFFSET ' . $data['offset'];
		}
		return $this->connector->query($query);
	}

	public function getCountNoEjecutados($data) {
		$query = 'SELECT count(*) total
			FROM contratos con 
			LEFT JOIN gestionContratos ges ON (con.contrato = ges.contrato AND ges.estatusId IN (4)) 
			INNER JOIN estatusProceso pro ON (ges.estatusId = pro.estatusId AND pro.estatusId IN (4)) 
			LEFT JOIN cuadrillasContratos cua ON(con.contrato = cua.contrato) 
			LEFT JOIN cobros cob USING(cobro) ';
		if (isset($data['search'])) {
			$where = 'WHERE con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.colonia like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" ';
			$query .= $where;
		}
		return $this->connector->query($query);
	}

	public function getTableNoEjecutados($data) {
		if (!isset($data['sort'])) {
			$data['sort'] = 'contrato';
		}
		$query = 'SELECT con.*, IFNULL(ges.estatusId,1) estatusId, IFNULL(pro.descripcion,"Sin Asignar") estatus, DATE_FORMAT(ges.fecha,"%Y-%m-%d") fecha, IFNULL(cua.cuadrilla," ") cuadrilla, cob.descripcion cobro, cob.precio, ges.anexo 
			FROM contratos con 
			LEFT JOIN gestionContratos ges ON (con.contrato = ges.contrato AND ges.estatusId IN (4)) 
			INNER JOIN estatusProceso pro ON (ges.estatusId = pro.estatusId AND pro.estatusId IN (4)) 
			LEFT JOIN cuadrillasContratos cua ON(con.contrato = cua.contrato) 
			LEFT JOIN cobros cob USING(cobro) ';
		if (isset($data['search'])) {
			$where = 'WHERE con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.colonia like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%" ';
			$query .= $where;
		}
		if (isset($data['limit'])) {
			$query .= 'ORDER BY ' . $data['sort'] . ' ' . $data['order'] . ' LIMIT ' . $data['limit'] * 2 . ' OFFSET ' . $data['offset'];
		}
		return $this->connector->query($query);
	}

	public function getTableProcesados($data) {
		$query = 'SELECT con.*, ges.estatusId, ges.fecha, ges.anexo, pro.descripcion estatus FROM contratos con INNER JOIN gestionContratos ges ON con.contrato = ges.contrato INNER JOIN ( SELECT contrato, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato ) est ON ges.contrato = est.contrato AND ges.fecha = est.fecha INNER JOIN estatusProceso pro USING(estatusId) ';
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

	public function getReporteTerminados($data) {
		$query = 'SELECT con.*, IFNULL(ges.estatus,"Sin Asignar") estatus FROM contratos con INNER JOIN (SELECT contrato, GROUP_CONCAT( CONCAT( fecha,  " :: ", descripcion ) ORDER BY fecha SEPARATOR  "|" ) estatus FROM gestionContratos INNER JOIN estatusProceso pro USING ( estatusId ) GROUP BY contrato) ges USING (contrato) WHERE con.contrato IN (' . $data . ');';
		return $this->connector->query( $query );
	}

	public function getReporteNoEjecutados($data) {
		$query = 'SELECT con.*, IFNULL(ges.estatusId,1) estatusId, IFNULL(pro.descripcion,"Sin Asignar") estatus, DATE_FORMAT(ges.fecha,"%Y-%m-%d") fecha, IFNULL(cua.cuadrilla," ") cuadrilla, cob.descripcion cobro, cob.precio, ges.anexo 
			FROM contratos con 
			LEFT JOIN gestionContratos ges ON (con.contrato = ges.contrato AND ges.estatusId IN (4)) 
			INNER JOIN estatusProceso pro ON (ges.estatusId = pro.estatusId AND pro.estatusId IN (4)) 
			LEFT JOIN cuadrillasContratos cua ON(con.contrato = cua.contrato) 
			LEFT JOIN cobros cob USING(cobro)  WHERE con.contrato IN (' . $data . ');';
		return $this->connector->query( $query );
	}


	public function getContratoAsignado($contrato, $cuadrilla) {
		$query = 'SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) estatusId, IFNULL(tmp.descripcion,"Sin Asignar") estatus, tmp.anexo, tmp.cuadrilla, DATE(tmp.fecha) fechaEstatus, cob.descripcion cobro, cob.precio FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato) LEFT JOIN cobros cob USING(cobro) WHERE tmp.contrato=:contrato AND tmp.cuadrilla=:cuadrilla;';
		return $this->connector->query($query, ['contrato'=>$contrato, 'cuadrilla'=>$cuadrilla]);
	}

	public function getContratoHistorial($contrato) {
		$query = 'SELECT ges.fecha, ges.estatusId, pro.descripcion, ges.anexo FROM gestionContratos ges INNER JOIN estatusProceso pro USING (estatusId) WHERE contrato = :contrato ORDER BY ges.fecha DESC;';
		return $this->connector->query($query, ['contrato'=>$contrato]);
	}

	public function getByCuadrilla($cuadrilla, $data) {
		if (!isset($data['sort'])) {
			$data['sort'] = 'contrato';
		}
		$query = 'SELECT con.*, IFNULL(ges.estatusId,1) estatusId, IFNULL(pro.descripcion,"Sin Asignar") estatus, IFNULL(cua.cuadrilla," ") cuadrilla FROM contratos con LEFT JOIN (SELECT MAX(contrato) contrato, MAX(estatusId) estatusId, MAX(fecha) fecha FROM gestionContratos GROUP BY contrato) ges USING (contrato) LEFT JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua USING(contrato) WHERE cua.cuadrilla = ' . $cuadrilla . ' ';
		if (isset($data['search'])) {
			$where = 'AND (con.contrato like "%' . $data['search'] . '%" OR con.propietario like "%' . $data['search'] . '%" OR con.usuario like "%' . $data['search'] . '%" OR con.municipio like "%' . $data['search'] . '%" OR con.suministro like "%' . $data['search'] . '%" OR con.contrato like "%' . $data['search'] . '%" OR con.calle like "%' . $data['search'] . '%") ';
			$query .= $where;
		}
		$query .= 'ORDER BY ' . $data['sort'] . ' ' . $data['order'] . ' LIMIT ' . $data['limit'] * 2 . ' OFFSET ' . $data['offset'];
		return $this->connector->query($query);
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
		$query = 'SELECT * FROM (SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) statusId, IFNULL(tmp.descripcion,"Sin Asignar") status, tmp.anexo, tmp.cuadrilla equipoInstalador, DATE(tmp.fecha) fechaEstatus FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato)) tmp ';
		$condicion = [];
		if (count($where)) {
			foreach ( $where as $campo => $valor ) {
				$condicion[] = $campo . '=:' . $campo;
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
		$query = 'SELECT COUNT(*) FROM (SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) statusId, IFNULL(tmp.descripcion,"Sin Asignar") status, tmp.anexo, tmp.cuadrilla equipoInstalador, DATE(tmp.fecha) fechaEstatus FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato)) tmp';
		$condicion = [];
		if (count($where)) {
			foreach ( $where as $campo => $valor ) {
				$condicion[] = $campo . '=:' . $campo;
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
		$query = 'INSERT INTO gestionContratos (contrato,estatusId,anexo,fecha) VALUES ' . implode(',', $values) . ';';
		$this->connector->query($query);
	//Crear nuevas asignaciones
		$values = [];
		foreach ($contratos as $contrato) {
			$values[] = '(' . $cuadrilla . ", '" . $contrato['contrato'] . "')";
		}
		$query = 'INSERT INTO cuadrillasContratos VALUES ' . implode(',', $values) . ';';
		return $this->connector->query($query);
	}

	public function setEstatus($contrato, $estatus, $motivo = '') {
		$query = 'INSERT INTO gestionContratos (contrato,estatusId,anexo,fecha) VALUES (:contrato, :estatusId, :motivo, NOW());';
		return $this->connector->query($query, ['contrato'=>$contrato, 'estatusId'=>$estatus, 'motivo'=>$motivo]);
	}

	public function setEstatusFecha($contrato, $estatus, $fecha, $motivo = '') {
		$query = 'INSERT INTO gestionContratos (contrato,estatusId,anexo,fecha) VALUES (:contrato, :estatusId, :motivo, :fecha);';
		return $this->connector->query($query, ['contrato'=>$contrato, 'estatusId'=>$estatus, 'motivo'=>$motivo, 'fecha'=>$fecha]);
	}

	public function setGPS($contrato, $longitud, $latitud) {
		$query = 'UPDATE contratos SET longitud = :longitud, latitud = :latitud WHERE contrato = :contrato;';
		return $this->connector->query($query, ['contrato'=>$contrato, 'longitud'=>$longitud, 'latitud'=>$latitud]);
	}

	public function setCampo($contrato, $campo, $valor) {
		$query = 'UPDATE contratos SET ' . $campo . ' = :' . $campo . ' WHERE contrato = :contrato;';
		return $this->connector->query($query, ['contrato'=>$contrato, $campo=>$valor]);
	}

	public function setMateriales($contrato, $materiales) {
		$values = [];
		foreach ($materiales as $key => $value) {
			$values[] = "('" . $contrato . "','" . $value . "',NOW())";
		}
		$query = 'INSERT INTO contratosMateriales VALUES ' . implode(',', $values) . ';';
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

	public function getByZone($status, $limites) {
		//ini_set('memory_limit', '1536M');
		$limites['status']  = $status;
		$query = 'SELECT * FROM (SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) statusId, IFNULL(tmp.descripcion,"Sin Asignar") status, tmp.anexo, tmp.cuadrilla equipoInstalador, DATE(tmp.fecha) fechaEstatus FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato)) tmp WHERE (longitud BETWEEN :east AND :west) AND (latitud BETWEEN :south AND :north) AND status=:status;';
		return $this->connector->query($query, $limites);
	}

	public function getDataBy($where) {
		$query = 'SELECT * FROM (SELECT con.*, CONCAT(con.longitud,",",con.latitud) gps, tmp.fecha, IFNULL(tmp.estatusId,1) statusId, IFNULL(tmp.descripcion,"Sin Asignar") status, tmp.anexo, tmp.cuadrilla equipoInstalador, DATE(tmp.fecha) fechaEstatus FROM contratos con LEFT JOIN (SELECT ges.contrato, ges.fecha, ges.estatusId, pro.descripcion, ges.anexo, IFNULL(cua.cuadrilla,"S/A") cuadrilla  FROM gestionContratos ges INNER JOIN (SELECT ges.contrato, MAX(ges.id) id FROM gestionContratos ges GROUP BY ges.contrato) tmp ON (ges.contrato=tmp.contrato AND ges.id=tmp.id) INNER JOIN estatusProceso pro USING (estatusId) LEFT JOIN cuadrillasContratos cua ON (cua.contrato=tmp.contrato)) tmp USING (contrato)) tmp ';
		$condicion = [];
		if (count($where)) {
			foreach ( $where as $campo => $valor ) {
				$condicion[] = $campo . '=:' . $campo;
			}
		}
		$condicion = implode( ' AND ', $condicion );
		if (trim($condicion) != '') {
			$query .= ' WHERE ' . $condicion;
		}
		return $this->connector->query($query, $where);
	}
}

