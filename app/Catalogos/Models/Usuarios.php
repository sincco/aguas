<?php

class UsuariosModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT usr.userId, usr.userName, ext.nombre, cua.cuadrilla, cua.descripcion FROM __usersControl usr
		INNER JOIN usuariosExtra ext USING (userId) 
		INNER JOIN cuadrillas cua USING (cuadrilla);';
		return $this->connector->query( $query );
	}

	public function getByUserName( $data ) {
		$query = 'SELECT usr.userId, usr.userName, ext.nombre, cua.cuadrilla, cua.descripcion FROM __usersControl usr
		INNER JOIN usuariosExtra ext USING (userId) 
		INNER JOIN cuadrillas cua USING (cuadrilla)
		WHERE usr.userName = :userName;';
		return $this->connector->query( $query, ['userName'=>$data] );
	}

	public function insert( $data ) {
		$query = 'INSERT INTO usuariosExtra 
			SET userId = :userId, nombre = :nombre, cuadrilla = :cuadrilla;';
		$id = $this->connector->query( $query, [
			'userId'=>$data['userId'],
			'nombre'=>$data['nombre'],
			'cuadrilla'=>$data['cuadrilla']
			] );
		return $id;
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
		$query = 'UPDATE usuariosExtra 
			SET ' . $campos . ' WHERE ' . $condicion;
		$parametros = array_merge( $set, $where );
		return $this->connector->query( $query, $parametros );
	}

}