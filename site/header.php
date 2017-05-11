<?php
include 'modules/libchart/libchart/classes/libchart.php';
include 'modules/pchart/class/pData.class.php';
include 'modules/pchart/class/pDraw.class.php';
include 'modules/pchart/class/pImage.class.php';
include 'modules/pchart/class/pPie.class.php';
include 'modules/gravatar/gravatar.php';

include 'inc.functions.php';
include 'inc.mysql.php';
include 'inc.init.php';
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
	<link rel="shortcut icon" href="favicon.ico" />
	<title>Email Mining</title>
	<?php echo (rrcphpbase_is_home() && isset($config['app']['general']['refresh']))?"<meta http-equiv='refresh' content='".$config['app']['general']['refresh']."'>":""; ?>
	
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	
	<!-- Custom styles for this template -->
    <link href="site/css/dashboard.css" rel="stylesheet">
	<link href="site/css/custom.css" rel="stylesheet">
	
	<!-- Font Awesome -->
	<script src="https://use.fontawesome.com/e84de6e0f7.js"></script>

	
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
		<div id="navbar" class="navbar-collapse collapse">
		  <p class='navbar-text'>
		  <?php
			echo "<span class='label label-".(($config['app']['general']['autosync'])?'success':'default')."'><i title='Auto-sync ".(($config['app']['general']['autosync'])?'(habilitado)':'(deshabilitado)')."' class='fa fa-refresh fa-fw' aria-hidden='true'></i></span>&nbsp;";
            echo "<span class='label label-".(($config['app']['general']['autodelete'])?'success':'default')."'><i title='Auto-delete ".(($config['app']['general']['autodelete'])?'(habilitado)':'(deshabilitado)')."' class='fa fa-trash fa-fw' aria-hidden='true'></i></span>&nbsp;";
			echo "<span class='label label-".(($config['app']['general']['debug'])?'success':'default')."'><i title='Debug mode ".(($config['app']['general']['debug'])?'(habilitado)':'(deshabilitado)')."' class='fa fa-bug fa-fw' aria-hidden='true'></i></span>&nbsp;";
          ?>
		  </p>
		  <ul class="nav navbar-nav navbar-right">
			<li><a href="#">Dashboard</a></li>
			<li><a href="#">Settings</a></li>
			<li><a href="#">Profile</a></li>
			<li><a href="#">Help</a></li>
			<?php if (rrcphpbase_login_islogged()){ ?>
			<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo get_gravatar(rrcphpbase_login_user_mail(), 20); ?>"/> <?php echo rrcphpbase_login_user_name(); ?> <span class="caret"></span></a>
              <ul class="dropdown-menu">
			    <li class="dropdown-header"><?php echo rrcphpbase_login_authText(); ?></li>
				<li><a href="#"><i class="fa fa-user-circle-o fa-fw" aria-hidden="true"></i> <?php echo rrcphpbase_login_user_id(); ?></a></li>
				<li><a href="#"><i class="fa fa-envelope fa-fw" aria-hidden="true"></i> <?php echo rrcphpbase_login_user_mail(); ?></a></li>
				<li role="separator" class="divider"></li>
                <li><a href="<?php echo rrcphpbase_login_logoutPage(); ?>">Cerrar sesión</a></li>
              </ul>
            </li>
			<?php } ?>
		  </ul>
		  <form class="navbar-form navbar-right">
		    <input id="p" name="p" type="hidden" value="search.php">
            <input id="pattern" name="pattern" type="text" class="form-control" placeholder="Buscar...">
          </form>
        </div>
      </div>
    </nav>
	<div class="container-fluid">
      <div class="row">
        <!--<div class="col-sm-3 col-md-2 sidebar">-->
		<div class="sidebar">
          <ul class="nav nav-sidebar">
            <li><a href="?"><i title="Dashboard" class='fa fa-tachometer fa-2x' aria-hidden='true'></i></a></li>
            <li><a href="?p=history.php"><i title="Histórico" class='fa fa-history fa-2x' aria-hidden='true'></i></a></li>
			<li><a href="?p=inbox.php"><i title="Bandeja de entrada" class='fa fa-envelope-o fa-2x' aria-hidden='true'></i></a></li>
            <li><a href="?p=sync.php"><i title="Sincronizar ahora!" class='fa fa-refresh fa-2x' aria-hidden='true'></i></a></li>
          </ul>
          <div class="nav nav-sidebar">
			<!--<p class="navbar-text small alert alert-<?php echo ($config['app']['general']['autosync'])?'success':'warning';?>">Sincronización automática <?php echo ($config['app']['general']['autosync'])?'':'des';?>habilitada.<?php echo (!$config['app']['general']['autosync'])?" Pulsa <a href='?p=sync.php' title='Sincronizar ahora!'>aquí</a> para sincronizar manualmente.":""; ?></p>-->
          </div>
        </div>
        <!--<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">-->
		<div class="main">
<?php
	/* SINCRONIZACION AUTOMATICA DEL CORREO */
	if (rrcphpbase_is_home() && $config['app']['general']['autosync']) collect_mail();
?>