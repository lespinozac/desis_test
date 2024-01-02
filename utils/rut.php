<?php
	/**
	* Archivo '{app}/utils/rut.php'
	* Este archivo contiene funciones utilitarias relacionadas a la comprobación de rut que pueda ingresarse en cualquier módulo del sistema.
	*/

	function rutVerified($rut){
	/**	
    * Verifica la validez del rut dado, calculando el dígito verificador a partir del correlativo
	* y comparándolo con el dígito verificador entregado en el parámetro.
    * 
    * @params
	*			string $rut: Cadena con el rut completo a evaluar, incluyendo el dígito verificador propuesto.
    *
	* @return
	*			boolean (true) si el rut dado como parámetro es válido.
	*			boolean (false) si el rut no es válido.	
	*/

		if ($rut == null || strlen($rut) == 0)
			return false; //Si la cadena no tiene la extensión mínima, retorna false
		
		$rut = strtoupper(str_replace(".", "", $rut));
		$pattern = "/^([0-9]+-[0-9K])$/";
		//$dv = substr($rut, strlen($rut) - 1, 1);
		
		if (!preg_match($pattern, $rut)) //Se evalúa mediante expresión regular si la cadena contiene los caracteres del respectivo dominio del dato
			return false; //Si la cadena contiene otros caracteres a los permitidos, retorna false
		
		$rutTemp = explode("-", $rut); //Se separa la parte correlativa del dígito verificador
		
		$dv = $rutTemp[1];
		if ($dv <> calcDigito((int)$rutTemp[0])) //Se utiliza el dígito verificador de la cadena para compararlo con el dígito verificador calculado
			return false; //En el caso de que no coincidan ambos verificdores, retorna false
		
		return true;
	}
	
	function calcDigito($rut){
	/**	
    * Calcula el digito verificador a partir de la mantisa del rut.
    * 
    * @params
	*			int $rut: Número correlativo sin digito verificador.
    *
	* @return
	*			char calculado con el digito verificador real del correlativo recibido como parámetro, incluyendo
	*				 el caracter 'K' cuando corresponde.
	*/
		$suma = 0;
		$multiplicador = 1;
		while ($rut <> 0){
			$multiplicador++;
			if ($multiplicador == 8)
				$multiplicador = 2;
			$suma += ($rut % 10) * $multiplicador;
			$rut = $rut / 10;
		}
		$suma = 11 - ($suma % 11);
		
		return ($suma == 11 ? 0 : ($suma == 10 ? "K" : $suma));
	}
	
	function rutDashed($rut){
	/**
	* Quita los puntos y añade un guión antes del último caracter de la cadena que representa al rut completo
	* para separar el número del dígito verificador, dejándolo en formato similar a 12345678-K (sin puntos y con guión).
	*
	* @params
	* 			string $rut: Cadena de caracteres que contiene el rut completo. Debe tener al menos 1 caracter de longitud.
	*
	* @return
	* 			string con formato de rut sin puntos y con guión.
	*			string vacío en caso de que la cadena dada como parámetro no cumpla la extensión mínima para el tratamiento.
	*/
		if ($rut == null || strlen($rut) == 0)
			return "";
		
		$rut = preg_replace("/[.-]/","",$rut);
		$format = substr($rut, strlen($rut) - 1);
		$pre = substr($rut, 0, strlen($rut) - 1);
		
		return $pre . "-" . $format;
	}
	
	function rutFormated($rut){
	/**
	* Formatea el rut añadiendo puntos y guión donde corresponde, dejándolo en formato similar a 12.345.678-K (con puntos y con guión).
	*
	* @params
	* 			string $rut: Cadena de caracteres que contiene el rut completo. Debe tener al menos 1 caracter de longitud.
	*
	* @return
	* 			string con formato de rut con puntos y con guión.
	*			string vacío en caso de que la cadena dada como parámetro no cumpla la extensión mínima para el tratamiento.
	*/
	
		$rut = rutDashed($rut); //Se asegura que el rut tenga el guión para usarlo como delimitador
		if ($rut == "")
			return ""; //Si la cadena no tiene la extensión mínima, retorna una cadena vacía
		
		$format = substr($rut, strlen($rut) - 2); //Se extrae el guión y el digito verificador del rut
		
		//Se añaden puntos cada tres dígitos en orden inverso, concatenando inicialmente el guión con su dígito verificador
		$cont = 0; 
		for ($i = strlen($rut) - 3; $i >= 0; $i--){
			$format = substr($rut ,$i, 1) . $format;
			$cont++;
			if ($cont == 3 && $i <> 0){
				$format = "." . $format;
				$cont = 0;
			}
		}
		return $format;
	}
?>