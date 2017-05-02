	<h1 class="page-header">Detalles</h1>
<?php
	$source_uid = isset($_GET['source_uid'])?$_GET['source_uid']:null;
	$service_uid = isset($_GET['service_uid'])?$_GET['service_uid']:null;
	$stats['Hoy'] = strtotime('today midnight');
	$stats['Los últimos 7 días'] = strtotime('-7 days midnight');
	$stats['Los últimos 30 días'] = strtotime('-30 days midnight');
	
	foreach ($stats as $key=>$value){
?>
		<h2 class="sub-header"><?php echo $key; ?></h2>
<?php
		$stmt = mysql_group_source_service_error($mysqli,$source_uid,$service_uid,$value);
		html_table_from_stmt($stmt,'table table-condensed table-striped small');
		$stmt->close();
	}
?>