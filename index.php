<?php
define('SKEL_ROOT', getcwd());

include 'core/constants.php';
include 'config.php';
include 'core/functions.php';
include 'core/auth.php';
include 'site/config.php';

# Abrimos la conexión a la base de datos

skel_db_open();

# Comprobamos si se desea cerrar la sesión

if (login_islogout()){
	login_logout();
}

# Comprobamos la necesidad de iniciar sesión

if (login_isneeded()){
	skel_load_login();
}

# Comrpobamos si la sesión está iniciada o no necesita inicio de sesión

if (!login_isneeded() || login_islogged()){
	
	# Mostramos el contenido seleccionado
	skel_calculate_page();
	skel_load_content();
}

# Cerramos la conexión a la base de datos

skel_db_close();
?>