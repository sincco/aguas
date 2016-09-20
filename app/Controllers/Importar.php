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
			$stid = oci_parse($conn, "SELECT NIS, to_char(ALTACONTRATO, 'yyyy-mm-dd') ALTACONTRATO, PROPIETARIO, USUARIO, SUMINISTRO, NUMTOMAS, GIRO, UTILIZACION, TARIFA, SERVICIOS, NIVELTARIFARIO, ASOCIACION, CVECATASTRAL, MUNICIPIO, COLONIA, VIA, CALLE, NUMOFICIAL, INTERIOR_1, INTERIOR_2, MARCA_ACT, NUM_APA, APARATO, TIP_MEDICION, F_INST, ANOS_DISP_MED, MAYORES_5_ANOS, to_char(F_RETIRO_DISP_MED, 'yyyy-mm-dd') F_RETIRO_DISP_MED FROM PROVEXTERN.PADRON_COLONIAS_MEDIDORES WHERE ROWNUM <= 5");
			oci_execute($stid);
			while ($padron = oci_fetch_assoc($stid)) {
				echo "Procesando " . $padron["NIS"] . "<br>\n";
				$query = "INSERT INTO contratos (contrato, altaContrato, propietario, usuario, suministro, numTomas, giro, utilizacion, tarifa, servicios, nivelTarifario, asociacion, cveCatastral, municipio, colonia, via, calle, numOficial, longitud, latitud, serieMedidor, telemetriaMedidor, interior, interior2, marca_act, num_apa, aparato, tip_medicion, f_inst, anos_disp_med, mayores_5_anos, f_retiro_disp_med, cobro, mod_med_ins, tipo_material, caudal_max, num_dig_medidor, f_fabricacion, diametro_toma, lectura_inicial)
				VALUES ('" . $padron["NIS"] . "','" . $padron["ALTACONTRATO"] . "','" . $padron["PROPIETARIO"] . "','" . $padron["USUARIO"] . "','" . $padron["SUMINISTRO"] . "','" . $padron["NUMTOMAS"] . "','" . $padron["GIRO"] . "','" . $padron["UTILIZACION"] . "','" . $padron["TARIFA"] . "','" . $padron["SERVICIOS"] . "','" . $padron["NIVELTARIFARIO"] . "','" . $padron["ASOCIACION"] . "','" . $padron["CVECATASTRAL"] . "','" . $padron["MUNICIPIO"] . "','" . $padron["COLONIA"] . "','" . $padron["VIA"] . "','" . $padron["CALLE"] . "','" . $padron["NUMOFICIAL"] . "','','','','','" . $padron["INTERIOR_1"] . "','" . $padron["INTERIOR_2"] . "','" . $padron["MARCA_ACT"] . "','" . $padron["NUM_APA"] . "','" . $padron["APARATO"] . "','" . $padron["TIP_MEDICION"] . "','" . $padron["F_INST"] . "','" . $padron["ANOS_DISP_MED"] . "','" . $padron["MAYORES_5_ANOS"] . "','" . $padron["F_RETIRO_DISP_MED"] . "','','','','',0,NULL,'',0);";
				$modelo->direct($query);
			}
			oci_free_statement($stid);
			oci_close($conn);
		}
	}

}