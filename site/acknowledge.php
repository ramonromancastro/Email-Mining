			<h1 class="page-header"><i class='fa fa-check fa-fw' aria-hidden='true' style='float:right;'></i>Alertas solucionada</h1>
			<div class="row">
				<div class="col-xs-12 col-sm-12">
<?php
	$uid=$_GET['uid'];
	if (!empty($uid)){
		foreach ($uid as $value){
			$stmt = mysql_emails_acknowledge_email_affected($mysqli,$value);
			purge_inbox_acknowledge_from_stmt($stmt,'uid');
			$stmt->close();
			mysql_emails_acknowledge_email($mysqli,$value);
			echo "<p>La alerta [$value] y sus descendientes se han marcado como solucionadas.</p>";
		}
	}
?>
					<p>Volver a la <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">página anterior<a>.</p>
				</div>
			</div>