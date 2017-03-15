<?php

class AguasModel extends Sincco\Sfphp\Abstracts\Model {
	public function execute($query) {
		return $this->connector->direct($query);
	}
}