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
		$total = 0;
		$errores = 0;
		foreach (glob(PATH_TMP . '/carga-contratos-*') as $file) {
			$data = $this->helper('ExcelParser')->read($file);
			$total += count($data);
			$modelo = $this->getModel('Aguas');
			foreach ($data as $row) {
				if (!isset($row['INTERIOR'])) {
					$row['INTERIOR'] = '';
				}
				if (!isset($row['VIA'])) {
					$row['VIA'] = '';
				}
				if (!isset($row['REGION'])) {
					$row['REGION'] = '';
				}
				if (!isset($row['COLONIA'])) {
					$row['COLONIA'] = '';
				}
				$query = "INSERT INTO `contratos`(`contrato`,  `propietario`, `usuario`,  `municipio`, `region`, `via`, `calle`, `numOficial`, `interior`, `colonia`) VALUES ('" . $row["NIS"] . "','" . $row["PROPIETARIO"] . "','" . str_replace("'", '', $row["USUARIO"]) . "','" . str_replace("'", '', $row["MUNICIPIO"]) . "','" . str_replace("'", '', $row["REGION"]) . "','" . str_replace("'", '', $row["VIA"]) . "','" . str_replace("'", '', $row["CALLE"]) . "','" . str_replace("'", '', $row["NUM_OFICIAL"]) . "','" . str_replace("'", '', $row["INTERIOR"]) . "','" . str_replace("'", '', $row["COLONIA"]) . "';";
				#var_dump($query);
				try {
					$modelo->execute($query);
				} catch (Exception $e) {
					$errores++;
				}
			}
			unlink($file);
		}
		echo "<h3>Se procesaron " . $total . " registros con " . $errores . "</h3>";
	}
}
