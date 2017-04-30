<?php
define('SKEL_ROOT', getcwd());

include 'config.php';
include 'includes.php';
include 'core/constants.php';
include 'core/functions.php';

skel_db_open();
skel_calculate_page();
skel_load_content();
skel_db_close();
?>