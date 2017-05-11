<?php
define('RRCPHPBASE_ROOT', getcwd());

include 'core/constants.php';
include 'config.php';
include 'core/functions.php';
include 'core/auth.php';
include 'site/config.php';

# Comprobamos si se desea una comporbación de requisitos

if (rrcphpbase_requirements_requested()){
	rrcphpbase_load_requirements();
	exit;
}

# Abrimos la conexión a la base de datos

rrcphpbase_db_open();

# Comprobamos si se desea cerrar la sesión

if (rrcphpbase_login_islogout()){
	rrcphpbase_login_logout();
}

# Comprobamos la necesidad de iniciar sesión

if (rrcphpbase_login_isneeded()){
	rrcphpbase_load_login();
}

# Comrpobamos si la sesión está iniciada o no necesita inicio de sesión

if (!rrcphpbase_login_isneeded() || rrcphpbase_login_islogged()){
	
	# Mostramos el contenido seleccionado
	rrcphpbase_calculate_page();
	rrcphpbase_load_content();
}

# Cerramos la conexión a la base de datos

rrcphpbase_db_close();
?>