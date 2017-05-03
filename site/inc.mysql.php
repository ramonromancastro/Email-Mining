<?php

function mysql_active_alerts_by_source($mysqli){	
	$sql = 'SELECT source, COUNT(*) as total'.
			' FROM (SELECT source, service, FROM_UNIXTIME(MAX(timestamp)) as timestamp'.
			' FROM emails'.
			' WHERE error = 1 AND acknowledge = 0 AND NOT EXISTS ('.
				' SELECT * FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND error = 0 AND Oks.timestamp > emails.timestamp'.
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
			' WHERE error = 1 AND acknowledge = 0 AND NOT EXISTS ('.
				' SELECT * FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND error = 0 AND Oks.timestamp > emails.timestamp'.
			' ) AND '.
			' timestamp >= ('.
				' SELECT MAX(timestamp) FROM emails as Oks'.
				' WHERE Oks.source_uid = emails.source_uid AND Oks.service_uid = emails.service_uid AND error = 1 AND acknowledge = 0'.
			' )'.
			' ORDER BY timestamp DESC';
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_search_sources($mysqli,$source){
	$sql = "SELECT DISTINCT source_uid, source FROM emails WHERE source LIKE ?";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("s",$source);
		$stmt->execute();
	}
	return $stmt;
}

function mysql_search_sources_services($mysqli,$pattern){
	$sql = "SELECT DISTINCT source_uid, source, NULL as service_uid, NULL as service FROM emails WHERE source LIKE ?".
			" UNION SELECT DISTINCT source_uid, source, service_uid, service FROM emails WHERE service LIKE ? ORDER BY source,service";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("ss",$pattern,$pattern);
		$stmt->execute();
	}
	return $stmt;
}

function mysql_emails_acknowledge_email_affected($mysqli,$uid){
	$sql = "SELECT uid FROM emails WHERE error = 1 and acknowledge = 0 AND EXISTS (SELECT * FROM (SELECT * FROM emails WHERE uid = ?) ack WHERE source_uid = emails.source_uid AND service_uid = emails.service_uid AND status = emails.status AND timestamp >= emails.timestamp)";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("i",$uid);
		$stmt->execute();
	}
	return $stmt;
}

function mysql_emails_acknowledge_email($mysqli,$uid){
	$sql = "UPDATE emails SET acknowledge=1 WHERE uid=?";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("i",$uid);
		$stmt->execute();
	}
	$stmt->close();

	$sql = "UPDATE emails SET acknowledge = 1 WHERE error = 1 and acknowledge = 0 AND EXISTS (SELECT * FROM (SELECT * FROM emails WHERE uid = ?) as ack WHERE source_uid = emails.source_uid AND service_uid = emails.service_uid AND timestamp >= emails.timestamp)";
	if ($stmt = $mysqli->prepare($sql)) {
		$stmt->bind_param("i",$uid);
		$stmt->execute();
	}
	$stmt->close();
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
	$sql = "SELECT source,COUNT(*) as total FROM emails WHERE error = 1 GROUP BY source ORDER BY total DESC";
	if ($top)
		$sql .= " LIMIT 0,$top";
	
	if ($stmt = $mysqli->prepare($sql)) $stmt->execute();
	return $stmt;
}

function mysql_report_top_service_alert($mysqli,$top=0){
	$sql = "SELECT CONCAT(source,' / ',service) as service,COUNT(*) as total FROM emails WHERE error = 1 GROUP BY source, service ORDER BY total DESC";
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
?>