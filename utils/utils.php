<?php
	/**
	* Archivo '{app}/utils/utils.php'
	* Este archivo contiene funciones utilitarias para operaciones genéricas que pueden ejecutarse desde cualquier módulo del sistema.
	*/

	function regexValidation($toValidate, $pattern, $errorMessage){
	/**
	* Ejecuta la validación de una cadena de caracteres proveniente de un formulario POST
	* utilizando una expresión regular como patrón.
	*
	* @params
	*			string $pattern: Expresión regular que se utiliza como validador.
	*			string $errorMessage: Mensaje de error en caso de que la validación no se cumpla.
	* @return
	*/
	
		$error = "";
		if (!preg_match($pattern, $toValidate)) //Evalúa si la cadena cumple el criterio definido en la expresión regular
			$error = $errorMessage;
		
		return array("error"=>$error);
	}
?>