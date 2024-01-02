	/*
	* Archivo '{app}/js/site.js'
	* Implementación de funciones globales del sistema
	*/

	function fillSelect(selectId, jSonOptions, notSelectedOption = 'Seleccione', disableOnEmpty = true){
	/**
	* Completa un control <select> de un formulario con las opciones obtenidas desde el servidor.
	*
	* @params
	*			string selectId: identificador único (atributo id) del control <select> a llenar.
	*			JSON jSonOptions: lista en formato JSON obtenida desde el servidor.
	*			string notSelectedOption: Cadena que se mostrará como opción cuando no exista ninguna selección en el control <select>.
	*			boolean disableOnEmpty: Permite indicar al control <select> si debe desactivarse cuando no contenga opciones en su lista.
	*/
		$(selectId).empty();
		$(selectId).prop('disabled', true);
		var optionsLength = jSonOptions.length;
		if (optionsLength > 0)
			$.each(jSonOptions, function (i, item) {
				$(selectId).append($('<option>', {
					value: item.id,
					text : item.nombre
				}));
			});
		
		if ($(selectId + " > option").length > 0 || disableOnEmpty == false)
			$(selectId).prop('disabled', false);
		
		if (notSelectedOption != null && optionsLength > 0)
			$(selectId).prepend('<option selected="true" value="">-- ' + notSelectedOption + ' --</option>');
	}

	/**
	* Gestionar las acciones generales para el cambio de información en los componentes del sistema.
	*
	* @params
	*			FormData data: Formulario que contiene el conjunto de datos a enviar al servidor.
	*			string task: Cadena de caracteres que indica la tarea que se solicitará al servidor.
	*			string handlerfile: Ruta y nombre del archivo que controlará la solicitud en el servidor.
	*			function() success: Función que se ejecutará si la tarea solicitada al servidor ha resultado exitosa.
	*			function() done: Función que se ejecutará una vez terminada la ejecución de la solicitud al servidor.
	*
	* @return
	*			JSON estructura que contiene el conjunto de datos obtenidos desde el servidor.
	*/
	function handleTask(data, task, handlerFile, success,  done) {
		
		data = (data === undefined || data === null) ? new FormData() : data;
		handlerFile = (handlerFile === undefined || handlerFile === null) ? 'forms/' + currentForm + '/handlers/handler.php' : handlerFile;
		success = (success === undefined || success === null) ? (function () { }) : success;
		done = (done === undefined || done === null) ? (function () { }) : done;
		
		data.append('task', task);
		var jSonResp = null;
		
		// Se realiza la petición al servidor y se espera una respuesta
		$.ajax({
			url: handlerFile,
			type: "POST",
			data: data,
			contentType: false,
			processData: false,
			async: false,
			success: function (result) {
				// Tratamiento de la cadena devuelta para crear la estructura JSON
				var j = decodeURIComponent(result);
				j = j.split("\\").join("\\\\");
				jSonResp = $.trim(j) != "" ? JSON.parse(j) : "";
				
				// Si la solicitud se lleva a cabo sin problema, se ejecuta la función success evaluando la respuesta entregada desde el servidor
				success(jSonResp);
			},
			error: function (err) {
				
				console.log("Error: revise la conexión con Handler: error: " + err.error + ", task: " + task + ", handlerFile: " + handlerFile);
				
			},
			complete: function () {
				
				// Al terminar la ejecución de la solicitud, se llama a la función done requerida.
				done(jSonResp);
			}
		});
		return jSonResp;
	}

	function submitForm(formId) {
	/**
	* Automatiza el envío de formularios al servidor para su tratamiento, generalmente solicitado para almacenar o editar los registros del sistema.
	* 
	* @params
	* 			string formId: identificador del formulario (atributo id) que se enviará al servidor.
	*
	* @return
	*			string mensaje de respuesta según el estado final de la solicitud al servidor.
	*/
	
		// Individualización del formulario
		var form = document.getElementById(formId);
		
		// Se evalúan los campos que no pueden estar en blanco
		var invMandat = invalidMandatories(form);
		
		// Se evalúan la validez de otros requerimientos del formulario 
		var invOthers = invalidOthers(form);
		
		// Si no existen inconsistencias en el formulario
		if (!invMandat && !invOthers) {
			var data = new FormData();
			
			// Se adjuntan los valores de los campos que serán enviados al servidor. Estos campos deben tener el atributo name.
			for (var i = 0, ii = form.length; i < ii; ++i) {
				var input = form[i];
				if (input.name && input.type != "select-multiple" && input.type != "select-one" && input.type != 'checkbox' && input.type != 'radio')
					data.append(input.name, input.value);
				else if (input.name && input.type == 'radio' && input.checked)
					data.append(input.name, input.value);
				else if (input.name && input.type == 'checkbox')
					data.append(input.name + '_' + input.value, $(input).is(":checked"));
				else if (input.name && (input.type == "select-multiple" || input.type == "select-one"))
					data.append(input.name, getSelectedOptions($(input).attr('id')));
			}
			
			// Se realiza la solicitud al servidor mediante el controlador respectivo
			struct = handleTask(data, 'save', 'forms/' + currentForm + '/handlers/form.php');
			alert(struct.error);
		}
		else{
			alert("No se pudo enviar el voto. Por favor verifique los campos ingresados e intente nuevamente");
		}
		return "";
	}

	function invalidMandatories(form) {
	/**
	* Verifica que los campos señalados como obligatorios (con el atributo required) no estén en blanco.
	* Para las validaciones de rangos de datos y otras más complejas se utiliza la función invalidOthers
	* 
	* @params
	* 			string form: formulario o conjunto de controles que serán revisados.
	*
	* @return
	*			boolean (true) en caso de que exista un campo obligatorio en blanco.
	*			boolean (false) en caso de que no existan campos obligatorios en blanco.
	*/
		var error = false;
		for (var i = 0, ii = form.length; i < ii; ++i) {
			
			var input = form[i];
			$(input).removeClass('forceInput');
			
			var errorInputField = "";
			if (input.required) {
				var errorMessage = input.getAttribute("required");
				if (input.type == "text"){
					errorInputField = $(input).val().trim().length == 0 ? errorMessage : "";
				}else{
					if (input.type == "select-one" || input.type == "select-multiple") {
						var selectedIndex = typeof input.selectedIndex == typeof undefined ? "" : input.selectedIndex;
						if (getSelectedOptions(input.id).length == 0)
							errorInputField = errorMessage;
					}
				}
				if (errorInputField){
					$("error[for='" + $(input).attr("name") + "']").html('<i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>' + errorInputField);
					$(input).addClass('forceInput');
					error = true;
				}else{
					$("error[for='" + $(input).attr("name") + "']").html('');
				}
			}
			
			if (errorInputField == "" && $(input).attr("validator")) {
				var target = $(input).attr("validator");
				var errorValidation = window[target]($(input));
				if (errorValidation != ""){
					$(input).addClass('forceInput');
					$("error[for='" + $(input).attr("name") + "']").html('<i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>' + errorValidation);
					error = true;
				}
			}

		}
		
		return error;
	}

	/**
	* Retornar array de valores seleccionados en campos Select y MultiSelect para almacenamiento
	*/
	function getSelectedOptions(selectId) {
		var arraySelectedOptions = new Array();
		$("#" + selectId + " option").each(function (name, val) {
			if (val.selected == true && val.value != "" && val.value != 0) {
				arraySelectedOptions.push(val.value);
			}
		});
		return arraySelectedOptions.join();
	}

	/**
	* Función para validaciones genéricas:
	* Utiliza controlador 'handler.php' del módulo respectivo
	*/
	function genericValidator(input, task){
		var data = new FormData();
		data.append("toValidate", input.val());

		var struct = handleTask(data, task);
		if (struct.error != "")
			return struct.error;
		
		if (struct.formatedInput !== undefined)
			input.val(struct.formatedInput);

		return false;
	}