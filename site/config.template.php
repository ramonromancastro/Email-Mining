<?php
/*************************/
/* Configuraci�n general */
/*************************/

$config['app']['general']['ok'] = 'Ok'; // Texto por defecto para sustituir en los informes.
$config['app']['general']['error'] = 'Error'; // Texto por defecto para sustituir en los informes.
$config['app']['general']['unknown'] = '-'; // Texto por defecto para aquellos campos que no tengan valor.
$config['app']['general']['nodata'] = 'No existe informaci�n disponible'; // Texto por defecto para los informes sin informaci�n disponible.
$config['app']['general']['refresh'] = 60; // INtervalode segundos de actualizaci�n de la p�gina de inicio. Establecer a NULL para desactivar.
$config['app']['general']['autosync'] = false; // Sincroniza de manera autom�tica con el correo electr�nico cada vez que se visita la p�gina de inicio.
$config['app']['general']['autodelete'] = true; // Elimina los correos que no son de error del servidor mientras realiza la sincronizaci�n.
$config['app']['general']['debug'] = true; // Vac�a la base de datos y descarga todos los emails en cada sincronizaci�n, sin eliminar correos.

/*************************************************/
/* Configuraci�n de acceso al correo electr�nico */
/*************************************************/

$config['app']['mail']['hostname'] = '{imap.domain.local:993/imap/ssl}INBOX';
$config['app']['mail']['username'] = 'emailmining@domain.local';
$config['app']['mail']['password'] = 'p@ssw0rd';

/*************************/
/* Formatos de cabeceras */
/*************************/

/*
	Las variables que se deben extraer para cada uno de los formatos de cabecera son
		- source
		- service
		- status
	En el caso de que una variable no est� definida en la cabecera, se utilizar� el valor
	de configuraci�n $config['app']['general']['unknown'].
*/

$config['app']['format']['nagios_host']['title'] = '/(\[.*\])?\s?(Nagios): Host (.*): (.*) \((.*)\)/';
$config['app']['format']['nagios_host']['variables'] = array('source' => 3, 'status' => 5);
$config['app']['format']['nagios_host']['error'] = '/(WARNING|CRITICAL|UNKNOWN)/';

$config['app']['format']['nagios_service']['title'] = '/(\[.*\])?\s?(Nagios): Service (.*): (.*)\/(.*) \((.*)\)/';
$config['app']['format']['nagios_service']['variables'] = array('source' => 4, 'service' => 5, 'status' => 6);
$config['app']['format']['nagios_service']['error'] = '/(WARNING|CRITICAL|UNKNOWN)/';

$config['app']['format']['ipcam']['title'] = '/(\[.*\])?\s?(.*) - (Motion Detection Notification)/';
$config['app']['format']['ipcam']['variables'] = array('source' => 2, 'service' => 3, 'status' => 3);
$config['app']['format']['ipcam']['error'] = '/Motion Detection Notification/';

$config['app']['format']['bacula_job']['title'] = '/(\[.*\])?\s?(Bacula): .* (.*) of (.*)/';
$config['app']['format']['bacula_job']['variables'] = array('source' => 2, 'service' => 4, 'status' => 3);
$config['app']['format']['bacula_job']['error'] = '/ERROR/';

$config['app']['format']['wsus']['title'] = '/(\[.*\])?\s?(WSUS): (.*) de (\w*)/';
$config['app']['format']['wsus']['variables'] = array('source' => 2, 'service' => 4, 'status' => 3);

$config['app']['format']['poseidon']['title'] = '/(\[.*\])?\s?(.*?) (.*) Alarm (ACTIVATED|DEACTIVATED)/';
$config['app']['format']['poseidon']['variables'] = array('source' => 2, 'service' => 3, 'status' => 4);
$config['app']['format']['poseidon']['error'] = '/^ACTIVATED$/';

$config['app']['format']['rrcndb']['title'] = '/(\[.*\])?\s?(rrcndb) \[\d{4}-\d{2}-\d{2}\] (.*)/';
$config['app']['format']['rrcndb']['variables'] = array('source' => 2, 'service' => 2, 'status' => 3);
$config['app']['format']['rrcndb']['error'] = '/ERROR/';
?>