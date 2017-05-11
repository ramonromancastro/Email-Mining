<?php
/*************************/
/* Configuración general */
/*************************/

$config['session']['cookie_path'] = '/path/to/installation/dir/';

$config['session']['auth']['enable'] = true; // Habilita/Deshabilita el acceso restringido
$config['session']['auth']['type'] = 'ad'; // Metodo de autentificación (ad|ldap)

$config['session']['auth']['ad']['domain'] = 'domain.local';
$config['session']['auth']['ad']['port'] = 389;
$config['session']['auth']['ad']['dn'] = 'DC=domain,DC=local';
$config['session']['auth']['ad']['memberof'] = 'CN=Group,OU=Unit,DC=domain,DC=local';

$config['session']['auth']['ldap']['host'] = 'ldaps://ldap.domain.local';
$config['session']['auth']['ldap']['port'] = 636;
$config['session']['auth']['ldap']['dn'] = 'o=unit,o=domain,c=local';

/**********************************************/
/* Configuración de acceso a la base de datos */
/**********************************************/

$config['db']['host'] = '127.0.0.1';
$config['db']['db'] = 'emailmining';
$config['db']['username'] = 'emailmining';
$config['db']['password'] = 'p@ssw0rd';

?>