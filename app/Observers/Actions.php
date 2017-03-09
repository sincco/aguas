<?php
/**
 * Captura de petición al home
 */
class ActionsObserver extends Sincco\Sfphp\Abstracts\Observer {

	public function _Dashboard_Index_pre() {
		var_dump("OBSERVER DASHBOARD");
	}
}