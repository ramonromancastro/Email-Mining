			<h1 class="page-header">Vistazo</h1>
			<div class="row">
				<div class="col-xs-4 col-sm-4">
<?php
	$stmt = mysql_active_alerts($mysqli);
	$stmt->store_result(); ?>
					<div class="alert alert-<?php echo ($stmt->num_rows())?"danger":"success"; ?> text-center">
						<?php echo ($stmt->num_rows())?"<span class='number'>".$stmt->num_rows()."</span><span class='text'>alerta(s) activa(s)</span>":"<span class='text'>No hay alertas activas</span>"; ?>
					</div>
<?php
	$stmt->free_result();
	$stmt->close();
?>
				</div>
				<div class="col-xs-4 col-sm-4">
<?php
	$stmt = mysql_active_alerts_by_source($mysqli);
	$stmt->store_result(); ?>
					<div class="alert alert-<?php echo ($stmt->num_rows())?"danger":"success"; ?> text-center">
						<?php echo ($stmt->num_rows())?"<span class='number'>".$stmt->num_rows()."</span><span class='text'>origen(es) activo(s)</span>":"<span class='text'>No hay orígenes activos</span>"; ?>
					</div>
<?php
	$stmt->free_result();
	$stmt->close();
?>
				</div>
				<div class="col-xs-4 col-sm-4">
					<div class="alert alert-<?php echo (isset($config['debug']['emails']))?"warning":"success"; ?> text-center">
						<?php echo (isset($config['debug']['emails']))?"<span class='number'>".count($config['debug']['emails'])."</span><span class='text'>correo(s) no minado(s)</span>":"<span class='text'>No existen correos sin minar</span>"; ?>
					</div>
				</div>
			</div>
			<h2 class="sub-header">Alertas activas</h2>
			<div class="table-responsive">
<?php
	$stmt = mysql_active_alerts($mysqli);
	html_table_from_stmt($stmt,'table table-condensed table-striped small');
	$stmt->close();
?>
			</div>
			<h3>Emails no procesados</h3>
<?php
	foreach($config['debug']['emails'] as $line){
		echo "<p>$line</p>";
	}
?>