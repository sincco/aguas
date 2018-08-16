<?php

class EmpresasModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT * FROM empresas;';
		return $this->connector->query( $query );
	}

	public function insert($data, $table=false) {
		$query = 'INSERT INTO empresas 
			SET descripcion=:descripcion';
		return $this->connector->query($query, $data);
	}
	
}
