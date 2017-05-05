<?php
define('SKEL_ROOT', getcwd());

include 'core/constants.php';
include 'config.php';
include 'core/functions.php';
include 'core/auth.php';
include 'site/config.php';

skel_db_open();
if (login_isneeded()){
	skel_load_login();
}
if (!login_isneeded() || login_islogged()){
	skel_calculate_page();
	skel_load_content();
}
skel_db_close();
?>