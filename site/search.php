<?php
	$pattern = "%{$_GET['pattern']}%";
?>
          <h1 class="page-header"><i class='fa fa-search fa-fw' aria-hidden='true' style='float:right;'></i>Buscar...</h1>
		  <h2 class="sub-header">Resultado de la búsqueda</h2>
          <div class="row">
			<div class="col-xs-12 col-sm-12">
            <ol>
<?php
	$stmt = mysql_search_sources_services($mysqli,$pattern);

	$variables = array();
	$data = array();

	$stmt->store_result();
	if ($stmt->num_rows){
		$fields = $stmt->result_metadata();
		while ($finfo = $fields->fetch_field()) {
			$variables[$finfo->name] = &$data[$finfo->name]; // pass by reference
		}
		$fields->close();
		call_user_func_array(array($stmt, 'bind_result'), $variables);
		while($stmt->fetch()) {
			echo "<li>";
			if (isset($variables['service_uid']))
				echo "<a href='?p=details.php&source_uid={$variables['source_uid']}&service_uid={$variables['service_uid']}'>{$variables['source']} / {$variables['service']}</a>";
			else
				echo "<a href='?p=details.php&source_uid={$variables['source_uid']}'>{$variables['source']}</a>";
			echo "</li>";
		}
	}
	else
		echo "<p>".$app['general']['nodata']."</p>";
	$stmt->close();
?>
			</ol>
			</div>
          </div>