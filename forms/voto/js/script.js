	/**
	* Archivo '{app}/forms/voto/js/script.js'
	* Script JS principal del módulo "voto"
	*/

	var scriptHandlerPath = 'forms/' + currentForm + '/handlers/form.php';

	$(document).ready(function() {
		$('#pollModal').modal('show');
		
		// Los controles <select> se llenan una vez que se ha terminado la carga del formulario
		cargarRegiones();
		cargarComunas("");
		cargarCandidatos();
		
		// Las comunas seleccionables cambian al seleccionar una región diferente
		$('#selectRegiones').on('change', function() { cargarComunas(this.value);});
		
		// Se vincula el botón de guardado con su respectiva función
		bindSubmitButton();
	});

	// Enlaza la tarea de guardar el formulario al botón principal
	function bindSubmitButton(){
		$('.btn-submit-vote').unbind("click");
		$('.btn-submit-vote').on("click", function(){ submitForm('form-voto'); });
	}

	// Carga los registros de las regiones existentes en el sistema
	function cargarRegiones(){
		handleTask(null, 'cargarRegiones', scriptHandlerPath, function(jSonRegiones){ fillSelect('#selectRegiones', jSonRegiones, 'Seleccione Región'); }, null);
	}

	// Según la región escogida, filtra y carga en el control <select> correspondiente las comunas relacionadas
	function cargarComunas(idReg) {
		var data = new FormData();
		data.append("idRegion", idReg);
		handleTask(data, 'cargarComunas', scriptHandlerPath, function(jSonComunas){ fillSelect('#selectComunas', jSonComunas, 'Seleccione Comuna'); }, null);
	}

	// Carga el conjunto de candidatos en el campo <select> correspondiente del formulario
	function cargarCandidatos(){
		handleTask(null, 'cargarCandidatos', scriptHandlerPath, function(jSonCandidatos){ fillSelect('#selectCandidatos', jSonCandidatos, 'Seleccione Candidato');}, null);
	}