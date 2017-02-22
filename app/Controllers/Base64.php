<?php

class Base64Controller extends Sincco\Sfphp\Abstracts\Controller {
	
	public function index() {
		$objects = new \RecursiveIteratorIterator(new RecursiveDirectoryIterator(PATH_ROOT . '/_expedientes/'));
		$model = $this->getModel('Aguas');
		$model->init();
		$model->contratosImages();
		$dirs = [];
		foreach($objects as $name => $object){
			$dirs[] = $name;
		}
		asort($dirs);
		foreach($dirs as $name){
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
				$this->resize(500, $destiny, $name);
				$content = file_get_contents($destiny);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
				$contrato = array_pop($path);
				$data = ['contrato'=>$contrato, 'imagen'=>$fileName, 'tipo'=>$type, 'base64'=>$base64, 'fecha'=>date('Y-m-d')];
				#$data = ['contrato'=>$contrato, 'imagen'=>$fileName, 'tipo'=>$type];
				echo 'PROCESANDO ' . $name . '::' . $model->insert($data, $table=false) . '\n';
				#find _expedientes/*/_thumbnails -mindepth 1 -maxdepth 1 | xargs rm -rf; find _expedientes -type f -name "*.db" | xargs rm -rf; find _expedientes -type f -name "*_MIN*" | xargs rm -rf
			}
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
}