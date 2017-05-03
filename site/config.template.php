<?php
/*************************/
/* Configuración general */
/*************************/

$app['general']['ok'] = 'Ok'; // Texto por defecto para sustituir en los informes.
$app['general']['error'] = 'Error'; // Texto por defecto para sustituir en los informes.
$app['general']['unknown'] = '-'; // Texto por defecto para aquellos campos que no tengan valor.
$app['general']['nodata'] = 'No existe información disponible'; // Texto por defecto para los informes sin información disponible.
$app['general']['refresh'] = 60; // INtervalode segundos de actualización de la página de inicio. Establecer a NULL para desactivar.
$app['general']['autosync'] = false; // Sincroniza de manera automática con el correo electrónico cada vez que se visita la página de inicio.
$app['general']['autodelete'] = true; // Elimina los correos que no son de error del servidor mientras realiza la sincronización.
$app['general']['debug'] = true; // Vacía la base de datos y descarga todos los emails en cada sincronización, sin eliminar correos.

/**********************************************/
/* Configuración de acceso a la base de datos */
/**********************************************/

$app['db']['host'] = '127.0.0.1';
$app['db']['db'] = 'emailmining';
$app['db']['username'] = 'emailmining';
$app['db']['password'] = 'p@ssw0rd';

/*************************************************/
/* Configuración de acceso al correo electrónico */
/*************************************************/

$app['mail']['hostname'] = '{imap.domain.local:993/imap/ssl}INBOX';
$app['mail']['username'] = 'emailmining@domain.local';
$app['mail']['password'] = 'p@ssw0rd';

/*************************/
/* Formatos de cabeceras */
/*************************/

/*
	Las variables que se deben extraer para cada uno de los formatos de cabecera son
		- source
		- service
		- status
	En el caso de que una variable no esté definida en la cabecera, se utilizará el valor
	de configuración $app['general']['unknown'].
*/

$app['format']['nagios_host']['title'] = '/(\[.*\])?\s?(Nagios): Host (.*): (.*) \((.*)\)/';
$app['format']['nagios_host']['variables'] = array('source' => 3, 'status' => 5);
$app['format']['nagios_host']['error'] = '/(WARNING|CRITICAL|UNKNOWN)/';

$app['format']['nagios_service']['title'] = '/(\[.*\])?\s?(Nagios): Service (.*): (.*)\/(.*) \((.*)\)/';
$app['format']['nagios_service']['variables'] = array('source' => 4, 'service' => 5, 'status' => 6);
$app['format']['nagios_service']['error'] = '/(WARNING|CRITICAL|UNKNOWN)/';

$app['format']['ipcam']['title'] = '/(\[.*\])?\s?(.*) - (Motion Detection Notification)/';
$app['format']['ipcam']['variables'] = array('source' => 2, 'service' => 3, 'status' => 3);
$app['format']['ipcam']['error'] = '/Motion Detection Notification/';

$app['format']['bacula_job']['title'] = '/(\[.*\])?\s?(Bacula): .* (.*) of (.*)/';
$app['format']['bacula_job']['variables'] = array('source' => 2, 'service' => 4, 'status' => 3);
$app['format']['bacula_job']['error'] = '/ERROR/';

$app['format']['wsus']['title'] = '/(\[.*\])?\s?(WSUS): (.*) de (\w*)/';
$app['format']['wsus']['variables'] = array('source' => 2, 'service' => 4, 'status' => 3);

$app['format']['poseidon']['title'] = '/(\[.*\])?\s?(.*?) (.*) Alarm (ACTIVATED|DEACTIVATED)/';
$app['format']['poseidon']['variables'] = array('source' => 2, 'service' => 3, 'status' => 4);
$app['format']['poseidon']['error'] = '/^ACTIVATED$/';

$app['format']['rrcndb']['title'] = '/(\[.*\])?\s?(rrcndb) \[\d{4}-\d{2}-\d{2}\] (.*)/';
$app['format']['rrcndb']['variables'] = array('source' => 2, 'service' => 2, 'status' => 3);
$app['format']['rrcndb']['error'] = '/ERROR/';
?>