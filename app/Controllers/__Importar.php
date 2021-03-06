<?php
/**
 * Captura de petición al home
 */
class ImportarController extends Sincco\Sfphp\Abstracts\Controller {
	/**
	 * Validar si el usuario está loggeado para accesar al dashboard
	 * @return none
	 */
	public function contratos() {
		$conn = oci_connect('ITRON_US','itron_us','187.217.120.218/SGCPPRO');
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		} else {
			$modelo = $this->getModel('Aguas');
			$stid = oci_parse($conn, "SELECT NIS, to_char(ALTACONTRATO, 'yyyy-mm-dd') ALTACONTRATO, PROPIETARIO, USUARIO, SUMINISTRO, NUMTOMAS, GIRO, UTILIZACION, TARIFA, SERVICIOS, NIVELTARIFARIO, ASOCIACION, CVECATASTRAL, MUNICIPIO, COLONIA, VIA, CALLE, NUMOFICIAL, INTERIOR_1, INTERIOR_2, MARCA_ACT, NUM_APA, APARATO, TIP_MEDICION, to_char(F_INST, 'yyyy-mm-dd') F_INST, ANOS_DISP_MED, MAYORES_5_ANOS, to_char(F_RETIRO_DISP_MED, 'yyyy-mm-dd') F_RETIRO_DISP_MED, REGION, ESTRATO, BANDERA, LONGITUD, LATITUD, USUARIO_S,ESTADO_ORDEN,CANTIDAD_MEDIDORES,NUM_OS,CON_PAGO,DIAMETRO_CONEXION,TEL_PROP,CEL_PROP,TEL_TP,CEL_TP,COMENT_OS FROM PROVEXTERN.PADRON_COLONIAS_MEDIDORES ORDER BY NIS ASC");
			oci_execute($stid);
			while ($padron = oci_fetch_assoc($stid)) {
				if (trim($padron["F_RETIRO_DISP_MED"]) == '') {
					$padron["F_RETIRO_DISP_MED"] = '1900-01-01';
				}
				if (trim($padron["F_INST"]) == '') {
					$padron["F_INST"] = '1900-01-01';
				}
				echo "<br>------Procesando " . $padron["NIS"] . "<br>\n";
				//var_dump($padron);
				$query = "INSERT INTO contratos (contrato, altaContrato, propietario, usuario, suministro, numTomas, giro, utilizacion, tarifa, servicios, nivelTarifario, asociacion, cveCatastral, municipio, colonia, via, calle, numOficial, longitud, latitud, serieMedidor, telemetriaMedidor, interior, interior2, marca_act, num_apa, aparato, tip_medicion, f_inst, anos_disp_med, mayores_5_anos, f_retiro_disp_med, cobro, mod_med_ins, tipo_material, caudal_max, num_dig_medidor, f_fabricacion, diametro_toma, lectura_inicial, region, estrato, bandera, USUARIO_S, ESTADO_ORDEN, CANTIDAD_MEDIDORES, NUM_OS, CON_PAGO, DIAMETRO_CONEXION, TEL_PROP, CEL_PROP, TEL_TP, CEL_TP, COMENT_OS) VALUES ('" . $padron["NIS"] . "','" . $padron["ALTACONTRATO"] . "','" . str_replace("'", '', $padron["PROPIETARIO"]) . "','" . str_replace("'", '', $padron["USUARIO"]) . "','" . str_replace("'", '', $padron["SUMINISTRO"]) . "','" . str_replace("'", '', $padron["NUMTOMAS"]) . "','" . str_replace("'", '', $padron["GIRO"]) . "','" . str_replace("'", '', $padron["UTILIZACION"]) . "','" . str_replace("'", '', $padron["TARIFA"]) . "','" . str_replace("'", '', $padron["SERVICIOS"]) . "','" . str_replace("'", '', $padron["NIVELTARIFARIO"]) . "','" . str_replace("'", '', $padron["ASOCIACION"]) . "','" . str_replace("'", '', $padron["CVECATASTRAL"]) . "','" . str_replace("'", '', $padron["MUNICIPIO"]) . "','" . str_replace("'", '', $padron["COLONIA"]) . "','" . str_replace("'", '', $padron["VIA"]) . "','" . str_replace("'", '', $padron["CALLE"]) . "','" . str_replace("'", '', $padron["NUMOFICIAL"]) . "','','','','','" . str_replace("'", '', $padron["INTERIOR_1"]) . "','" . str_replace("'", '', $padron["INTERIOR_2"]) . "','" . str_replace("'", '', $padron["MARCA_ACT"]) . "','" . str_replace("'", '', $padron["NUM_APA"]) . "','" . str_replace("'", '', $padron["APARATO"]) . "','" . str_replace("'", '', $padron["TIP_MEDICION"]) . "','" . str_replace("'", '', $padron["F_INST"]) . "','" . str_replace("'", '', $padron["ANOS_DISP_MED"]) . "','" . str_replace("'", '', $padron["MAYORES_5_ANOS"]) . "','" . str_replace("'", '', $padron["F_RETIRO_DISP_MED"]) . "','','','','',0,NULL,'',0,'" . str_replace("'", '', $padron["REGION"]) . "','" . str_replace("'", '', $padron["ESTRATO"]) . "','" . str_replace("'", '', $padron["BANDERA"]) . "','" . $padron['USUARIO_S'] . "','" . $padron['ESTADO_ORDEN'] . "','" . $padron['CANTIDAD_MEDIDORES'] . "','" . $padron['NUM_OS'] . "','" . $padron['CON_PAGO'] . "','" . $padron['DIAMETRO_CONEXION'] . "','" . $padron['TEL_PROP'] . "','" . $padron['CEL_PROP'] . "','" . $padron['TEL_TP'] . "','" . $padron['CEL_TP'] . "','" . str_replace('"', '', str_replace("'", '', $padron['COMENT_OS'])) . "') ON DUPLICATE KEY UPDATE interior2='" . str_replace("'", '', $padron["INTERIOR_2"]) . "',region='" . str_replace("'", '', $padron["REGION"]) . "',estrato='" . str_replace("'", '', $padron["ESTRATO"]) . "',bandera=" . str_replace("'", '', $padron["BANDERA"]) . ", USUARIO_S='" . $padron['USUARIO_S'] . "', ESTADO_ORDEN='" . $padron['ESTADO_ORDEN'] . "',CANTIDAD_MEDIDORES='" . $padron['CANTIDAD_MEDIDORES'] . "',NUM_OS='" . $padron['NUM_OS'] . "',CON_PAGO='" . $padron['CON_PAGO'] . "',DIAMETRO_CONEXION='" . $padron['DIAMETRO_CONEXION'] . "',TEL_PROP='" . $padron['TEL_PROP'] . "',CEL_PROP='" . $padron['CEL_PROP'] . "',TEL_TP='" . $padron['TEL_TP'] . "',CEL_TP='" . $padron['CEL_TP'] . "',COMENT_OS='" . str_replace('"', '', str_replace("'", '', $padron['COMENT_OS'])) . "';";
				file_put_contents("/var/www/html/import_data.sql", $query . "\n", FILE_APPEND);
				#echo "<br>\n" . $query . "<br>\n";
				$modelo->execute($query);
			}
			oci_free_statement($stid);
			oci_close($conn);
		}
	}

	/**
	 * Validar si el usuario está loggeado para accesar al dashboard
	 * @return none
	 */
	public function origen() {
		$conn = oci_connect('ITRON_US','itron_us','187.217.120.218/SGCPPRO');
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		} else {
			$stid = oci_parse($conn, "SELECT * FROM PROVEXTERN.PADRON_COLONIAS_MEDIDORES WHERE BANDERA='3' ORDER BY NIS ASC");
			oci_execute($stid);
			while ($padron = oci_fetch_assoc($stid)) {
				var_dump($padron); echo "<br/><br/>\n";
			}
			oci_free_statement($stid);
			oci_close($conn);
		}
	}

	public function sincronizar() {
		$conn = oci_connect('ITRON_US','itron_us','187.217.120.218/SGCPPRO');
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		} else {
			$contratos = $this->getModel('Expedientes\Contratos');
			$data = $contratos->getDataBy(['statusId'=>7]);
			foreach ($data as $_contrato) {
				$query = "UPDATE PROVEXTERN.PADRON_COLONIAS_MEDIDORES SET BANDERA='7', NUM_APA_RTN = '" . $_contrato['serieMedidor'] . "', MARCA_RTN = '', LECT_INST_RTN = '0', LECT_RETIRO_RTN = '', F_INST_RTN = '" . str_replace("-", "", $_contrato['fecha_ins']) . "', F_FABRIC_RTN = '" . str_replace("-", "", $_contrato['fecha_ins']) . "', DIAMETRO_RTN = '" . $_contrato['DIAMETRO_CONEXION'] . "', NUM_SERIE_RTN = '" . $_contrato['serieMedidor'] . "', TELEMETRIA_RTN = '" . $_contrato['telemetriaMedidor'] . "', LATITUD_RTN = '" . $_contrato['latitud'] . "', LONGITUD_RTN = '" . $_contrato['longitud'] . "' WHERE NIS = '" . trim($_contrato['contrato']) . "'";
				#echo $query . "<br>";
				echo "Actualizando estatus de contrato " . $_contrato['contrato'] . "<br>";
				$stid = oci_parse($conn, $query);
				$r = oci_execute($stid);
				if (!$r) {
					$e = oci_error($stid);
					print htmlentities($e['message']);
					print "\n<pre>\n";
					print htmlentities($e['sqltext']);
					printf("\n%".($e['offset']+1)."s", "^");
					print  "\n</pre>\n";					
				}
				oci_free_statement($stid);
			}
			oci_close($conn);
		}
	}

	public function consultar() {
		$conn = oci_connect('ITRON_US','itron_us','187.217.120.218/SGCPPRO');
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		} else {
			$stid = oci_parse($conn, "SELECT * FROM PROVEXTERN.PADRON_COLONIAS_MEDIDORES WHERE NIS='" . trim($_GET['nis']) . "'");
			oci_execute($stid);
			while ($padron = oci_fetch_assoc($stid)) {
				var_dump($padron); echo "<br/><br/>\n";
			}
			oci_free_statement($stid);
			oci_close($conn);
		}
	}
}

