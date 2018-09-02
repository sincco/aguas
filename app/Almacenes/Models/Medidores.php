<?php

class MedidoresModel extends Sincco\Sfphp\Abstracts\Model 
{
	public function getTable($data=[])
	{
		return $this->medidores();
	}

	public function setRegister($data)
	{
		$query = "SELECT * FROM medidores WHERE serie = '" . $data['serie'] . "';";
		$previous = $this->connector->query($query);
		if (count($previous) == 0) {
			$query = "INSERT INTO medidores SET serie = :serie, diametro = :diametro, modelo = :modelo, digitosLectura = :digitosLectura, empresaId = :empresaId, marca = :marca, fechaAsignacion = :fechaAsignacion;";
			return $this->connector->query($query, $data);
		} else {
			return false;
		}
	}

}



 