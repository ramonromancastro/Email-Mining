<?php
$session = session_get_cookie_params();
session_set_cookie_params($session['lifetime'],$config['session']['cookie_path']);
session_start();

$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_OK;

function rrcphpbase_login_password_hash($password){
	global $config;
	
	$hash = $password;
	
	switch ($config['session']['auth']['mysql']['field']['hash']) {
		case 'MD5':
			return md5($hash);
	}
	
	return $hash;
}

function rrcphpbase_login_error(){
	global $config;
	return $config['runtime']['login']['error'];
}

function rrcphpbase_login_statusText(){
	global $config;
	
	switch ($config['runtime']['login']['error']) {
		case RRCPHPBASE_LOGIN_OK:
			return "Ok";
			break;
		case RRCPHPBASE_LOGIN_CANNOT_CONNECT:
		case RRCPHPBASE_LOGIN_CANNOT_BIND:
			return "Ha ocurrido un error intentando conectar con el servidor de autentificación";
			break;
		case RRCPHPBASE_LOGIN_INVALID_USERNAME_PASSWORD:
			return "Usuario y/o contraseña inválidos";
			break;
	}
}

function rrcphpbase_login_authText(){
	global $config;
	
	switch ($config['session']['auth']['type']) {
		case "ad":
			return "Active Directory";
			break;
		case "ldap":
			return "LDAP";
			break;
		case "mysql":
			return "MySQL";
			break;
	}
}

function rrcphpbase_login_ad($username,$password){
	global $config;
	
	$result = false;
	
	$filter = "(sAMAccountName=$username)";
	if ($config['session']['auth']['ad']['memberof']){
		$filter = "(&$filter(memberOf:1.2.840.113556.1.4.1941:=".$config['session']['auth']['ad']['memberof']."))";
	}

	//ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
	$ldapconn = @ldap_connect($config['session']['auth']['ad']['domain'],$config['session']['auth']['ad']['port']);
	if ($ldapconn){
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		$ldapbind = @ldap_bind($ldapconn, $username.'@'.$config['session']['auth']['ad']['domain'], $password);
		if ($ldapbind){
			$ldapresult = ldap_search($ldapconn, $config['session']['auth']['ad']['dn'],$filter,array('sAMAccountName','displayname','mail'));
			
			if ($ldapresult){
				$ldapentry = @ldap_get_entries($ldapconn, $ldapresult);
				if (($ldapentry) && ($ldapentry['count'])){
					$_SESSION['uid'] = $ldapentry[0]['samaccountname'][0];
					$_SESSION['name'] = $ldapentry[0]['displayname'][0];
					$_SESSION['mail'] = $ldapentry[0]['mail'][0];
					$result = true;
				}
				else{
					$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_INVALID_USERNAME_PASSWORD;
				}
			}
			else{
				$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_INVALID_USERNAME_PASSWORD;
			}
		}
		else{
			$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_CANNOT_BIND;
		}
		ldap_close($ldapconn);
	}
	else{
		$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_CANNOT_CONNECT;
	}

	return $result;
}

function rrcphpbase_login_ldap($username,$password){
	global $config;
	
	$result = false;
	$userdn = "uid=$username," . $config['session']['auth']['ldap']['dn'];

	//ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
	$ldapconn = @ldap_connect($config['session']['auth']['ldap']['host'],$config['session']['auth']['ldap']['port']);
	if ($ldapconn){
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		$ldapbind = @ldap_bind($ldapconn, $userdn, $password);
		if ($ldapbind){
			$ldapresult = ldap_search($ldapconn, $config['session']['auth']['ldap']['dn'], "(uid=$username)",array('uid','displayName','mail'));
			if ($ldapresult){
				$ldapentry = @ldap_get_entries($ldapconn, $ldapresult);
				if (($ldapentry) && ($ldapentry['count'])){
					$_SESSION['uid'] = $ldapentry[0]['uid'][0];
					$_SESSION['name'] = $ldapentry[0]['displayname'][0];
					$_SESSION['mail'] = $ldapentry[0]['mail'][0];
					$result = true;
				}
				else{
					$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_INVALID_USERNAME_PASSWORD;
				}
			}
			else{
				$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_INVALID_USERNAME_PASSWORD;
			}
		}
		else{
			$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_CANNOT_BIND;
		}
		ldap_close($ldapconn);
	}
	else{
		$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_CANNOT_CONNECT;
	}

	return $result;
}

function rrcphpbase_login_mysql($username,$password){
	global $config;
	
	$result = false;
	$mysqlconn = @mysqli_connect($config['session']['auth']['mysql']['host'], $config['session']['auth']['mysql']['username'], $config['session']['auth']['mysql']['password'], $config['session']['auth']['mysql']['dbname']);
	
	if ($mysqlconn){
		$query = "SELECT ".$config['session']['auth']['mysql']['field']['username'].",".$config['session']['auth']['mysql']['field']['displayname'].",".$config['session']['auth']['mysql']['field']['mail']." FROM ".$config['session']['auth']['mysql']['dbtable']." WHERE ".$config['session']['auth']['mysql']['field']['username']." = ? AND ".$config['session']['auth']['mysql']['field']['password']." = ?";
		if ($stmt = @mysqli_prepare($mysqlconn,$query)) {
			$password = rrcphpbase_login_password_hash($password);
			$stmt->bind_param("ss",$username,$password);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows() > 0){
				if ($stmt->bind_result($col1, $col2, $col3)){
					$stmt->fetch();
					$_SESSION['uid'] = $col1;
					$_SESSION['name'] = $col2;
					$_SESSION['mail'] = $col3;
					$result = true;
				}
				else{
					$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_INVALID_USERNAME_PASSWORD;
				}
			}
			else{
				$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_INVALID_USERNAME_PASSWORD;
			}
			$stmt->free_result();
			$stmt->close();
		}
		else{
			$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_CANNOT_BIND;
		}
	}
	else{
		$config['runtime']['login']['error'] = RRCPHPBASE_LOGIN_CANNOT_CONNECT;
	}

	return $result;
}

function rrcphpbase_login_user_name(){
	global $config;
	return $_SESSION['name'];
}

function rrcphpbase_login_user_id(){
	global $config;
	return $_SESSION['uid'];
}

function rrcphpbase_login_user_mail(){
	global $config;
	return $_SESSION['mail'];
}

function rrcphpbase_login_islogged(){
	global $config;
	return isset($_SESSION['uid']);
}

function rrcphpbase_login_islogout(){
	global $config;
	return isset($_GET['logout']);
}

function rrcphpbase_login_isactive(){
	global $config;
	return $config['session']['auth']['enable'];
}

function rrcphpbase_login_isneeded(){
	global $config;
	return (rrcphpbase_login_isactive() && !rrcphpbase_login_islogged());
}

function rrcphpbase_login_logout(){
	# http://php.net/manual/es/function.session-destroy.php
	$_SESSION = array();
	
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(),'',time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);
	}
	
	session_destroy();
}

function rrcphpbase_login_logoutPage(){
	echo "?logout";
}
?>