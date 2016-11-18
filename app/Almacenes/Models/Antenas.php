<?php

class AntenasModel extends Sincco\Sfphp\Abstracts\Model 
{
	public function getTable($data=[])
	{
		return $this->antenas();
	}

	public function setRegister($data)
	{
		$query = "SELECT * FROM antenas WHERE serie = '" . $data['serie'] . "';";
		$previous = $this->connector->query($query);
		if (count($previous) == 0) {
			$query = "INSERT INTO antenas SET serie = :serie, fechaRecepcion = :fechaRecepcion;";
			return $this->connector->query($query, $data);
		} else {
			return false;
		}
	}

}



 