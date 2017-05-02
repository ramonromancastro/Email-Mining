<?php
global $app;

include 'modules/libchart/libchart/classes/libchart.php';
include 'modules/pchart/class/pData.class.php';
include 'modules/pchart/class/pDraw.class.php';
include 'modules/pchart/class/pImage.class.php';
include 'modules/pchart/class/pPie.class.php';

include 'functions.php';

/******************************************************/
/* RECOLECTAMOS LOS EMAILS */
/******************************************************/

if ($app['general']['autosync']) collect_mail();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Panel de control del Área de Sistemas del servicio de Tecnología y Sistemas de la Agencia Andaluza de Instituciones Culturales">
    <meta name="author" content="Ramon Roman Castro <ramonromancastro@gmail.com>">
	<link rel="icon" href="favicon.ico">
	<title>Email Mining</title>
	<?php echo (skel_is_home() && isset($app['general']['refresh']))?"<meta http-equiv='refresh' content='".$app['general']['refresh']."'>":""; ?>
	
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	
	<!-- Custom styles for this template -->
    <link href="site/css/dashboard.css" rel="stylesheet">
	<link href="site/css/custom.css" rel="stylesheet">
	
	<!-- Load fonts -->
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img class="pull-left" title="[Logo]" alt="[Logo]" src="site/images/logo.png"/><span class="pull-left name">Email Mining</span></a>
        </div>
        <!--
		<div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="#">Profile</a></li>
            <li><a href="#">Help</a></li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>
        </div>
		-->
      </div>
    </nav>
	<div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li><a href="?">Vistazo</a></li>
            <li><a href="?p=history.php">Histórico</a></li>
          </ul>
          <ul class="nav nav-sidebar">
			<li><a href='#'><span class="label label-<?php echo ($app['general']['autosync'])?'success':'danger';?>">Sincronización automática</span></a></li>
            <li><a href="?p=sync.php">Sincronizar ahora!</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">