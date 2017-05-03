			<h1 class="page-header"><i class='fa fa-check fa-fw' aria-hidden='true' style='float:right;'></i>Alertas solucionada</h1>
			<div class="row">
				<div class="col-xs-12 col-sm-12">
<?php
	$uid=$_GET['uid'];

	$stmt = mysql_emails_acknowledge_email_affected($mysqli,$uid);
	purge_inbox_acknowledge_from_stmt($stmt,'uid');

	$stmt->close();
	mysql_emails_acknowledge_email($mysqli,$uid);
?>
					<p>La alerta se ha marcado como solucionada.</p>
					<p>Volver a la <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">página anterior<a>.</p>
				</div>
			</div>