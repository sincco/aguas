<?php

use \Sincco\Sfphp\Response;
use \Sincco\Tools\Debug;

class ImagesController extends Sincco\Sfphp\Abstracts\Controller 
{
	public function resize() {
		$directories = scandir(PATH_ROOT . '/_expedientes');
		array_shift($directories);
		array_shift($directories);
		foreach ($directories as $dir) {
			if (is_dir(PATH_ROOT . '/_expedientes/' .str_replace(' ', '%20', $dir))) {
				$files = scandir(PATH_ROOT . '/_expedientes/' .str_replace(' ', '%20', $dir));
				array_shift($files);
				array_shift($files);
				foreach ($files as $file) {
					if (strpos($file, '_thumbnails') === FALSE) {
						$fileOld = PATH_ROOT . '/_expedientes/' . $dir . '/' . $file;
						$fileNew = PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/' . $file;
						if (!is_dir(PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/')) {
							mkdir(PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/');
							chmod(PATH_ROOT . '/_expedientes/' . $dir . '/_thumbnails/', 0777);
						}
						if (!file_exists($fileNew)) {
							$this->helper('Images')->resize($fileOld, $fileNew);
						}
					}
				}
			}
		}
	}

	// Marca de agua
	public function watermark() {
		die();
		$contrato = $this->getParams('contrato');
		$files = scandir(PATH_IMG . '/' . $contrato);
		array_shift($files);
		array_shift($files);
		foreach ($files as $file) {
			if (strpos($file, '_thumbnails') === FALSE) {

				imagepng(imagecreatefromstring(file_get_contents(PATH_IMG . $contrato . '/' . $file)), PATH_IMG . $contrato . '/tmp_' . $file);
				$imagen=imagecreatefrompng(PATH_IMG . $contrato . '/tmp_' . $file);
				$watermarktext="adp.itron.mx\n" . date("Y-m-d H:i") . "\NIS " . $contrato . "\n" . $_imagen;
				$blanco = imagecolorallocate($imagen, 255, 255, 255);
				$negro = imagecolorallocate($imagen, 0, 0, 0);
				imagettftext($imagen, 30, 0, 21, 30, $negro, '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', $watermarktext);
				imagettftext($imagen, 30, 0, 20, 29, $blanco, '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', $watermarktext);
				imagepng($imagen, PATH_IMG . $contrato . '/' . $file);

				$fileOld = PATH_IMG . '/' . $contrato . '/tmp_' . $file;
				$fileNew = PATH_IMG . '/' . $contrato . '/_thumbnails/' . $file;
				if (!is_dir(PATH_IMG . '/' . $contrato . '/_thumbnails/')) {
					mkdir(PATH_IMG . '/' . $contrato . '/_thumbnails/');
					chmod(PATH_IMG . '/' . $contrato . '/_thumbnails/', 0777);
				}
				if (!file_exists($fileNew)) {
					$this->helper('Images')->resize($fileOld, $fileNew);
				}
			}
		}
	}

	public function upload() {
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			if(!(isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier'])!='')){
				$_GET['resumableIdentifier']='';
			}
			$temp_dir = PATH_TMP . '/' . $_GET['resumableIdentifier'];
			if(!(isset($_GET['resumableFilename']) && trim($_GET['resumableFilename'])!='')){
				$_GET['resumableFilename']='';
			}
			if(!(isset($_GET['resumableChunkNumber']) && trim($_GET['resumableChunkNumber'])!='')){
				$_GET['resumableChunkNumber']='';
			}
			$chunk_file = $temp_dir.'/'.$_GET['resumableFilename'].'.part'.$_GET['resumableChunkNumber'];
			if (file_exists($chunk_file)) {
				header("HTTP/1.0 200 Ok");
			} else {
				header("HTTP/1.0 404 Not Found");
			}
		}

		if (!empty($_FILES)) foreach ($_FILES as $file) {
			if ($file['error'] != 0) {
				var_dump('error '.$file['error'].' in file '.$_POST['resumableFilename']);
				continue;
			}
			// archivo destino (format <filename.ext>.part<#chunk>
			if(isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier'])!=''){
				$temp_dir = PATH_TMP . '/' . $_POST['resumableIdentifier'];
			}
			$dest_file = $temp_dir.'/'.$_POST['resumableFilename'].'.part'.$_POST['resumableChunkNumber'];
			if (!is_dir($temp_dir)) {
				mkdir($temp_dir, 0777, true);
			}
			if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
				var_dump('Error saving (move_uploaded_file) chunk '.$_POST['resumableChunkNumber'].' for file '.$_POST['resumableFilename']);
			} else {
				// checa las partes cargadas y crea el archivo
				$expediente = explode('-', $_POST['resumableIdentifier']);
				$this->createFileFromChunks($temp_dir, $_POST['resumableFilename'], $_POST['resumableChunkSize'], $_POST['resumableTotalSize'], $_POST['resumableTotalChunks'], $expediente[0]);
			}
		}
	}

	private function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize,$total_files, $expediente) {
		// cuenta las partes del archivo
		$total_files_on_server_size = 0;
		$temp_total = 0;
		foreach(scandir($temp_dir) as $file) {
			$temp_total = $total_files_on_server_size;
			$tempfilesize = filesize($temp_dir.'/'.$file);
			$total_files_on_server_size = $temp_total + $tempfilesize;
		}
		// revisa si cada parte está en el directorio
		//Si el tamaño de las partes es igual al del archivo subido
		if ($total_files_on_server_size >= $totalSize) {
			if (! is_dir(PATH_IMG . $expediente)) {
				mkdir(PATH_IMG . $expediente, 0775, true);
			}
			// Directorio final
			if (($fp = fopen(PATH_IMG . $expediente . '/' . $fileName, 'w')) !== false) {
				for ($i=1; $i<=$total_files; $i++) {
					fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
					//var_dump('escribiendo parte '.$i);
				}
				fclose($fp);
				chmod(PATH_IMG . $expediente . '/' . $fileName, 0777);
				//$size = getimagesize(PATH_IMG . $expediente . '/' . $fileName . '.tmp');
				//$this->helper('Images')->resize(PATH_IMG . $expediente . '/' . $fileName . '.tmp', PATH_IMG . $expediente . '/' . $fileName, $size[0] / 2, $size[1] / 2);
				// unlink(PATH_IMG . $expediente . '/' . $fileName);
			} else {
				var_dump('no se pudo escribir el archivo final',PATH_IMG . $expediente . '/' . $fileName);
				return false;
			}
			// renombra el directorio (para evitar sobreescritura de piezas)
			if (rename($temp_dir, $temp_dir.'_UNUSED')) {
				$this->rrmdir($temp_dir.'_UNUSED');
			} else {
				$this->rrmdir($temp_dir);
			}
		}
	}

	private function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . "/" . $object) == "dir") {
						$this->rrmdir($dir . "/" . $object); 
					} else {
						unlink($dir . "/" . $object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

}