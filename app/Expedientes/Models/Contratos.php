<?php

class ContratosModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT * FROM expedientes;';
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

	public function getByCuadrilla($data) {
		$query = 'SELECT asg.cuadrilla, asg.contrato
		FROM cuadrillasContratos asg
		INNER JOIN contratos con USING contrato
		WHERE asg.cuadrilla = :cuadrilla;';
		return $this->connector($query, ['cuadrilla'=>$data]);
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