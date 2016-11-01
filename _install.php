<!--
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
-->
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="initial-scale=1, width=device-width, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>Salamandra</title>
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
	<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<script type="text/javascript" src="html/js/sincco.min.js"></script>
</head>

<body>

<div class="jumbotron">
	<div class="row">
		<div class="col-xs-12">
			<h1>Salamandra</h1>
			<p class="lead">Instalación del sistema</p>
		</div>
	</div>
</div>
<?php if( !file_exists( 'etc/config/config.xml' ) ) : ?>
<div class="container">
	<form role="form" id="instalacion">
	<div class="panel panel-success">
		<div class="panel-heading">
			<h4 class="panel-title">Base de datos (Mysql)</h4>
		</div>
		<div class="panel-body">
			<input type="hidden" name="type" value="mysql">
			<div class="col-sm-4 col-xs-12">
				<label>Host</label>
				<input type="text" name="host" placeholder="host" class="form-control col-sm-4 col-xs-12">
			</div>
			<div class="col-sm-4 col-xs-12">
				<label>Usuario</label>
				<input type="text" name="user" placeholder="user" class="form-control col-sm-4 col-xs-12">
			</div>
			<div class="col-sm-4 col-xs-12">
				<label>Contraseña</label>
				<input type="text" name="password" placeholder="password" class="form-control col-sm-4 col-xs-12">
			</div>
		</div>
	</div>
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h4 class="panel-title">Base de datos (SAE)</h4>
		</div>
		<div class="panel-body">
			<div class="col-md-3 col-xs-12">
				<label>Ubicación</label>
				<input type="text" name="sae_host" placeholder="host" class="form-control col-md-3 col-xs-12">
			</div>
			<div class="col-md-3 col-xs-12">
				<label>Archivo</label>
				<input type="text" name="sae_dbname" placeholder="host" class="form-control col-md-3 col-xs-12">
			</div>
		</div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title">Sistema</h4>
		</div>
		<div class="panel-body">
			<div class="col-sm-4 col-xs-12">
				<label>Dominio</label>
				<input type="text" name="dominio" placeholder="url" class="form-control col-sm-4 col-xs-12">
			</div>
		</div>
	</div>
	</form>
	<div class="row">
		<button type="button" class="btn btn-success" onclick="guardar()">Instalar</button>
	</div>
</div>
<?php else : ?>
	<div class="container">
		<h3>Ya existe un archivo de configuración, por lo tanto no se puede aplicar una instalación</h3>
	</div>
<?php endif;?>
<script type="text/javascript">
function guardar() {
	loader.show()
	sincco.consumirAPI('POST','_apiinstall.php',{instalacion:$("#instalacion").serializeJSON()})
	.done(function(data) {
		loader.hide()
		if(data.respuesta) {
			window.location = 'login'			
		} else {
			toastr.warning(data.mensaje, 'Error al instalar')
		}
	}).fail(function(jqXHR, textStatus, errorThrown) {
		console.log(errorThrown);
		toastr.error('Error al instalar.', 'Intenta de nuevo')
		loader.hide()
	})
}
</script>

</body>