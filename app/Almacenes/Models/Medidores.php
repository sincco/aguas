<?php

class MedidoresModel extends Sincco\Sfphp\Abstracts\Model 
{
	public function getTable($data=[])
	{
		return $this->medidores();
	}

	public function setRegister($data)
	{
			$query = "SELECT m.serie,m.diametro,m.modelo,e.descripcion,m.marca,m.fechaAsignacion FROM medidores m
			inner join empresas e on m.empresaId=e.idEmpresa WHERE serie = '" . $data['serie'] . "';";
		$previous = $this->connector->query($query);
		if (count($previous) == 0) {
			$query = "INSERT INTO medidores SET serie = :serie, diametro = :diametro, modelo = :modelo, digitosLectura = :digitosLectura, empresaId = :empresaId, marca = :marca, fechaAsignacion = :fechaAsignacion;";
			return $this->connector->query($query, $data);
		} else {
			return false;
		}
	}

}



 