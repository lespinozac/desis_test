<?php
	/**
	* Archivo '{app}/utils/database.php'
	* Este archivo contiene las definiciones para gestionar el acceso a la base de datos y la ejecución de tareas comunes
	* del sistema en ese contexto.
	* Desde aquí se proveen las funciones que sirven de interfaz entre la aplicación y la base de datos para realizar consultas,
	* actualizar datos o eventualmente realizar otras tareas que permitan mantener la integidad de la información en el sistema.
	*/

	//Se integra el archivo que contiene los parámetros de conexión a la base de datos.
	include_once(APP_ROOT . '/const/dbconn.php');

	function dbOpen(){
	/**
	* Apertura de la base de datos del sistema.
	* los parámetros de conexión se encuentran en el archivo './conts/dbconn.php'.
	*
	* @return
	*			mysqli de conexión activa a la base de datos del sistema
	*/
	
		//Obtener los parámetros de conexión almacenados en dbconn.php
		global $host, $user, $password, $database, $port;
		
		//Instanciar el objeto que contiene la nueva conexión
		$con = new mysqli($host, $user, $password, $database, $port);
		if (!$con) {
			die('No es posible conectar a la base de datos: ' . mysqli_error($con));
		}
		
		//Retorna el objeto de conexión
		return $con;
	}

	function dbClose($con){
	/**
	* Cierre de la conexion a la base de datos.
	* @params
	* 			mysqli $con: Conexión a la base de datos.
	*/
		mysqli_close($con);
	}

	function executeQuery($query){
	/**
	* Entrega en una matriz el conjunto de datos generado a partir de una consulta SQL realizada
	* a la base de datos.
	*
	* @params
	* 			string $query: La consulta SQL a ejecutar en la base de datos.
	* @return
	*			array con el conjunto de datos resultante.
	*/
		//Apertura de la conexión
		$con = dbOpen();
		
		//Ejecución de la consulta en la base de datos
		$result = $con->query($query);

		//Cierre de la conexión
		dbClose($con);
		
		//Conversión a matriz del conjunto de datos entregado como resultado
		$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);	
		
		return $rows;
	}

	function update($queries){
	/**
	* Realiza las tareas de actualización, adición y eliminación de registros desde la base de datos del sistema.
	* Mantiene la integridad de los datos ejecutando varias sentencias en una sola transacción y volviéndolas
	* permanentes mediante la operación commit, o descartándolas mediante rollback.
	*
	* @params
	* 			array $queries: vector de cadenas de caracteres que individualizan las consultas a ejecutar en la transacción.
	*
	* @return
	*			boolean (true) si la transacción fue exitosa y se llevaron a cabo completamente todas las sentencias.
	*			boolean (false) si se descartaron las actualizaciones solicitadas debido a algún error.
	*/
	
		//Apertura de la conexión a la base de datos
		$con = dbOpen();
		
		//Desactivar la acción de autoconfirmación de sentencias
		$con->autocommit(FALSE);
		
		$success = true;
		
		//Ejecutar cada sentencia del vector, gestionando los problemas para mantener la integridad de los datos
		try{
			foreach ($queries as $query){
				
				//En caso de que no se logre ejecutar una transacción, se detiene la ejecución de las sentencias
				if(!$con->query($query)) 
					throw new Exception('Consulta no ejecutada: ' . $query);
			}
			
			//Confirma la transacción actual
			$con->commit();
			
		}catch(Exception $e){
			
			//En caso de algun problema, se cancelan los cambios solicitados en la transacción actual, se registra el error y se termina el intento
			$con->rollback();
			echo $e->getMessage();
			$success = false;
			
		}finally{
			
			//En cualquier evento, se cierra la conexión a la base de datos
			dbClose($con);
		}
		
		//Se retorna el estado final (exito o fracaso) de la transacción
		return $success;
	}
?>