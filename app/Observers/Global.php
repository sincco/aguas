<?php
/**
 * Captura de petición al home
 */
class GlobalObserver extends Sincco\Sfphp\Abstracts\Observer {

	public function ResolveUrl_pre() {
		/*$segments = $this->getRequest()->get('segments');
		if (substr(strtoupper($segments['action']),0,3) == 'API') {
			// var_dump('Validar token para api');
		} else {
			switch ($segments['controller']) {
				case '': case 'Login':
					break;
				default:
				if (!$this->helper('UsersAccount')->checkLogin()) {
					$this->getRequest()->setRedirect('login');
				} else {
					
				}
					break;
			}
		}*/
	}
}