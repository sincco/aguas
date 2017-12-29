<?php
/**
 * Captura de petición al home
 */
class ImportarController extends Sincco\Sfphp\Abstracts\Controller {
	/**
	 * Validar si el usuario está loggeado para accesar al dashboard
	 * @return none
	 */
	public function index() {
		$view = $this->newView( 'CargaContratos' );
		$view->render();
	}

	public function procesar() {
		foreach (glob(PATH_TMP . '/carga-contratos-*') as $file) {
			$data = $this->helper('ExcelParser')->read($file);
			$total += count($data);
			foreach ($data as $row) {
				var_dump($row);
			}
			unlink($file);
		}
	}
}
