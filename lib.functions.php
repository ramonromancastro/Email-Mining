<?php

function mysql_active_alerts_by_source($mysqli){	
	$sql = 'SELECT source, COUNT(*) as total'.
			' FROM (SELECT source, service, FROM_UNIXTIME(MAX(timestamp)) as timestamp'.
			' FROM emails'.
			' WHERE error AND NOT EXISTS ('.
				' SELECT * FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND NOT error AND Oks.timestamp > emails.timestamp'.
			' )'.
			' GROUP BY source, service) as TempTable'.
			' GROUP BY source'.
			' ORDER BY total DESC';
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_active_alerts($mysqli){	
	$sql = 'SELECT source, service, status, FROM_UNIXTIME(timestamp) as timestamp'.
			' FROM emails'.
			' WHERE error AND NOT EXISTS ('.
				' SELECT * FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND NOT error AND Oks.timestamp > emails.timestamp'.
			' ) AND '.
			' timestamp >= ('.
				' SELECT MAX(timestamp) FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND error'.
			' )'.
			' ORDER BY timestamp DESC';
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_source_service_error($mysqli,$source,$service){
	global $config;
	$sql = "SELECT CASE error WHEN 1 THEN '".$config['general']['ok']."' ELSE '".$config['general']['error']."' END as valor, COUNT(*) as total".
			" FROM emails".
			" WHERE source=? AND service=?".
			" GROUP BY source, service, error";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("ss",$source,$service);
		$stmt->execute();
	}
	return $stmt;
}

function mysql_report_top_source_alert($mysqli,$top=0){
	$sql = "SELECT source,COUNT(*) as total FROM emails WHERE error GROUP BY source ORDER BY total DESC";
	if ($top)
		$sql .= " LIMIT 0,$top";
	
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_report_top_service_alert($mysqli,$top=0){
	$sql = "SELECT CONCAT(source,' / ',service) as service,COUNT(*) as total FROM emails WHERE error GROUP BY source, service ORDER BY total DESC";
	if ($top)
		$sql .= " LIMIT 0,$top";
	
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_truncate_events($mysqli){	
	$mysqli->query('TRUNCATE TABLE emails');
}

function mysql_insert_event($mysqli, $source, $service, $status, $timestamp, $error){
	if ($stmt = $mysqli->prepare("INSERT IGNORE INTO text_uid (text) VALUES (?)")) {
		$stmt->bind_param("s",$source);
		$stmt->execute();
		$stmt->close();
	}
	else{
		echo "$mysqli->error";
	}
	
	if ($stmt = $mysqli->prepare("INSERT IGNORE INTO text_uid (text) VALUES (?)")) {
		$stmt->bind_param("s",$service);
		$stmt->execute();
		$stmt->close();
	}
	else{
		echo "$mysqli->error";
	}

	if ($stmt = $mysqli->prepare("INSERT INTO emails (source, service, status, timestamp, error, source_uid, service_uid) (SELECT ?,?,?,?,?,(SELECT id_text_uid FROM text_uid WHERE text = ?),(SELECT id_text_uid FROM text_uid WHERE text = ?))")) {
		$stmt->bind_param("sssiiss",$source,$service,$status,$timestamp,$error,$source,$service);
		$stmt->execute();
		echo "$mysqli->error";
		$stmt->close();
	}
	else{
		echo "$mysqli->error";
	}
}

function graph_2dring_from_stmt($stmt,$title='2D Ring Chart',$class=''){
	global $config;
	
	include_once("pchart/class/pData.class.php");
	include_once("pchart/class/pDraw.class.php");
	include_once("pchart/class/pImage.class.php");
	include_once("pchart/class/pPie.class.php"); 

	$myData = new pData();
	
	$stmt->store_result();
	if( $stmt->num_rows() > 0){
		$stmt->bind_result($col1, $col2);
		while( $stmt->fetch() ){
			$labels[] = "$col1 ($col2)";
			$values[] = $col2;
		}
		$myData->addPoints($labels,'labels');
		$myData->addPoints($values,'values');
	}
	$myData->setAbscissa("labels"); 
	$stmt->free_result();
	
	$myPicture = new pImage(640,480,$myData);
	$myPicture->drawRectangle(0,0,639,479,array("R"=>0,"G"=>0,"B"=>0)); 
	$myPicture->drawGradientArea(0,0,639,40,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100)); 
	$myPicture->setFontProperties(array("FontName"=>"pchart/fonts/verdana.ttf","FontSize"=>14)); 
	$myPicture->drawText(10,30,$title,array("R"=>255,"G"=>255,"B"=>255)); 
	$myPicture->setFontProperties(array("FontName"=>"pchart/fonts/verdana.ttf","FontSize"=>10));
	if (isset($values)) {
		$PieChart = new pPie($myPicture,$myData);
		$PieChart->draw2DRing(320,240,array("OuterRadius"=>120,"InnerRadius"=>60,"WriteValues"=>TRUE,"ValuePosition"=>PIE_VALUE_INSIDE,"DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"Border"=>TRUE));
	}
	else
		$myPicture->drawText(320,240,"No hay información disponible",array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE));

	// $myPicture = new pImage(512,320,$myData);	
	// $myPicture->drawRectangle(0,0,511,319,array("R"=>0,"G"=>0,"B"=>0)); 
	// $myPicture->drawGradientArea(0,0,511,40,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100)); 
	// $myPicture->setFontProperties(array("FontName"=>"pchart/fonts/Forgotte.ttf","FontSize"=>14)); 
	// $myPicture->drawText(10,23,$title,array("R"=>255,"G"=>255,"B"=>255)); 
	// $myPicture->setFontProperties(array("FontName"=>"pchart/fonts/Forgotte.ttf","FontSize"=>11));
	// $PieChart = new pPie($myPicture,$myData);
	// $PieChart->draw2DRing(256,160,array("WriteValues"=>TRUE,"ValuePosition"=>PIE_VALUE_INSIDE,"DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"Border"=>TRUE));
	$myPicture->setShadow(FALSE);
	//$PieChart->drawPieLegend(20,60,array("Alpha"=>20));
	$filename = uniqid(rand(), true);
	$myPicture->Render($config['graph']['path']."/$filename.png");
	echo "<img class='$class' alt='$title' src='".$config['graph']['path']."/$filename.png'/>";
}

function pie_chart_from_mysql_stmt($stmt, $title='Pie Chart', $x=320, $y=200, $class=''){
	global $config;
	
	$chart = new PieChart( $x, $y );
	$dataSet = new XYDataSet();
	$stmt->store_result();
	if( $stmt->num_rows() > 0){
		$stmt->bind_result($col1, $col2);
		while( $stmt->fetch() ){
			$dataSet->addPoint(new Point($col1 . " ($col2)", $col2));
		}
		$chart->setDataSet($dataSet);
		$chart->setTitle($title);
		$filename = uniqid(rand(), true);
		$chart->render($config['graph']['path']."/$filename.png");
		echo "<img class='$class' alt='$title' src='".$config['graph']['path']."/$filename.png'/>";
	}
	$stmt->free_result();
}

function pie_chart_from_mysql_result($result, $title='Pie Chart', $x=320, $y=200, $class=''){
	global $config;
	
	$chart = new PieChart( $x, $y );
	$dataSet = new XYDataSet();
	if( $result->num_rows > 0){
		while( $row = $result->fetch_row() ){
			$dataSet->addPoint(new Point($row[0] . " ($row[1])", $row[1]));
		}
		$chart->setDataSet($dataSet);
		$chart->setTitle($title);
		$filename = uniqid(rand(), true);
		$chart->render($config['graph']['path']."/$filename.png");
		echo "<img class='$class' alt='$title' src='".$config['graph']['path']."/$filename.png'/>";
	}
}

function html_table_from_stmt($stmt,$class){
	$variables = array();
	$data = array();
	
	echo "<table class='$class'>";
	$fields = $stmt->result_metadata();
	echo "<tr>";
	while ($finfo = $fields->fetch_field()) {
		echo "<th>".$finfo->name."</th>";
		$variables[] = &$data[$finfo->name]; // pass by reference
	}
	echo "</tr>";
	$fields->close();
	
	call_user_func_array(array($stmt, 'bind_result'), $variables);
	
	while($stmt->fetch()) {
		echo "<tr>";
		foreach ($variables as $value ) {
			echo "<td>$value</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

function html_table_from_mysql_result($result,$class){
	echo "<table class='$class'>";
	echo "<tr>";
	while ($finfo = $result->fetch_field()) {
		echo "<th>".$finfo->name."</th>";
	}
	echo "</tr>";
	while ($row = $result->fetch_row()){
		echo "<tr>";
		foreach ($row as $value ) {
			echo "<td>$value</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

function collect_mail(){
	global $config, $mysqli;
	
	mysql_truncate_events($mysqli);
	echo "$mysqli->error";
	
	$inbox = imap_open($config['mail']['hostname'],$config['mail']['username'],$config['mail']['password']) or die('Cannot connect to IMAP: ' . imap_last_error());
	$emails = imap_search($inbox,'ALL');

	if($emails) {
		foreach($emails as $email_number) {
			$overview = imap_fetch_overview($inbox,$email_number,0);
			$title = imap_utf8($overview[0]->subject);
			foreach ($config['format'] as $expresion){
				$found = preg_match($expresion['title'], $title, $values);
				if ($found){
					$source = (array_key_exists('source',$expresion['variables']))?$values[$expresion['variables']['source']]:$config['general']['unknown'];
					$service = (array_key_exists('service',$expresion['variables']))?$values[$expresion['variables']['service']]:$config['general']['unknown'];
					$status = (array_key_exists('status',$expresion['variables']))?$values[$expresion['variables']['status']]:$config['general']['unknown'];
					$timestamp = strtotime($overview[0]->date);
					$error=0;
					if (array_key_exists('error',$expresion)){
						$error = preg_match($expresion['error'], $status);		
					}
					// echo "<p>-------------------------------------------------------</p>";
					// echo "<p>Source: $source</p>";
					// echo "<p>Service: $service</p>";
					// echo "<p>Status: $status</p>";
					// echo "<p>Timestamp: $timestamp</p>";
					// echo "<p>-------------------------------------------------------</p>";
					mysql_insert_event($mysqli,$source,$service,$status,$timestamp,$error);
					break;
				}
			}
			if (!$found) $config['debug']['emails'][] = $overview[0]->date." :: $title";
		}
	} 
	imap_close($inbox);
}
?>