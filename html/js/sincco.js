//JQUERY: Agregar parseo de formulario a datos JSON 
(function ($) {
    $.fn.serializeJSON = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        })
        return o;
    }
})(jQuery);

// Para resaltar el console.log
(function(){
    if(window.console && console.log){
        var old = console.log;
        console.log = function(){
            Array.prototype.unshift.call(arguments, 'DEBUG => ');
            old.apply(this, arguments);
        }
    }  
})();

//SINCCO: Agrupación de funciones cutomizadas
var sincco = new function() {
//Validación de RFC
	this.validarRFC = function(cadena) {
		var exp = /^[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?/i;
    	return exp.test(cadena);
	}
//Validación de Correo
	this.validarCorreo = function(cadena) {
		var exp = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    	return exp.test(cadena);
	}
//Validar no vacia
	this.esVacio = function(cadena) {
		cadena = cadena.trim();
		return (!cadena || 0 === cadena.length);
	}
//Consumir una API/REST
	this.consumirAPI = function(metodo,url,datos) {
		return $.ajax({ 
			type: metodo,
			url: url,
			data: JSON.stringify(datos),
			headers: {"x-access-token": localStorage.getItem('userToken')},
			contentType: "application/json",
			dataType: "json",
			async: true,
			processData: false,
			cache: false,
		});
	}
}

// Modal para indicar carga
var loader;
loader = loader || (function () {
    var ajaxLoader = $('<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false"><div class="modal-dialog"> <div class="modal-content"> <div class="modal-header"><h4 id="titulo" class="modal-title">Cargando...</h4> </div> <div class="modal-body"><div class="alert" role="alert alert-info"><div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 90%"></div> </div> </div> </div> </div> </div></div>');
    return {
        show: function() {
            ajaxLoader.modal('show');
        },
        hide: function () {
            ajaxLoader.modal('hide');
        },

    }
})();

// Modal de avisos
var msgModal;
msgModal = msgModal || (function () {
    var modalMessage = $('<div id="myModal" class="modal fade" role="dialog" data-keyboard="false"><div class="modal-dialog"> <div class="modal-content"> <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 id="titulo" class="modal-title">Aviso!</h4> </div> <div class="modal-body"> <div class="alert" role="alert"><span></span></div></div> </div> </div></div>');
    return {
        show: function(tipo,mensaje) {
        	$(modalMessage).addClass('alert-' + tipo);
        	$(modalMessage).find("span").html(mensaje);
            modalMessage.modal('show');
        }
    }
})();
