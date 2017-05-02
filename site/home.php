			<h1 class="page-header">Vistazo</h1>
			<div class="row">
				<div class="col-xs-3 col-sm-3">
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
				<div class="col-xs-3 col-sm-3">
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
<?php
	if ($app['general']['autosync']){
?>
				<div class="col-xs-3 col-sm-3">
					<div class="alert alert-success text-center">
						<?php echo (isset($app['runtime']['emails']))?"<span class='number'>".$app['runtime']['emails']."</span><span class='text'>correo(s) analizado(s) en la última sincronización</span>":"<span class='text'>No se han analizado correos en la última sincronización</span>"; ?>
					</div>
				</div>
				<div class="col-xs-3 col-sm-3">
					<div class="alert alert-<?php echo (isset($app['runtime']['unknown']))?"warning":"success"; ?> text-center">
						<?php echo (isset($app['runtime']['unknown']))?"<span class='number'>".count($app['runtime']['unknown'])."</span><span class='text'>correo(s) no minado(s) en la última sincronización</span>":"<span class='text'>No existen correos sin minar en la última sincronización</span>"; ?>
					</div>
				</div>
<?php
	}
?>
			</div>
			<h2 class="sub-header">Alertas activas</h2>
			<div class="table-responsive">
<?php
	$stmt = mysql_active_alerts($mysqli);
	$variables = array();
	$data = array();
	$hidden = array('uid', 'source_uid', 'service_uid');
	echo "<table class='table table-condensed table-striped small'>";
	$fields = $stmt->result_metadata();
	echo "<tr>";
	while ($finfo = $fields->fetch_field()) {
		if (!in_array($finfo->name,$hidden)) echo "<th>".$finfo->name."</th>";
		$variables[$finfo->name] = &$data[$finfo->name]; // pass by reference
	}
	echo "<th>Actions</th>";
	echo "</tr>";
	$fields->close();
	call_user_func_array(array($stmt, 'bind_result'), $variables);
	while($stmt->fetch()) {
		echo "<tr>";
		foreach ($variables as $key=>$value ) {
			if (!in_array($key,$hidden)){
				switch ($key) {
					case 'source':
						echo "<td><a href='?p=details.php&source_uid=".$variables['source_uid']."' title='[Detalles]'>$value</a></td>";
						break;
					case 'service':
						echo "<td><a href='?p=details.php&source_uid=".$variables['source_uid']."&service_uid=".$variables['service_uid']."' title='[Detalles]'>$value</a></td>";
						break;
					default:
					   echo "<td>$value</td>";
				}
			}
		}
		echo "<td>";
		echo "<a href='?p=email.php&uid=".$variables['uid']."'>[Read email]</a>";
		echo " <a href='?p=acknowledge.php&uid=".$variables['uid']."'>[Acknowledge]</a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	//html_table_from_stmt($stmt,'table table-condensed table-striped small');
	$stmt->close();
?>
			</div>
			<h2 class="sub-header">Emails no procesados</h2>
<?php
	if (isset($app['runtime']['unknown'])){
		foreach($app['runtime']['unknown'] as $line){
			echo "<p>$line</p>";
		}
	}
	else{
		echo "No existen correos sin minar en la última sincronización";
	}
?>