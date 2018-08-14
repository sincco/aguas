<?php
use \Sincco\Sfphp\Response;

/**
 * Control de reloj
 */
class AntenasController extends Sincco\Sfphp\Abstracts\Controller {
	/**
	 * Validar si el usuario está loggeado para accesar al dashboard
	 * @return none
	 */
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$view = $this->newView('Almacenes\AntenasTabla');
		$view->antenas = $this->getModel('Almacenes\Antenas')->getTable()->getData();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function procesarArchivo() {
		$count = 0;
		$total = 0;
		foreach (glob(PATH_TMP . '/carga-antenas-*') as $file) {
			$data = $this->helper('ExcelParser')->read($file);
			$total += count($data);
			foreach ($data as $row) {
				if ($this->getModel('Almacenes\Antenas')->setRegister($row)) {
					$count++;
				}
			}
			unlink($file);
		} 
		new Response('json', ['total'=>$total, 'insertados'=>$count]);
	}

}