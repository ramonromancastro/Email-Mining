          <h1 class="page-header">Histórico</h1>
		  
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-6 placeholder">
<?php
	$top = 10;
	$stmt = mysql_report_top_source_alert($mysqli,10);
	graph_2dring_from_stmt($stmt,"Top $top Orígenes",'img-responsive');
	$stmt->close();
?>
              <h4>Top Orígenes</h4>
              <span class="text-muted">Los <?php echo $top; ?> origenes mas conflictivos</span>
            </div>
            <div class="col-xs-6 col-sm-6 placeholder">
<?php
	$top = 10;
	$stmt = mysql_report_top_service_alert($mysqli,10);
	graph_2dring_from_stmt($stmt,"Top $top Servicios",'img-responsive');
	$stmt->close();
?>
              <h4>Top Servicios</h4>
              <span class="text-muted">Los <?php echo $top; ?> servicios mas conflictivos</span>
            </div>
          </div>
		  <div class="row">
            <div class="col-xs-6 col-sm-6">
			  <h4>Orígenes más conflictivos</h4>
<?php
	$stmt = mysql_report_top_source_alert($mysqli);
	html_table_from_stmt($stmt,'table table-condensed table-striped small');
	$stmt->close();
?>
			</div>
			<div class="col-xs-6 col-sm-6">
			  <h4>Servicios más conflictivos</h4>
<?php
	$stmt = mysql_report_top_service_alert($mysqli);
	html_table_from_stmt($stmt,'table table-condensed table-striped small');
	$stmt->close();
?>
			</div>
          </div>