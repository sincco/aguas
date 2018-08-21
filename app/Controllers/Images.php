<?php

use \Sincco\Sfphp\Response;
use \Sincco\Tools\Debug;

class ImagesController extends Sincco\Sfphp\Abstracts\Controller 
{
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
				$this->process(PATH_IMG . $expediente[0] . '/' . $_POST['resumableFilename']);
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
			$del = explode(".", PATH_IMG . $expediente . '/' . $fileName);
			array_map('unlink', glob($del[0]."*"));
			// Directorio final
			if (($fp = fopen(PATH_IMG . $expediente . '/' . $fileName, 'w')) !== false) {
				for ($i=1; $i<=$total_files; $i++) {
					fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
					//var_dump('escribiendo parte '.$i);
				}
				fclose($fp);
				chmod(PATH_IMG . $expediente . '/' . $fileName, 0777);
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

	private function process($name) {
		$model = $this->getModel('Aguas');
		$model->init();
		$model->contratosImages();
		$fileName = pathinfo($name, PATHINFO_FILENAME);
		if (strlen($fileName) > 1) {
			$type = pathinfo($name, PATHINFO_EXTENSION);
			$path = pathinfo($name, PATHINFO_DIRNAME);
			$path = explode('/', $path);
			if (strrpos($fileName, 'venta') !== false) {
				$_file = explode('-', $fileName);
				$fileName = $_file[0] . '-' . $_file[2];
			} else {
				$fileName = substr($fileName, 1);
				$_file = explode('-', $fileName);
				$fileName = $_file[0] . '-' . $_file[1];
			}
			$ext = explode('.', $name);
			$ext = end($ext);
			$destiny = pathinfo($name, PATHINFO_DIRNAME) . '/' . $fileName . '_MIN.' . $ext;
			$del = explode(".", $destiny);
			array_map('unlink', glob($del[0]."*"));
			$this->resize(900, $destiny, $name);
			$content = file_get_contents($destiny);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
			$contrato = array_pop($path);
			$data = ['contrato'=>$contrato, 'imagen'=>$fileName, 'tipo'=>$type, 'base64'=>$base64, 'fecha'=>date('Y-m-d')];
			$model->insert($data, $table=false);
		}
	}

	private function resize($newWidth, $targetFile, $originalFile) {
		$date = date("d M Y H:i:s.", filectime($originalFile));
		$info = getimagesize($originalFile);
		$mime = $info['mime'];

		switch ($mime) {
			case 'image/jpeg':
				$image_create_func = 'imagecreatefromjpeg';
				$image_save_func = 'imagejpeg';
				$new_image_ext = 'jpg';
				break;

			case 'image/png':
				$image_create_func = 'imagecreatefrompng';
				$image_save_func = 'imagepng';
				$new_image_ext = 'png';
				break;

			case 'image/gif':
				$image_create_func = 'imagecreatefromgif';
				$image_save_func = 'imagegif';
				$new_image_ext = 'gif';
				break;

			default: 
			throw new Exception('Unknown image type.');
		}

		$img = $image_create_func($originalFile);
		list($width, $height) = getimagesize($originalFile);

		$newHeight = ($height / $width) * $newWidth;
		$tmp = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		if (file_exists($targetFile)) {
			unlink($targetFile);
		}
		$image_save_func($tmp, $targetFile);
		$this->watermark($targetFile, $image_create_func, $image_save_func, $date);
	}

	private function watermark($fileName, $image_create_func, $image_save_func, $text = false) {
		$im = $image_create_func($fileName);
		$estampa = imagecreatefrompng('html/img/adp_watermark.png');

		$margen_dcho = 10;
		$margen_inf = 10;
		$sx = imagesx($estampa);
		$sy = imagesy($estampa);

		imagecopy($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa));
		if ($text) {
			$text_color = imagecolorallocate($im, 0, 0, 0);
			imagestring($im, 5, 5, 5,  $text, $text_color);
			$text_color = imagecolorallocate($im, 255, 248, 6);
			imagestring($im, 5, 4, 4,  $text, $text_color);
		}

		$image_save_func($im, $fileName);
		imagedestroy($im);
	}
	
	public function reprocess() {
		$archivos = file_get_contents('./archivos.txt');
		$archivos = explode(PHP_EOL, $archivos);
		foreach($archivos as $name) {
			$model = $this->getModel('Aguas');
			$model->init();
			$model->contratosImages();
			$fileName = pathinfo($name, PATHINFO_FILENAME);
			if (strlen($fileName) > 1) {
				$type = pathinfo($name, PATHINFO_EXTENSION);
				$path = pathinfo($name, PATHINFO_DIRNAME);
				$path = explode('/', $path);
				if (strrpos($fileName, 'venta') !== false) {
					$_file = explode('-', $fileName);
					$fileName = $_file[0] . '-' . $_file[2];
				} else {
					$fileName = substr($fileName, 1);
					$_file = explode('-', $fileName);
					$fileName = $_file[0] . '-' . $_file[1];
				}
				$ext = explode('.', $name);
				$ext = end($ext);
				$destiny = pathinfo($name, PATHINFO_DIRNAME) . '/' . $fileName . '_MIN.' . $ext;
				$del = explode(".", $destiny);
				array_map('unlink', glob($del[0]."*"));
				$this->resize(900, $destiny, $name);
				$content = file_get_contents($destiny);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
				$contrato = array_pop($path);
				$data = ['contrato'=>$contrato, 'imagen'=>$fileName, 'tipo'=>$type, 'base64'=>$base64, 'fecha'=>date('Y-m-d')];
				$model->insert($data, $table=false);
			}
		}
		echo "TERMINADO";
	}
}
