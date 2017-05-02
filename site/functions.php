<?php

function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
} 

function strokeToBase64($pImage)
{
   ob_start();
   imagepng($pImage->Picture);
   $contents =  ob_get_contents();
   ob_end_clean();
   return base64_encode($contents);
}

function mysql_active_alerts_by_source($mysqli){	
	$sql = 'SELECT source, COUNT(*) as total'.
			' FROM (SELECT source, service, FROM_UNIXTIME(MAX(timestamp)) as timestamp'.
			' FROM emails'.
			' WHERE error AND NOT acknowledge AND NOT EXISTS ('.
				' SELECT * FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND NOT error AND Oks.timestamp > emails.timestamp'.
			' )'.
			' GROUP BY source, service) as TempTable'.
			' GROUP BY source'.
			' ORDER BY total DESC';
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_sources($mysqli){	
	$sql = 'SELECT DISTINCT source FROM emails ORDER BY source';
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_sources_services($mysqli){	
	$sql = 'SELECT DISTINCT source, service FROM emails ORDER BY source, service';
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_active_alerts($mysqli){	
	$sql = 'SELECT uid, source, service, source_uid, service_uid, status, FROM_UNIXTIME(timestamp) as timestamp'.
			' FROM emails'.
			' WHERE error AND NOT acknowledge AND NOT EXISTS ('.
				' SELECT * FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND NOT error AND Oks.timestamp > emails.timestamp'.
			' ) AND '.
			' timestamp >= ('.
				' SELECT MAX(timestamp) FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND error AND NOT acknowledge'.
			' )'.
			' ORDER BY timestamp DESC';
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_acknowledge_email($mysqli,$uid){
	$sql = "UPDATE emails SET acknowledge=1 WHERE uid=?";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("i",$uid);
		$stmt->execute();
	}

	$sql = "UPDATE emails SET acknowledge = 1 WHERE EXISTS (SELECT * FROM (SELECT * FROM emails WHERE acknowledge) as ack WHERE source_uid = emails.source_uid AND service_uid = emails.service_uid AND status = emails.status AND timestamp > emails.timestamp)";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->execute();
	}
}

function mysql_group_source_service_error($mysqli,$source_uid=null,$service_uid=null,$timestamp=null){
	$params[0] = '';
	$sql = "SELECT source, service, SUM(CASE error WHEN 0 THEN 1 ELSE 0 END) as ok, SUM(error) as error FROM emails";
	if ($source_uid){
		$sqlWhere[] = "source_uid=?";
		$params[0] .= 'i';
		$params[] = $source_uid;
	}
	if ($service_uid){
		$sqlWhere[] = "service_uid=?";
		$params[0] .= 'i';
		$params[] = $service_uid;
	}
	if ($timestamp){
		$sqlWhere[] = "timestamp>=?";
		$params[0] .= 'i';
		$params[] = $timestamp;
	}
	if ($sqlWhere){
		$sql .= ' WHERE '.join($sqlWhere,' AND ');
	}
	$sql .= " GROUP BY source, service ORDER BY source, service";
	if ($stmt = $mysqli->prepare($sql)) {
		call_user_func_array(array($stmt, 'bind_param'), refValues($params));
		$stmt->execute();
	}
	echo $mysqli->error;
	return $stmt;
}

function mysql_source_service_error($mysqli,$source_uid,$service_uid){
	global $app;
	$sql = "SELECT CASE error WHEN 0 THEN ? ELSE ? END as valor, COUNT(*) as total".
			" FROM emails".
			" WHERE source_uid=? AND service_uid=?".
			" GROUP BY source, service, error";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("ssss",$app['general']['ok'],$app['general']['error'],$source_uid,$service_uid);
		$stmt->execute();
	}
	return $stmt;
}

function mysql_emails_distinct_source_service($mysqli,$source_uid=null,$service_uid=null,$timestamp=null){
	$params[0] = '';
	$sql = "SELECT DISTINCT source_uid, service_uid, source, service FROM emails";
	if ($source_uid){
		$sqlWhere[] = "source_uid=?";
		$params[0] .= 'i';
		$params[] = $source_uid;
	}
	if ($service_uid){
		$sqlWhere[] = "service_uid=?";
		$params[0] .= 'i';
		$params[] = $service_uid;
	}
	if ($timestamp){
		$sqlWhere[] = "timestamp>=?";
		$params[0] .= 'i';
		$params[] = $timestamp;
	}
	if ($sqlWhere){
		$sql .= ' WHERE '.join($sqlWhere,' AND ');
	}
	$sql .= " ORDER BY source, service";
	if ($stmt = $mysqli->prepare($sql)) {
		call_user_func_array(array($stmt, 'bind_param'), refValues($params));
		$stmt->execute();
	}
	echo $mysqli->error;
	return $stmt;
}

function mysql_services_per_source($mysqli,$source){
	$sql = "SELECT DISTINCT source, service FROM emails WHERE source=? ORDER BY source, service";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("s",$source);
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

function mysql_insert_event($mysqli, $source, $service, $status, $timestamp, $error, $uid){
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

	if ($stmt = $mysqli->prepare("INSERT INTO emails (source, service, status, timestamp, error, source_uid, service_uid, uid) (SELECT ?,?,?,?,?,(SELECT id_text_uid FROM text_uid WHERE text = ?),(SELECT id_text_uid FROM text_uid WHERE text = ?),?)")) {
		$stmt->bind_param("sssiissi",$source,$service,$status,$timestamp,$error,$source,$service,$uid);
		$stmt->execute();
		echo "$mysqli->error";
		$stmt->close();
	}
	else{
		echo "$mysqli->error";
	}
}

function graph_2dring_from_stmt($stmt,$title='2D Ring Chart',$class=''){
	
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
	
	$myPicture = new pImage(800,480,$myData);
	$myPicture->drawRectangle(0,0,799,479,array("R"=>0,"G"=>0,"B"=>0)); 
	$myPicture->drawGradientArea(0,0,799,40,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100)); 
	$myPicture->setFontProperties(array("FontName"=>"site/modules/pchart/fonts/verdana.ttf","FontSize"=>14)); 
	$myPicture->drawText(10,30,$title,array("R"=>255,"G"=>255,"B"=>255)); 
	$myPicture->setFontProperties(array("FontName"=>"site/modules/pchart/fonts/verdana.ttf","FontSize"=>10));
	if (isset($values)) {
		$PieChart = new pPie($myPicture,$myData);
		$PieChart->draw2DRing(400,240,array("OuterRadius"=>120,"InnerRadius"=>60,"WriteValues"=>TRUE,"ValuePosition"=>PIE_VALUE_INSIDE,"DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"Border"=>TRUE));
	}
	else
		$myPicture->drawText(400,240,"No hay información disponible",array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE));
	$myPicture->setShadow(FALSE);
	echo "<img class='$class' alt='$title' src='data:image/png;base64,".strokeToBase64($myPicture)."'/>";
}

function pie_chart_from_mysql_stmt($stmt, $title='Pie Chart', $x=320, $y=200, $class=''){
	
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
		echo "<img class='$class' alt='$title' src='data:image/png;base64,".strokeToBase64($myPicture)."'/>";
	}
	$stmt->free_result();
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
		echo "<img class='$class' alt='$title' src='data:image/png;base64,".strokeToBase64($myPicture)."'/>";
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

function html_table_from_stmt_adv($stmt,$class='',$hidden=null,$page=null){
	$variables = array();
	$data = array();
	
	echo "<table class='$class'>";
	$fields = $stmt->result_metadata();
	echo "<tr>";
	while ($finfo = $fields->fetch_field()) {
		echo "<th>".$finfo->name."</th>";
		$variables[$finfo->name] = &$data[$finfo->name]; // pass by reference
	}
	echo "</tr>";
	$fields->close();
	
	call_user_func_array(array($stmt, 'bind_result'), $variables);
	
	while($stmt->fetch()) {
		echo "<tr>";
		foreach ($variables as $key=>$value ) {
			if (!in_array($key,$hidden)){
				echo "<td>$value</td>";
			}
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
	global $app, $mysqli;
	
	//mysql_truncate_events($mysqli);
	$inbox = imap_open($app['mail']['hostname'],$app['mail']['username'],$app['mail']['password']) or die('Cannot connect to IMAP: ' . imap_last_error());
	$emails = imap_search($inbox,'UNSEEN');

	if($emails) {
		$app['runtime']['emails'] = count($emails);
		foreach($emails as $email_number) {
			imap_setflag_full($inbox, $email_number, "\\Seen");
			$overview = imap_fetch_overview($inbox,$email_number,0);
			$title = imap_utf8($overview[0]->subject);
			foreach ($app['format'] as $expresion){
				$found = preg_match($expresion['title'], $title, $values);
				if ($found){
					$source = (array_key_exists('source',$expresion['variables']))?$values[$expresion['variables']['source']]:$app['general']['unknown'];
					$service = (array_key_exists('service',$expresion['variables']))?$values[$expresion['variables']['service']]:$app['general']['unknown'];
					$status = (array_key_exists('status',$expresion['variables']))?$values[$expresion['variables']['status']]:$app['general']['unknown'];
					$uid = $overview[0]->uid;
					$timestamp = strtotime($overview[0]->date);
					$error=0;
					if (array_key_exists('error',$expresion)){
						$error = preg_match($expresion['error'], $status);		
					}
					mysql_insert_event($mysqli,$source,$service,$status,$timestamp,$error,$uid);
					break;
				}
			}
			if (!$found) $app['runtime']['unknown'][] = $overview[0]->date." :: $title";
		}
		// $message = imap_fetchbody($inbox,$email_number,1.2);
		// if(!strlen($message)>0){
			// $message = imap_fetchbody($inbox,$email_number,1);
		// }
		// echo $message;
	} 
	imap_close($inbox);
}

function mail_delete($uid){
	global $app;
	
	$inbox = imap_open($app['mail']['hostname'],$app['mail']['username'],$app['mail']['password']) or die('Cannot connect to IMAP: ' . imap_last_error());
	imap_delete($inbox,$uid,FT_UID);
	imap_expunge($inbox);
	imap_close($inbox);
}
?>