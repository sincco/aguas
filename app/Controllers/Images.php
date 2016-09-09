<?php

use \Sincco\Sfphp\Response;

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
}