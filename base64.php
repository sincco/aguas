<?php
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./_expedientes'));
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
		}
		$destiny = str_replace($fileName, $fileName . '_MIN', $name);
		resize(100, $destiny, $name);
		$content = file_get_contents($name);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
		#$data = ['contrato'=>$path[2], 'imagen'=>$fileName, 'tipo'=>$type, 'data'=>$base64];
		$data = ['contrato'=>$path[2], 'imagen'=>$fileName, 'tipo'=>$type];
		var_dump($data);
	}
}

function resize($newWidth, $targetFile, $originalFile) {
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
	$image_save_func($tmp, "$targetFile.$new_image_ext");
}