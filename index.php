<?php
# NOTICE OF LICENSE
#
# This source file is subject to the Open Software License (OSL 3.0)
# that is available through the world-wide-web at this URL:
# http://opensource.org/licenses/osl-3.0.php
#
# -----------------------
# @author: Iván Miranda (@deivanmiranda)
# @version: 1.0.0
# -----------------------
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/paths.php';

use Sincco\Sfphp\Session;
use Sincco\Sfphp\Launcher;
use Sincco\Tools\Debug;

try {
	Session::get();
	new Launcher();
}catch (\Exception $err) {
	$errorInfo = sprintf( '%s: %s in %s on line %s.',
		'Error',
		$err,
		$err->getFile(),
		$err->getLine()
	);
	Debug::dump( $errorInfo );
}