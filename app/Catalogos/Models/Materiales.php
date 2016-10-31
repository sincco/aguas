<?php

class MaterialesModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT * FROM materiales;';
		return $this->connector->query( $query );
	}

	public function getById( $data ) {
		return $this->connector->query( 'SELECT cot.cotizacion, cot.fecha, cot.razonSocial, cot.estatus,
			det.producto, det.descripcion, det.unidad, det.cantidad, det.precio, det.cantidad * det.precio AS subtotal
			FROM cotizaciones cot
			INNER JOIN cotizacionesDetalle det USING( cotizacion )
			WHERE cotizacion = :Cotizacion
			ORDER BY det.descripcion', [ 'Cotizacion'=>$data ] );
	}

	public function insert($data) {
		$query = 'INSERT INTO cuadrillas 
			SET descripcion=:descripcion';
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