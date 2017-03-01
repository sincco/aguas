<?php

class CuadrillasModel extends Sincco\Sfphp\Abstracts\Model {

	public function getAll() {
		$query = 'SELECT * FROM cuadrillas;';
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

	public function insert( $data ) {
		$query = 'INSERT INTO cotizaciones 
			SET fecha=NOW(), cliente=:Cliente, 
			razonSocial=:RazonSocial, email=:Email, 
			estatus=:Estatus,userId=:UserId';
		$id = $this->connector->query( $query, [
			'Cliente'=>$data[ 'cliente' ],
			'RazonSocial'=>$data[ 'razonSocial' ],
			'Email'=>$data[ 'email' ],
			'Estatus'=>$data[ 'estatus' ],
			'UserId'=>$data[ 'vendedor' ]
			] );
		if( $id ) {
			foreach ($data[ 'productos' ] as $producto) {
				if( trim( $producto[ 0 ] ) == '' )
					continue;
				$query = 'INSERT INTO cotizacionesDetalle 
				SET cotizacion=:Cotizacion, producto=:Producto, 
				descripcion=:Descripcion, unidad=:Unidad,
				cantidad=:Cantidad, precio=:Precio';
				$detalle = $this->connector->query( $query, [
					'Cotizacion'=>$id,
					'Producto'=>$producto[ 0 ],
					'Descripcion'=>$producto[ 1 ],
					'Unidad'=>$producto[ 2 ],
					'Cantidad'=>$producto[ 3 ],
					'Precio'=>$producto[ 4 ]
					]);
			}
			return $id;
		}
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