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
				$query = "INSERT INTO contratos (contrato, altaContrato, propietario, usuario, suministro, numTomas, giro, utilizacion, tarifa, servicios, nivelTarifario, asociacion, cveCatastral, municipio, colonia, via, calle, numOficial, longitud, latitud, serieMedidor, telemetriaMedidor, interior, interior2, marca_act, num_apa, aparato, tip_medicion, f_inst, anos_disp_med, mayores_5_anos, f_retiro_disp_med, cobro, mod_med_ins, tipo_material, caudal_max, num_dig_medidor, f_fabricacion, diametro_toma, lectura_inicial, region, estrato, bandera, USUARIO_S, ESTADO_ORDEN, CANTIDAD_MEDIDORES, NUM_OS, CON_PAGO, DIAMETRO_CONEXION, TEL_PROP, CEL_PROP, TEL_TP, CEL_TP, COMENT_OS) VALUES ('" . $row["NIS"] . "','" . $row["ALTACONTRATO"] . "','" . str_replace("'", '', $row["PROPIETARIO"]) . "','" . str_replace("'", '', $row["USUARIO"]) . "','" . str_replace("'", '', $row["SUMINISTRO"]) . "','" . str_replace("'", '', $row["NUMTOMAS"]) . "','" . str_replace("'", '', $row["GIRO"]) . "','" . str_replace("'", '', $row["UTILIZACION"]) . "','" . str_replace("'", '', $row["TARIFA"]) . "','" . str_replace("'", '', $row["SERVICIOS"]) . "','" . str_replace("'", '', $row["NIVELTARIFARIO"]) . "','" . str_replace("'", '', $row["ASOCIACION"]) . "','" . str_replace("'", '', $row["CVECATASTRAL"]) . "','" . str_replace("'", '', $row["MUNICIPIO"]) . "','" . str_replace("'", '', $row["COLONIA"]) . "','" . str_replace("'", '', $row["VIA"]) . "','" . str_replace("'", '', $row["CALLE"]) . "','" . str_replace("'", '', $row["NUMOFICIAL"]) . "','','','','','" . str_replace("'", '', $row["INTERIOR_1"]) . "','" . str_replace("'", '', $row["INTERIOR_2"]) . "','" . str_replace("'", '', $row["MARCA_ACT"]) . "','" . str_replace("'", '', $row["NUM_APA"]) . "','" . str_replace("'", '', $row["APARATO"]) . "','" . str_replace("'", '', $row["TIP_MEDICION"]) . "','" . str_replace("'", '', $row["F_INST"]) . "','" . str_replace("'", '', $row["ANOS_DISP_MED"]) . "','" . str_replace("'", '', $row["MAYORES_5_ANOS"]) . "','" . str_replace("'", '', $row["F_RETIRO_DISP_MED"]) . "','','','','',0,NULL,'',0,'" . str_replace("'", '', $row["REGION"]) . "','" . str_replace("'", '', $row["ESTRATO"]) . "','" . str_replace("'", '', $row["BANDERA"]) . "','" . $row['USUARIO_S'] . "','" . $row['ESTADO_ORDEN'] . "','" . $row['CANTIDAD_MEDIDORES'] . "','" . $row['NUM_OS'] . "','" . $row['CON_PAGO'] . "','" . $row['DIAMETRO_CONEXION'] . "','" . $row['TEL_PROP'] . "','" . $row['CEL_PROP'] . "','" . $row['TEL_TP'] . "','" . $row['CEL_TP'] . "','" . str_replace('"', '', str_replace("'", '', $row['COMENT_OS'])) . "') ON DUPLICATE KEY UPDATE interior2='" . str_replace("'", '', $row["INTERIOR_2"]) . "',region='" . str_replace("'", '', $row["REGION"]) . "',estrato='" . str_replace("'", '', $row["ESTRATO"]) . "',bandera=" . str_replace("'", '', $row["BANDERA"]) . ", USUARIO_S='" . $row['USUARIO_S'] . "', ESTADO_ORDEN='" . $row['ESTADO_ORDEN'] . "',CANTIDAD_MEDIDORES='" . $row['CANTIDAD_MEDIDORES'] . "',NUM_OS='" . $row['NUM_OS'] . "',CON_PAGO='" . $row['CON_PAGO'] . "',DIAMETRO_CONEXION='" . $row['DIAMETRO_CONEXION'] . "',TEL_PROP='" . $row['TEL_PROP'] . "',CEL_PROP='" . $row['CEL_PROP'] . "',TEL_TP='" . $row['TEL_TP'] . "',CEL_TP='" . $row['CEL_TP'] . "',COMENT_OS='" . str_replace('"', '', str_replace("'", '', $row['COMENT_OS'])) . "';";
				var_dump($query);
			}
			unlink($file);
		}
	}
}
