<?php
/*************************/
/* Configuración general */
/*************************/

$config['session']['cookie_path'] = '/path/to/installation/dir/';

$config['session']['auth']['enable'] = true; // Habilita/Deshabilita el acceso restringido
$config['session']['auth']['type'] = 'ldap'; // Metodo de autentificación (ad|ldap|mysql)

$config['session']['auth']['ad']['domain'] = 'domain.local';
$config['session']['auth']['ad']['port'] = 389;
$config['session']['auth']['ad']['dn'] = 'DC=domain,DC=local';
$config['session']['auth']['ad']['memberof'] = 'CN=Group,OU=Unit,DC=domain,DC=local';

$config['session']['auth']['ldap']['host'] = 'ldaps://ldap.domain.local';
$config['session']['auth']['ldap']['port'] = 636;
$config['session']['auth']['ldap']['dn'] = 'o=unit,o=domain,c=local';

$config['session']['auth']['mysql']['host'] = '127.0.0.1';
$config['session']['auth']['mysql']['dbname'] = 'dbname';
$config['session']['auth']['mysql']['dbtable'] = 'users';
$config['session']['auth']['mysql']['username'] = 'dbuser';
$config['session']['auth']['mysql']['password'] = 'dbpassword';
$config['session']['auth']['mysql']['field']['hash'] = 'MD5'; // Tipo de password en la base de datos (Null|MD5). Establecer a Null si está "en claro"
$config['session']['auth']['mysql']['field']['username'] = 'username'; // Nombre del campo que contiene el usuario
$config['session']['auth']['mysql']['field']['password'] = 'password'; // Nombre del campo que contiene la contraseña
$config['session']['auth']['mysql']['field']['displayname'] = 'username'; // Nombre del campo que contiene el nombre completo
$config['session']['auth']['mysql']['field']['mail'] = 'mail'; // Nombre de campo que contiene el correo electrónico

/***************************/
/* Configuración del login */
/***************************/

$config['login']['title'] = 'Application name';
$config['login']['subtitle'] = 'A short description';

/**********************************************/
/* Configuración de acceso a la base de datos */
/**********************************************/

$config['db']['host'] = '127.0.0.1';
$config['db']['db'] = 'emailmining';
$config['db']['username'] = 'emailmining';
$config['db']['password'] = 'p@ssw0rd';

?>