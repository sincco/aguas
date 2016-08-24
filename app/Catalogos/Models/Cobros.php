<?php

class CobrosModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT * FROM cobros;';
		return $this->connector->query( $query );
	}
	
}