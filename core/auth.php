<?php
$session = session_get_cookie_params();
session_set_cookie_params($session['lifetime'],$config['session']['cookie_path']);
session_start();

// Devuelve un string con el nombre del usuaario si el acceso se ha comprobado satisfactoriamente
// Devuelve NULL en cualquier otro caso
function login_ldap($username,$password){
	global $config;
	
	$result = false;

	//ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
	$ldapconn = @ldap_connect($config['session']['auth']['ldap']['host'],$config['session']['auth']['ldap']['port']);
	if ($ldapconn){
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, $config['session']['auth']['ldap']['protocol_version']);
		$ldapbind = @ldap_bind($ldapconn, sprintf($config['session']['auth']['ldap']['base_dn'], $username), $password);
		if ($ldapbind){
			$ldapresult = @ldap_read($ldapconn, sprintf($config['session']['auth']['ldap']['base_dn'], $username), '(objectclass=*)',array($config['session']['auth']['ldap']['uid'],$config['session']['auth']['ldap']['mail']));
			if ($ldapresult){
				$ldapentry = @ldap_get_entries($ldapconn, $ldapresult);
				if ($ldapentry){
					$_SESSION[$config['session']['uid']] = $ldapentry[0][$config['session']['auth']['ldap']['uid']][0];
					$_SESSION[$config['session']['mail']] = $ldapentry[0][$config['session']['auth']['ldap']['mail']][0];
					$result = true;
				}
			}
		}
		ldap_close($ldapconn);
	}

	return $result;
}

function login_user_name(){
	global $config;
	return $_SESSION[$config['session']['uid']];
}

function login_user_mail(){
	global $config;
	return $_SESSION[$config['session']['mail']];
}

function login_islogged(){
	global $config;
	return isset($_SESSION[$config['session']['uid']]);
}

function login_isactive(){
	global $config;
	return $config['session']['auth']['enable'];
}

function login_isneeded(){
	global $config;
	return (login_isactive() && !login_islogged());
}
?>