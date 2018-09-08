<?php

class CuadrillasModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT cua.cuadrilla, cua.idEmpresa, cua.descripcion, emp.descripcion empresa FROM cuadrillas cua INNER JOIN empresas emp USING (idEmpresa);';
		return $this->connector->query( $query );
	}

	public function getById( $data ) {
		return $this->connector->query( 'SELECT cua.cuadrilla, cua.idEmpresa, cua.descripcion, emp.descripcion empresa FROM cuadrillas cua INNER JOIN empresas emp USING (idEmpresa) WHERE cua.cuadrilla=:cuadrilla;', [ 'cuadrilla'=>$data ] );
	}

	public function insert($data, $table=false) {
		$query = 'INSERT INTO cuadrillas 
			SET descripcion=:descripcion, idEmpresa=:idEmpresa';
		return $this->connector->query($query, $data);
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