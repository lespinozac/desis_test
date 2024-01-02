<?php
	/**
	* Archivo '{app}/forms/voto/handlers/handler.php'
	* Este archivo implementa el controlador que gestiona las acciones que pueda solicitar un cliente en el contexto
	* de la validación de un formulario. En este caso, el formulario de emisión de votos del sistema.
	*/

	//Se define la ruta a la raiz de la aplicación para obtener los archivos que deban instanciarse desde ubicaciones absolutas
	if (!defined('APP_ROOT')){
		$app_path = dirname($_SERVER['REQUEST_URI']);
		$current_path = dirname($_SERVER['PHP_SELF']);
		
		$fs = explode('/', $app_path);
		$app = $fs[count($fs) - count(explode('/', $current_path)) + 1];
		
		define ('APP_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/'. $app );
	}

	include_once(APP_ROOT . '/utils/rut.php');
	include_once(APP_ROOT . '/utils/utils.php');

	//Estructura condicional para controlar las acciones que se solicitan desde el cliente
	if(isset($_POST['task'])) {
		$task = $_POST['task'];
		$json = "";
		switch ($task) {
			
			//Validación de rut. Si el rut es válido lo formatea con puntos y guión
			case "rutValidate":
				$json = rutValidate($_POST['toValidate']);
				break;
				
			//Validación de alias. El alias debe tener más de 5 caracteres y mezclar letras y números
			case "aliasValidate":
				$json = regexValidation($_POST['toValidate'], "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/",
										"<i><b>Alias</b></i> debe tener una extensión mayor a 5 caracteres y contener letras y números, sin espacios en blanco");
				break;
				
			//Validación de email. El email registrado debe ser un email válido de acuerdo a estandar
			case "emailValidate":
				$json = regexValidation($_POST['toValidate'], "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
										"Debe ingresar una dirección de <i><b>E-mail</b></i> válida");
				break;
		}
		
		//Encripta la matriz asociativa resultante y la representa como texto en formato JSON
		$json = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		print $json;
	}
	
	function rutValidate($rut){
	/**
	* Indica si un ingresado es válido o no. En caso de cumplir el criterio de validación, el rut se reformatea con puntos y guión
	*
	* @params
	*			string $rut: El rut a evaluar.
	* @return
	*			array contenedor de mensajes sobre estado final de la evaluación
	*				-> string error: Información de error en caso de que el rut no sea válido.
	*				-> string formatedInput: Rut formateado con puntos y guión en caso de que se ratifique su validez.
	*/
		$error = "";
		$rutFormated = $rut;
		
		if ($rut <> null && strlen($rut) > 0)
			!rutVerified(rutDashed($rut)) ? $error = "El Rut no es válido" : $rutFormated = rutFormated($rut);
		else
			$error = "No se ha ingresado el Rut";
		
		return array("error"=>$error, "formatedInput"=>$rutFormated);
	}
?>