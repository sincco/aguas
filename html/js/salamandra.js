// authqrconfig.html
var authqrconfig = new function() {

	this.actualizaCodigo = function() {
		sincco.consumirAPI('GET',BASE_URL + 'authqr/apiObtenerCodigo')
		.done(function(data) {
			$('#codigo').html(data.codigo);
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
			toastr.error('Error al hacer la petición del código.', 'Intenta de nuevo');
		});
	}

	this.init = function() {
		setInterval(function() {
			authqrconfig.actualizaCodigo();
		}, 5000);
	}

}

// dashboard.html
var dashboard = new function() {

	this.filtrar = function() {
		jQuery.redirect(BASE_URL + 'dashboard', { 'fechaInicio': $('#fechaInicio').val(), 'fechaFin': $('#fechaFin').val() });
	}

	this.muestraDetalle = function( panel, titulo ) {
		var cols = false;
		var data = false;
		
		loader.show();

		$( '#panel-detalle' ).bootstrapTable( 'destroy' );
		$( '#modal-titulo' ).html( 'Detalle ' + titulo );
		
		jQuery.ajax({
			url: BASE_URL + 'dashboard/apidetallepanelcols',
			data: { panel:panel },
			method: 'POST',
			async: false,
			success: function (result) {
				cols = result
			},
			async: false
		});

		$( '#panel-detalle' ).bootstrapTable({
			method: 'get',
			url: BASE_URL + 'dashboard/apidetallepanel/panel/' + panel,
			columns: cols,
			height: 600,
			striped: true,
			pagination: true,
			pageSize: 25,
			pageList: [10, 25, 50, 100, 200],
			search: true,
			showColumns: true,
			showRefresh: true,
			showExport: true,
			mobileResponsive: true,
			minimumCountColumns: 2
		});

		$( '#modal' ).modal('show');

		loader.hide();
	}

	this.init = function() {
		$('#fecha-fin').datetimepicker({
			format: 'DD/MM/YYYY'
		});
		$('#fecha-inicio').datetimepicker({
			useCurrent: true,
			format: 'DD/MM/YYYY'
		});
		$("#fecha-inicio").on("dp.change", function (e) {
			$('#fecha-fin').data("DateTimePicker").minDate(e.date);
			$('#fecha-fin').data("DateTimePicker").date(e.date);
		});
		$("#fecha-fin").on("dp.change", function (e) {
			$('#fecha-inicio').data("DateTimePicker").maxDate(e.date);
		});
	}

}

//login.html
var login = new function() {

	this.init = function() {
		$("#password").keypress(function(event) {
			if(event.which == 13) login.accesar();
		});

		$("#usuario").keypress(function(event) {
			if(event.which == 13) login.accesar();
		});

		$(".btn-login").click(function(event) {
			login.accesar();
		});

	}

	this.accesar = function() {
		loader.show()
		sincco.consumirAPI('POST',BASE_URL + 'login/apilogin',{userData:$("#acceso").serializeJSON()})
		.done(function(data) {
			loader.hide();
			if(data.acceso)
				window.location = BASE_URL + 'dashboard';
			else
				toastr.warning('Verifica tu usuario y contraseña.', 'Error de acceso');
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
			toastr.error('Error al hacer la petición de acceso.', 'Intenta de nuevo');
			loader.hide();
		});
	}

}

//clientestabla.html
var clientestabla = new function() {

	this.mostrarAdeudos = function() {
		$( '#modal-bloqueo' ).modal( 'show' );
	}

	this.mostrarActivar = function() {
		$( '#modal-activar' ).modal( 'show' );
	}

	this.bloquearAdeudos = function() {
		loader.show();
		sincco.consumirAPI( 'POST', BASE_URL + 'cxc/adeudos/apibloquear', { auth: $('#2step').val(), clientes: $('#notificaciones').bootstrapTable('getSelections'),  } )
		.done( function( data ) {
			if( data.status )
				toastr.success( 'Se han bloqueado los clientes solicitados', 'Éxito' )
			else
				toastr.error( data.error, 'Intenta de nuevo' )
			loader.hide();
			$( '#modal-bloqueo' ).modal( 'hide' );
		}).fail( function( jqXHR, textStatus, errorThrown ) {
			toastr.error( 'Error al efectuar la operación.', 'Intenta de nuevo' );
			loader.hide();
		})
	}

	this.activarClientes = function() {
		loader.show();
		sincco.consumirAPI( 'POST', BASE_URL + 'catalogo/clientes/apistatus', { auth: $('#2step2').val(), clientes: $('#clientes').bootstrapTable('getSelections'), status:'A' } )
		.done( function( data ) {
			if( data.status )
				toastr.success( 'Se han activado los clientes solicitados', 'Éxito' );
			else
				toastr.error( data.error, 'Intenta de nuevo' );
			loader.hide();
			$( '#modal-bloqueo' ).modal( 'hide' );
		}).fail( function( jqXHR, textStatus, errorThrown ) {
			toastr.error( 'Error al efectuar la operación.', 'Intenta de nuevo' );
			loader.hide();
		})
	}

}