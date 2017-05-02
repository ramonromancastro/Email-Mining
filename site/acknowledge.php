			<h1 class="page-header">Alertas solucionada</h1>
			<div class="row">
				<div class="col-xs-12 col-sm-12">
<?php
	$uid=$_GET['uid'];
	mysql_acknowledge_email($mysqli,$uid);
	mail_delete($uid);
?>
					<p>La alerta se ha marcado como solucionada.</p>
					<p>Volver a la <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">página anterior<a>.</p>
				</div>
			</div>