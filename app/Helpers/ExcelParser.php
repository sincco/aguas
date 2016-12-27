<?php

/**
 * Funciones para manejo de cuentas de usuario
 */
class ExcelParserHelper extends Sincco\Sfphp\Abstracts\Helper {

	public function read($file, $sheet=0) {
		$file = \Sincco\Excell\IOFactory::load($file);
		$sheet = $file->getSheet($sheet);
		$columns = count($sheet->getColumnDimensions());
		$tableData = [];
		$headerData = [];
		foreach ($sheet->getRowIterator() as $row=>$data) {
			foreach ($sheet->getColumnIterator() as $column=>$data) {
				if ($row==1) {
					if (trim($sheet->getCell($column.$row)->getValue()) != '') {
						$headerData[$column] = $sheet->getCell($column.$row)->getValue();
					}
				} else {
					if (trim($sheet->getCell($column.$row)->getValue()) != '') {
						$tableData[$row][$headerData[$column]] = $sheet->getCell($column.$row)->getValue();
					}
				}
			}
		}
		return $tableData;
	}
}