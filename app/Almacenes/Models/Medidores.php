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
			$query = "INSERT INTO medidores SET serie = :serie, qMax = :qMax, qN = :qN, qT = :qT, qMin = :qMin, diametro = :diametro, modelo = :modelo, digitosLectura = :digitosLectura, banca = :banca, usuario = :usuario, fechaFabricacion = :fechaFabricacion;";
			return $this->connector->query($query, $data);
		} else {
			return false;
		}
	}

}



 