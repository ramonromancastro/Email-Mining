<?php
/*************************/
/* Configuración general */
/*************************/

$config['general']['unknown'] = '-';
$config['general']['refresh'] = 60;

/**********************************************/
/* Configuración de acceso a la base de datos */
/**********************************************/

$config['db']['host'] = '127.0.0.1';
$config['db']['db'] = 'emailmining';
$config['db']['username'] = 'emailmining';
$config['db']['password'] = 'p@ssw0rd';

/*************************************************/
/* Configuración de acceso al correo electrónico */
/*************************************************/

$config['mail']['hostname'] = '{imap.juntadeandalucia.es:993/imap/ssl}INBOX';
$config['mail']['username'] = 'emailmining@domain.com';
$config['mail']['password'] = 'p@ssw0rd';

/*************************/
/* Formatos de cabeceras */
/*************************/

/*
	Las variables que se deben extraer para cada uno de los formatos de cabecera son
		- source
		- service
		- status
	En el caso de que una variable no esté definida en la cabecera, se utilizará el valor
	de configuración $config['general']['unknown'].
*/

$config['format']['nagios_host']['title'] = '/(Nagios): Host (.*): (.*) \((.*)\)/';
$config['format']['nagios_host']['variables'] = array('source' => 2, 'status' => 4);
$config['format']['nagios_host']['error'] = '/(WARNING|CRITICAL|UNKNOWN)/';

$config['format']['nagios_service']['title'] = '/(Nagios): Service (.*): (.*)\/(.*) \((.*)\)/';
$config['format']['nagios_service']['variables'] = array('source' => 3, 'service' => 4, 'status' => 5);
$config['format']['nagios_service']['error'] = '/(WARNING|CRITICAL|UNKNOWN)/';

$config['format']['ipcam']['title'] = '/(\[.*\])?\s?(.*) - (Motion Detection Notification)/';
$config['format']['ipcam']['variables'] = array('source' => 1, 'service' => 2, 'status' => 2);
$config['format']['ipcam']['error'] = '/Motion Detection Notification/';

$config['format']['bacula_job']['title'] = '/(Bacula): .* (.*) of (.*)/';
$config['format']['bacula_job']['variables'] = array('source' => 1, 'service' => 3, 'status' => 2);
$config['format']['bacula_job']['error'] = '/ERROR/';

$config['format']['wsus']['title'] = '/(WSUS): (.*) de (\w*)/';
$config['format']['wsus']['variables'] = array('source' => 1, 'service' => 3, 'status' => 2);

$config['format']['poseidon']['title'] = '/(\[.*\])?\s?(.*?) (.*) Alarm (ACTIVATED|DEACTIVATED)/';
$config['format']['poseidon']['variables'] = array('source' => 1, 'service' => 2, 'status' => 3);
$config['format']['poseidon']['error'] = '/^ACTIVATED$/';

$config['format']['rrcndb']['title'] = '/(rrcndb) \[\d{4}-\d{2}-\d{2}\] (.*)/';
$config['format']['rrcndb']['variables'] = array('source' => 1, 'service' => 1, 'status' => 2);
$config['format']['rrcndb']['error'] = '/ERROR/';
?>