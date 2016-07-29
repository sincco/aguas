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
use Sincco\Sfphp\DB\Connector;
use Sincco\Sfphp\Config\Writer;
use Sincco\Sfphp\Crypt;

define( 'DEV_CACHE', 1 );
define( 'PATH_ROOT', __DIR__ );
define( 'PATH_ERROR_TEMPLATE', PATH_ROOT . '/errors' );
define( 'PATH_CACHE', PATH_ROOT . '/var/cache' );
define( 'PATH_LOGS', PATH_ROOT . '/var/log' );
define( 'PATH_CONFIG', PATH_ROOT . '/etc/config' );
define( 'PATH_SESSION', PATH_ROOT . '/var/session' );

$_contenido = file_get_contents( "php://input" );
$params = array();		
if( trim( $_contenido ) != "" ) {
	$params = json_decode( $_contenido, TRUE );
}
$data = $params[ 'instalacion' ];
$data[ 'dbname' ] = $data[ 'user' ];
$bases = array( 
	'default'=>array(
		'host'=>$data[ 'host' ],
		'user'=>$data[ 'user' ],
		'password'=>$data[ 'password' ],
		'dbname'=>$data[ 'user' ],
		'type'=>$data[ 'type' ]
	),
	'sae'=>array(
		'host'=>$data[ 'sae_host' ],
		'user'=>'sysdba',
		'password'=>'masterkey',
		'dbname'=>$data[ 'sae_dbname' ],
		'type'=>'firebird'
	),
);
$db = new Connector( $data );
if( !createXML( $bases, $data[ 'dominio' ] ) ) {
	echo json_encode( [ 'respuesta' => true ] );
	
}
try {
	$lines = file( 'salamandra.sql' );
	$query = '';
	foreach ( $lines as $line ) {
		if ( substr( $line, 0, 2 ) == '--' || $line == '' )
		continue;
		$query .= $line;
		if ( substr( trim( $line ), -1, 1 ) == ';' ) {
			$db->query( $query );
			$query = '';
		}
	}
	echo json_encode( [ 'respuesta'=>true ] );
} catch (\PDOException $err) {
	$errorInfo = sprintf( '%s: %s in %s on line %s.',
		'Database Error',
		$err,
		$err->getFile(),
		$err->getLine()
	);
	echo json_encode( [ 'respuesta'=>false, 'mensaje'=>$errorInfo ] );
}


function createXML( $bases, $url ) {
	$_llave_encripcion = strtoupper(md5(microtime().rand()));
	$bases[ 'sae' ][ 'password' ] = Crypt::encrypt( $bases[ 'sae' ][ 'password' ], $_llave_encripcion );
	$bases[ 'default' ][ 'password' ] = Crypt::encrypt( $bases[ 'default' ][ 'password' ], $_llave_encripcion );
	$_config = [
		'app' => [
			'key' => $_llave_encripcion,
			'name' => 'salamandra',
			'company' => 'sincco.com',
			'timezone' => 'America/Chicago',
			'version' => '0.0.1'
		],
		'front' => [
			'url' => $url,
		],
		'bases' => $bases,
		'sesion' => [
			'type' => "DEFAULT",
			'name' => "salamandra",
			'ssl' => 0,
			'inactivity' => 300,
		],
		'dev' => [
			'showerrors' => 1,
		],
	];
	return Writer::write( $_config, 'config', 'etc/config/config.xml');
		chmod("./Etc/Config/config.xml", 0777);
}