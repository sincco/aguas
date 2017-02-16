<?php
$objects = new \RecursiveIteratorIterator(new RecursiveDirectoryIterator('./_expedientes/1000019'));
foreach($objects as $name => $object){
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
		resize(500, $destiny, $name);
		$content = file_get_contents($destiny);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
		#$data = ['contrato'=>$path[2], 'imagen'=>$fileName, 'tipo'=>$type, 'data'=>$base64];
		$data = ['contrato'=>$path[2], 'imagen'=>$fileName, 'tipo'=>$type];
		var_dump($data);
	}
}

function resize($newWidth, $targetFile, $originalFile) {
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
	watermark($targetFile, $image_create_func, $image_save_func, $date);
}

function watermark($fileName, $image_create_func, $image_save_func, $text = false) {
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
/*
CREATE TABLE `aguas.net`.`contratosImages` ( `contrato` CHAR(8) NOT NULL , `imagen` CHAR(20) NOT NULL , `tipo` CHAR(4) NOT NULL , `base64` MEDIUMTEXT CHARACTER SET ascii COLLATE ascii_bin NOT NULL ) ENGINE = InnoDB COMMENT = 'Imagens de proceso';
ALTER TABLE `contratosImages` ADD `idContratoImage` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`idContratoImage`);
*/