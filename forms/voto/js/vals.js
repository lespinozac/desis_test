	/**
	* Archivo '{app}/forms/voto/js/vals.js'
	* Validaciones para campos del módulo específico "voto"
	*/

	// Validación del alias desde el servidor
	function AliasError(input) {
		return genericValidator(input, "aliasValidate");
	}

	// Validación del rut desde el servidor
	function RutError(input) {
		return genericValidator(input, "rutValidate");
	}

	// Validación del email desde el servidor
	function EmailError(input) {
		return genericValidator(input, "emailValidate");
	}

	// Otras validaciones desde el servidor
	function invalidOthers(form){
		// Verifica que se seleccionen dos opciones en el ítem "Como se enteró de Nosotros"
		var error = false;
		if ($('input:checkbox[name="difusion"]:checked').length < 2){
				errorInputField = "Por favor escoja al menos dos opciones en <i><b>Como se enteró de Nosotros</b></i>";
				$("error[for='difusion']").html('<i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>' + errorInputField);
				error = true;
			}else{
				errorInputField = "";
				$("error[for='difusion']").html('');
		}
		
		return error;
	}

