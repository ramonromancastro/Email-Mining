          <h1 class="page-header">Overview</h1>
		  
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-6 placeholder">
<?php
	$top = 10;
	$result = mysql_report_top_source_alert($mysqli,10);
	pie_chart_from_mysql_result($result, '', 512, 320, 'img-responsive');
	$result->close();
?>
              <h4>Top Sources</h4>
              <span class="text-muted">Los <?php echo $top; ?> origenes mas conflictivos</span>
            </div>
            <div class="col-xs-6 col-sm-6 placeholder">
<?php
	$top = 10;
	$result = mysql_report_top_service_alert($mysqli,10);
	pie_chart_from_mysql_result($result, '', 512, 320, 'img-responsive');
	$result->close();
	
	$result = mysql_source_service_error($mysqli,'comerzzia','Tomcat.comerzzia.status');
	pie_chart_from_mysql_stmt($result, '', 800, 320, 'img-responsive');
	$result->close();
?>
              <h4>Top Services</h4>
              <span class="text-muted">Los <?php echo $top; ?> servicio mas conflictivos</span>
            </div>
          </div>
		  
          <h2 class="sub-header">Alertas activas</h2>
          <div class="table-responsive">
<?php
	$result = mysql_active_alerts($mysqli);
	html_table_from_mysql_result($result,'table table-condensed table-striped small');
	$result->close();
?>
          </div>