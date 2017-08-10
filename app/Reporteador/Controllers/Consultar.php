<?php

use \Sincco\Sfphp\XML;
use \Sincco\Sfphp\Request;
use \Sincco\Sfphp\Response;
/**
 * Dashboard del sistema
 */
class ConsultarController extends Sincco\Sfphp\Abstracts\Controller {
	
	public function index() {
		$user = unserialize($_SESSION['sincco\login\controller']);
		if (stripos($user['userName'], "adp") !== false) {
			Request::redirect('gestion/visor');
		}
		$this->helper('UsersAccount')->checkLogin();
		$view = $this->newView('ConsultarReporte');
		$view->menus = $this->helper('UsersAccount')->createMenus();

		$xml = new XML('etc/config/reportes.xml');
		$reportes = [];
		foreach ($xml->data as $key => $reporte) {
			$reporte['llave'] = $key;
			$reportes[] = $reporte;
		}
		$view->reportes = $reportes;
		$view->render();
	}

	public function download() {
		switch (Request::get('method')) {
			case 'POST':
				$model = $this->getModel('Aguas');
				$reporte = $this->getParams('reporte');
				$xml = new XML('etc/config/reportes.xml');
				$dataReporte = $xml->data;
				$dataReporte = $dataReporte[$reporte];

				ini_set('memory_limit','2000M');
				$data = $model->run($dataReporte['query']);
				var_dump(array_keys($data[0])); die();
				$data = $this->arrayToCsv($data);

				$file = PATH_TMP . '/' . strtolower(str_replace(' ', '-', $dataReporte['titulo'])) . '.csv';
				#file_put_contents($file, $data);

				header('HTTP/1.1 200 OK');
				header('Cache-Control: no-cache, must-revalidate');
				header("Pragma: no-cache");
				header("Expires: 0");
				header("Content-type: text/csv");
				header("Content-Disposition: attachment; filename=".strtolower(str_replace(' ', '-', $dataReporte['titulo'])) . '.csv');
				echo $data;
				break;
			default:
			new Response('json', ['data'=>false, 'extra'=>'Método ' . Request::get('method') . ' no soportado']);
			break;
		}
	}

	private function arrayToCsv( array &$fields, $delimiter = ',', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
		$delimiter_esc = preg_quote($delimiter, '/');
		$enclosure_esc = preg_quote($enclosure, '/');

		$data = [];
		foreach ($fields as $row) {
			$output = [];
			foreach ($row as $field ) {
				// Enclose fields containing $delimiter, $enclosure or whitespace
				if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
					$output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
				}
				else {
					$output[] = $field;
				}
			}
			$_row= implode( $delimiter, $output );
			$data[] = $_row;
		}
		return implode(PHP_EOL, $data);
	}
}
