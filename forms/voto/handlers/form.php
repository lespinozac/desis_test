<?php
	/**
	* Archivo '{app}/forms/voto/handlers/form.php'
	* Desde este script se administran las tareas asociadas a la persistencia e integridad de los datos
	* de un formulario en el sistema. Las validaciones de los campos obligatorios y de la consistencia respecto a sus respectivos dominios
	* deben implementarse en los controladores correspondientes.	
	* 
	* En el contexto del sistema, el archivo actual implementa el almacenamiento del formulario de voto del sistema con comprobación
	* de duplicado según el rut del usuario. Sólo aquellos usuarios que no hayan emitido su voto podrán hacerlo.
	* También permite gestionar la carga de datos de los componentes del formulario, tales como las listas de selección de regiones, comunas
	* y candidatos.
	*
	* Para tratar varias acciones en este controlador, se sugiere dirigir las tareas a través del flujo condicional switch/case. 
	*/
	
	if (!defined('APP_ROOT')){
		$app_path = dirname($_SERVER['REQUEST_URI']);
		$current_path = dirname($_SERVER['PHP_SELF']);
		
		$fs = explode('/', $app_path);
		$app = $fs[count($fs) - count(explode('/', $current_path)) + 1];
		
		define ('APP_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/'. $app );
	}
	
	include_once(APP_ROOT . '/utils/database.php');
	include_once(APP_ROOT . '/utils/rut.php');
	
	//include_once('../../utils/database.php');
	//include_once('../../utils/rut.php');
	
	// Flujo condicional para seleccionar las acciones segun la tarea solicitada
	if(isset($_POST['task'])) {
		$task = $_POST['task'];
		$json = "";
		switch ($task) {
			case "save": //Crear o actualizar registro
			$json = save($_POST);
			break;
		/*case "load": //Cargar registro
			$id = $_POST['id'];
			$json = load($id);
			break;
		case "delete": //Eliminar registro
			$id = $_POST['id'];
			$json = delete($id);
			break;*/
		case "cargarComunas":
			$json = getComunas($_POST);
			break;
		case "cargarRegiones":
			$json = getRegiones();
			break;
		case "cargarCandidatos":
			$json = getCandidatos();
			break;
		}
		$json = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		print $json;
	}

	function save($context){
	/**
	* Almacena el voto enviado en la base de datos.
	* Los datos son recibidos mediante POST del formulario.
	* Todos los datos existentes en el formulario recibido deben ser válidos.
	*
	* @params
	*			$context: array generado al realizar el envío mediante POST del formulario.
	*
	* @return
	*			array con las estructuras que informan del resultado del proceso:
	*				-> string error: Informa el error que se genera en el caso de algun problema.
	*				-> string message: > Confirma o complementa el resultado de la tarea.
	*/
		$error = "";
		$message = "";
		
		//Formatea el rut con dígito verificador, sin puntos y con guión
		$rut = rutDashed($context["rut"]); 

		//Verificar si el rut aun no emite el voto
		if (!votoEmitido($rut)){
			
			//Se reciben los datos desde el formulario que fueron enviados mediante POST
			$nombre = $context['nombre'];
			$alias = $context['alias'];
			$email = $context['email'];
			$id_comuna = $context['comuna'];
			$id_candidato = $context['candidato'];
			$web = $context['difusion_web'];
			$tv = $context['difusion_tv'];
			$rs = $context['difusion_rs'];
			$amigo = $context['difusion_amigo'];
			
			//Se crean las consultas para registrar al votante y su voto
			$queries = [];
			$queries[] = "INSERT INTO votantes (rut_votante, nombre_votante, alias_votante, email_votante, id_comuna, web_votante, tv_votante, rs_votante, amigo_votante) "
					. "VALUES ('". $rut ."', '". $nombre ."', '" . $alias . "', '". $email ."', " . $id_comuna . ", " . $web . ", " . $tv . ", " . $rs . ", " . $amigo . ")";
			$queries[] = "INSERT INTO votos (id_candidato, rut_votante) "
						. "VALUES (" . $id_candidato . ",'" . $rut . "')";
						
			//Se intentan almacenar los datos
			$success = update($queries);
			
			//Se informa el resultado del intento
			if(!$success)
				$error = "Error al registrar el voto. Hubo un problema en el gestor de base de datos";
			else
				$message = "El voto fue enviado correctamente";
		}
		else{
			//En caso de que ya exista un voto vinculado al rut
			$error = "El rut ingresado ya emitió el voto. No se puede registrar nuevamente.";
		}
		return array("error"=>$error, "message"=>$message);
	}
	
	function votoEmitido($rut){
	/**
	* Función que verifica si el rut ingresado ya ha registrado algun voto en el sistema.
	*
	* @params
	*			$rut: string rut que se busca.
	*
	* @return
	*			boolean (true) si existe al menos un voto asociado al rut ingresado.
	*			boolean (false) si no existen votos asociados al rut.			
	*/
		$query = "SELECT * FROM votos WHERE rut_votante = '" . $rut . "'";
		$rows = executeQuery($query);
		return (count($rows) > 0 ? true : false);
	}
	
	function getRegiones(){
	/**
	* Entrega una matriz asociada de datos con el listado de regiones del sistema.
	*
	* @return
	*			array con el conjunto de regiones registradas en la base de datos del sistema.
	*/
		$query = "SELECT id_region as id, nombre_region as nombre FROM regiones";
		return executeQuery($query);
	}
	

	function getComunas($context){
	/**
	* Entrega una matriz asociada de datos con el listado de comunas que contiene una región.
	* 
	* @params
	* 			$context: Matriz generada a partir del método POST de una solicitud desde el cliente.
	*
	* @return
	*			array con el conjunto de comunas vinculadas a una región y registradas en la base de datos del sistema.
	*/
		$regId = $context['idRegion'];
		if (trim($regId) <> ""){
			$query = "SELECT id_comuna as id, nombre_comuna as nombre FROM provincias JOIN comunas ON provincias.id_provincia = comunas.id_provincia WHERE provincias.id_region = " . $regId . " ORDER BY nombre";
			return executeQuery($query);
		}
		return "";
	}
	
	function getCandidatos(){
	/**
	* Entrega una matriz asociada de datos con el listado de candidatos registrados en el sistema.
	*
	* @return
	*			array con el conjunto de candidatos registradas en la base de datos del sistema.
	*/	
		$query = "SELECT id_candidato as id, nombre_candidato as nombre FROM candidatos ORDER BY nombre";
		return executeQuery($query);
	}

?>