<?php
include 'version.php';
include 'config.php';
include 'lib.functions.php';
include 'libchart/libchart/classes/libchart.php';

/******************************************************/
/* CONECTAR CON LA BASE DE DATOS */
/******************************************************/

$mysqli = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);
if ($mysqli->connect_errno) {
	printf("Connect failed: %s\n", $mysqli->connect_errno);
	exit();
}

/******************************************************/
/* RECOLECTAMOS LOS EMAILS */
/******************************************************/

collect_mail();

/******************************************************/
/* SELECCIONAMOS LA PAGINA ACTUAL */
/******************************************************/

$realpath=dirname(realpath(__FILE__));
$page = 'overview.php';
if(isset($_GET['p']) && !empty($_GET['p'])){
	if ($_GET['p'] == basename(__FILE__)){
		$page = 'overview.php';
	}
	else{
		$page = $_GET['p'];
		$page_path = realpath("$realpath/$page");
		if (empty($page_path) || strpos($page_path,$realpath) === false || !is_file($page_path)){
			$page = '404.php';
		}
	}
}

/******************************************************/
/* ELIMINAMOS LOS GRAFICOS ANTERIORES */
/******************************************************/

$files = glob($config['graph']['path'].'/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

/******************************************************/
/* CARGAMOS EL CONTENIDO */
/******************************************************/

include 'html.header.php';
include $page;
include 'html.footer.php';

/******************************************************/
/* DESCONECTAR LA BASE DE DATOS */
/******************************************************/

$mysqli->close();
?>