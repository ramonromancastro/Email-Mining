          <h1 class="page-header">Origen / Servicio</h1>
		  
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-6 placeholder">
<?php
	$top = 10;
	$stmt = mysql_report_top_source_alert($mysqli,10);
	graph_2dring_from_stmt($stmt,"Top $top Sources",'img-responsive');
	$stmt->close();
?>
              <h4>Top Sources</h4>
              <span class="text-muted">Los <?php echo $top; ?> origenes mas conflictivos</span>
            </div>
            <div class="col-xs-6 col-sm-6 placeholder">
<?php
	$top = 10;
	$stmt = mysql_report_top_service_alert($mysqli,10);
	graph_2dring_from_stmt($stmt,"Top $top Services",'img-responsive');
	$stmt->close();
	
	$stmt = mysql_source_service_error($mysqli,'comerddzzia','Tomcat.comerzzia.status');
	graph_2dring_from_stmt($stmt,'comerzzia / Tomcat.comerzzia.status','img-responsive');
	$stmt->close();
	
	
?>
              <h4>Top Services</h4>
              <span class="text-muted">Los <?php echo $top; ?> servicio mas conflictivos</span>
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