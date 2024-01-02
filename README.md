** Implementación de formulario PHP-MySQL-Javascript / Prueba técnica**

------------

[TOC]

####Requerimientos
Se solicita desarrollar un formulario para un **Sistema de Votación**. La interfaz debe validar y guardar los datos del formulario en una base de datos.

Debe utilizarse lenguajes *HTML*, *PHP* y *Javascript* y la base de datos deberá utilizar alguno de los motores de base de datos más conocidos.

Los campos del formulario se someterán a diferentes criterios de aceptación para permitir almacenar los datos que remitan.


####Aspectos Generales
El sistema consiste principalmente en un formulario *html* que debe implementar la gestión de variables en el entorno cliente, para luego validarse y enviarse a la base de datos para su almacenamiento.

Este conjunto integral de características representa las funcionalidades escenciales de cualquier sistema de software que se base en la arquitectura **cliente-servidor**, por lo que esta propuesta no sólo se enfoca en resolver el problema específico, sino en establecer una base para un sistema que pueda ir creciendo modularmente.

Por lo tanto, el objetivo es entregar un sistema de software inicial que sirva como base sólida y flexible para proyectos más amplios y escalables en el futuro. Este sistema básico no solo cumple con los requisitos iniciales del proyecto actual, sino que también se diseñará estratégicamente para admitir la integración de nuevas funcionalidades. La arquitectura subyacente se estructurará de manera modular, permitiendo la fácil incorporación de módulos adicionales según sea necesario.

Con esta aproximación, se busca crear no solo un producto funcional para la prueba actual, sino también una herramienta versátil que sirva como plantilla adaptable para proyectos más grandes y complejos en el futuro.


####Implementación
Para mantener la organización de los componentes del sistema y una estructura comprensible, se han dispuesto los ficheros de tal manera que los programas tengan una cercanía acorde a la interacción que se pretende entre ellos.

Al momento de hablar de módulos, se hace referencia principalmente a la interfaz de usuario (formulario) que lo representa y a los scripts -tanto JavaScript como PHP- que desarrollen tareas específicas para éste, por lo que la implementación física de un módulo estaría circunscrita a una subcarpeta específica dentro del sistema, en este caso dentro de la carpeta *desis_test/forms* de la estructura.

En el caso concreto de la implementación de este sistema, por el momento sólo se cuenta con el módulo *voto*, donde puede ejemplificarse la estructura general propuesta para cualquier módulo que pudiera implementarse a futuro.

Como se menciona, el lenguaje utilizado para los servicios alojados en el servidor es PHP, mientras que del lado cliente se presenta la interfaz mediante HTML en conjunto con JavaScript. Para el intercambio de datos entre el cliente y el servidor se utilizará preferentemente el formato de texto JSON.


##### Estructura de Directorio

    desis_test/
               const/
			   		dbconn.php
               css/
			   		site.css
               forms/
			   		voto/
								css/
								handlers/
											form.php
											handler.php
								js/
											script.js
											vals.js
								form.html
               js/
			   		 site.js
               mdb5/
               sql/
               utils/
			   		 database.php
						rut.php
						utils.php
               index.php


La carpeta **desis_test** indica la raiz de la aplicación. A partir de ésta se puede acceder a las carpetas que contienen los archivos del sistema, siendo los niveles más superficiales los que contienen las funciones más generales.

Las interfaces de usuario con sus respectivas tareas y acciones se alojan en la carpeta **forms**. Estos módulos deben mantener la estructura que se muestra en la carpeta **voto** para simplificar la interacción entre los programas que lo componen.

#####Punto de Entrada
El punto de entrada es el lugar en el código donde se inicia la ejecución del programa. En el caso de este sistema, las peticiones iniciales se derivan desde el archivo *index.php*, alojado en la raíz de la aplicación, hacia el módulo solicitado por el usuario. Aquí se cargará el archivo **form.html** contenido en el directorio correspondiente.

Al cargarse el formulario principal del módulo requerido, los controladores completan la información que debe mostrarse, para iniciar así la interacción con el usuario. En el caso del módulo *voto*, éste solicitará al servidor la información registrada en la base de datos referente a las regiones y a los candidatos por quienes se puede votar.

#####Controladores
Los controladores o handlers permiten tratar varias acciones del lado servidor. Mediante éstos se pueden gestionar los eventos del sistema y entregarle respuestas al usuario por las acciones inicialmente solicitadas por ellos.

La carpeta *handlers* del módulo contiene estos controladores. Aquí encontramos el archivo *form.php*, que se encarga de las tareas de carga de datos al formulario (tales como las listas de selección de regiones, comunas y candidatos de la interfaz *voto*), y guardado en la base de datos, mientras que el controlador *handler.php* resuelve las peticiones de validaciones de los campos del formulario.

####Validaciones
La implementación del sistema considera diversas validaciones, las cuales pueden distinguirse según la etapa de la secuencia en que se encuentre el intercambio de los datos entre el cliente y el servidor.

- **En el cliente**: Para verificar que los datos obligatorios no estén en blanco

- **En el cliente con solicitud al servidor**: Para verificar que los datos ingresados estén en el dominio de los datos definido en el modelo o que cumplan otras características requeridas.

- **En el servidor**: Para evitar duplicidad de registros u otras posibles inconsistencias de la información al realizar las transacciones con la base de datos.

#####Validaciones del lado Cliente:
	Los controles Nombre, Alias, RUT, Email (todas de tipo *text*) no pueden enviarse en blanco. 
	Los controles tipo select Región, Comuna, Candidato deben tener una opción seleccionada.
	El conjunto de checkbox debe tener al menos dos opciones seleccionadas.

Estas comprobaciones se ejecutan en el ámbito de los *javascripts* antes de intentar el envío de cualquier dato al servidor.

Específicamente, el archivo *script.js* contiene la mayor parte de las funciones desencadenadoras de estas validaciones. Este archivo se encuentra alojado en la ruta **desis_test/forms/voto/js/script.js**.
	

#####Validaciones del lado Cliente con solicitud al servidor
	El campo Alias debe tener más de 5 caracteres y debe mezclar números y letras.
	El campo RUT debe ser un rut válido según el formato chileno.
	El campo Email debe ajustarse al estándar de direcciones de correo electrónico.

Consisten en validaciones que pudieran ser más complejas, por lo que se implementan mayormente del lado del servidor, pero siendo gatilladas por eventos del cliente.

Lo que se envía al servidor es sólo parte del conjunto de datos y no el formulario completo, permitiendo entregar respuestas más complejas apartir del procesamiento de una cantidad mínima de datos y realizar consultas a la base de datos de ser necesario.

Principalmente, el archivo *vals.js* del módulo efectúa estas tareas (**desis_test/forms/voto/js/vals.js**).

#####Validaciones exclusivas del servidor
	El usuario no puede emitir doble votación, por lo que se verifica la existencia del rut antes de guardar el registro.
Una vez realizadas todas las validaciones posibles del lado del cliente, las validaciones del lado servidor permiten mantener la integridad de los datos en el sistema.

Si bien pudiera haberse implementado la verificación de duplicidad simplemente procesando el campo *RUT*, este caso se toma como ejemplo para lo que debiera hacerse en situaciones de manejo más complejo de datos, donde se debiera mantener la integridad de un conjunto de datos mayor.

