<?php

function mysql_active_alerts($mysqli){	
	$sql = 'SELECT source, service, FROM_UNIXTIME(MAX(timestamp)) as timestamp'.
			' FROM emails'.
			' WHERE error AND NOT EXISTS ('.
				' SELECT * FROM emails as Oks'.
				' WHERE Oks.source = emails.source AND Oks.service = emails.service AND NOT error AND Oks.timestamp > emails.timestamp'.
			' )'.
			' GROUP BY source, service'.
			' ORDER BY timestamp DESC';
	return $mysqli->query($sql);
}

function mysql_source_service_error($mysqli,$source,$service){
	$sql = "SELECT CONCAT(source,'.',service) as valor, COUNT(*) as total".
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
	if ($top)
		return $mysqli->query("SELECT source,COUNT(*) as total FROM emails WHERE error GROUP BY source ORDER BY total DESC LIMIT 0,$top");
	else
		return $mysqli->query("SELECT source,COUNT(*) as total FROM emails WHERE error GROUP BY source ORDER BY total DESC");
}

function mysql_report_top_service_alert($mysqli,$top=0){
	if ($top)
		return $mysqli->query("SELECT CONCAT(source,'.',service) as service,COUNT(*) as total FROM emails WHERE error GROUP BY source, service ORDER BY total DESC LIMIT 0,$top");
	else
		return $mysqli->query("SELECT CONCAT(source,'.',service) as service,COUNT(*) as total FROM emails WHERE error GROUP BY source, service ORDER BY total DESC");
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

function pie_chart_from_mysql_stmt($result, $title='Pie Chart', $x=320, $y=200, $class=''){
	$chart = new PieChart( $x, $y );
	$dataSet = new XYDataSet();
	if( $result->num_rows() > 0){
		$result->bind_result($col1, $col2);
		while( $result->fetch() ){
			$dataSet->addPoint(new Point($col1 . " ($col2)", $col2));
		}
		$chart->setDataSet($dataSet);
		$chart->setTitle($title);
		$filename = uniqid(rand(), true);
		$chart->render("generated/$filename.png");
		echo "<img class='$class' alt='$title' src='generated/$filename.png'/>";
	}
}

function pie_chart_from_mysql_result($result, $title='Pie Chart', $x=320, $y=200, $class=''){
	$chart = new PieChart( $x, $y );
	$dataSet = new XYDataSet();
	if( $result->num_rows > 0){
		while( $row = $result->fetch_row() ){
			$dataSet->addPoint(new Point($row[0] . " ($row[1])", $row[1]));
		}
		$chart->setDataSet($dataSet);
		$chart->setTitle($title);
		$filename = uniqid(rand(), true);
		$chart->render("generated/$filename.png");
		echo "<img class='$class' alt='$title' src='generated/$filename.png'/>";
	}
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
		}
	} 
	imap_close($inbox);
}
?>