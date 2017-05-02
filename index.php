<?php
define('SKEL_ROOT', getcwd());

include 'core/constants.php';
include 'config.php';
include 'core/functions.php';
include 'includes.php';
include 'site/config.php';

skel_db_open();
skel_calculate_page();
skel_load_content();
skel_db_close();
?>