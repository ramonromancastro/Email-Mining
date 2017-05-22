<?php
function rrcphpbase_is_home(){
	global $runtime;
	
	return ($runtime['page'] == 'home.php');
}

function rrcphpbase_db_open(){
	global $config,$mysqli;
	
	$mysqli = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);
	if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_errno);
		exit();
	}
}

function rrcphpbase_db_close(){
	global $mysqli;
	
	$mysqli->close();
}

function rrcphpbase_calculate_page(){
	global $runtime;

	$runtime['page'] = 'home.php';
	if(isset($_GET['p']) && !empty($_GET['p'])){
		if ($_GET['p'] != basename(__FILE__)){
			$runtime['page'] = $_GET['p'];
			$page_path = realpath(RRCPHPBASE_ROOT.'/site/'.$runtime['page']);
			if (empty($page_path) || strpos($page_path,RRCPHPBASE_ROOT) === false || !is_file($page_path)){
				$runtime['page'] = '404.php';
			}
		}
	}
}

function rrcphpbase_load_content(){
	global $runtime,$mysqli,$config;
	
	include 'site/header.php';
	include 'site/'.$runtime['page'];
	include 'site/footer.php';
}

function rrcphpbase_load_login(){
	global $runtime,$mysqli,$config;
	
	include 'core/login.php';
}

function rrcphpbase_requirements_requested(){
	return isset($_GET['requirements']);
	
}

function rrcphpbase_load_requirements(){
	global $runtime,$mysqli,$config;
	
	include 'core/requirements.php';
}
?>