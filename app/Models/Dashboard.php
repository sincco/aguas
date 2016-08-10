<?php

class DashboardModel extends Sincco\Sfphp\Abstracts\Model {

	public function run( $query, $params = [] ) {
		return $this->connector->query( $query, $params );
	}

}