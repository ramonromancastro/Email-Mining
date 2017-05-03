			<h1 class="page-header"><i class='fa fa-dashboard fa-fw' aria-hidden='true' style='float:right;'></i>Dashboard</h1>
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
<?php
	if ($app['general']['autosync']){
?>
				<div class="col-xs-4 col-sm-4">
					<div class="alert alert-success text-center">
						<?php echo "<span class='number'>".$app['runtime']['sync']['analyzed']."</span><span class='text'> correo(s) analizado(s)</span>"; ?>
					</div>
				</div>
<?php
	}
?>
			</div>
			<h2 class="sub-header">Alertas activas</h2>
			<div class="table-responsive">
				<form action='index.php'>
					<input type="hidden" name="p" value="acknowledge.php">
<?php
	$stmt = mysql_active_alerts($mysqli);
	$variables = array();
	$data = array();
	$hidden = array('uid', 'source_uid', 'service_uid');
	echo "<table class='table table-condensed table-striped small'>";
	$fields = $stmt->result_metadata();
	echo "<tr>";
	echo "<th>#</th>";
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
		echo "<td><input type='checkbox' name='uid[]' value='".$variables['uid']."'/></td>";
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
		echo "<a href='?p=email.php&uid=".$variables['uid']."'><i title='[Read email]' class='fa fa-envelope-o fa-fw' aria-hidden='true'></i></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<p><strong>Para todos los elementos seleccionados:</strong></p>";
	echo "<p><input class='btn btn-default' type='submit' value='Marcar como solucionado''></p>";
	//html_table_from_stmt($stmt,'table table-condensed table-striped small');
	$stmt->close();
?>
				<form>
			</div>