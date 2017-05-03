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
	global $app;
	
	$variables = array();
	$data = array();

	$stmt->store_result();
	if (!$stmt->num_rows){
		echo "<p>".$app['general']['nodata']."</p>";
		exit;
	}
	echo "<table class='$class'>";
	$fields = $stmt->result_metadata();
	echo "<tr>";
	while ($finfo = $fields->fetch_field()) {
		if (!in_array($finfo->name,$hidden)){
			echo "<th>".$finfo->name."</th>";
		}
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

function init_all_values(){
	reset_runtime_sync();
}

function reset_runtime_sync(){
	global $app;
	
	$app['runtime']['sync']['analyzed'] = 0;
	$app['runtime']['sync']['included'] = 0;
	$app['runtime']['sync']['excluded'] = 0;
}

function collect_mail(){
	global $app, $mysqli;
	
	$mailbox = 'UNSEEN';
	
	if ($app['general']['debug']){
		mysql_truncate_events($mysqli);
		$mailbox = 'ALL';
	}

	$inbox = imap_open($app['mail']['hostname'],$app['mail']['username'],$app['mail']['password']) or die('Cannot connect to IMAP: ' . imap_last_error());
	$emails = imap_search($inbox,$mailbox);

	if($emails) {
		$app['runtime']['sync']['analyzed'] = count($emails);
		foreach($emails as $email_number) {
			imap_setflag_full($inbox, $email_number, "\\Seen");
			$overview = imap_fetch_overview($inbox,$email_number,0);
			$title = imap_utf8($overview[0]->subject);
			$found = 0;
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
					if (!$error && $app['general']['autodelete'] && !$app['general']['debug']){
						imap_delete($inbox,$email_number,0);
					}
					mysql_insert_event($mysqli,$source,$service,$status,$timestamp,$error,$uid);
					$app['runtime']['sync']['included']=$app['runtime']['sync']['included']+1;
					break;
				}
			}
			if (!$found) $app['runtime']['sync']['excluded']=$app['runtime']['sync']['excluded']+1;
		}
	}
	imap_expunge($inbox);
	imap_close($inbox);
}

function email_inbox(){
	global $app, $mysqli;
	
	$result = array();
	$inbox = imap_open($app['mail']['hostname'],$app['mail']['username'],$app['mail']['password']) or die('Cannot connect to IMAP: ' . imap_last_error());
	$emails = imap_search($inbox,'ALL');

	if($emails) {
		foreach($emails as $email_number) {
			imap_setflag_full($inbox, $email_number, "\\Seen");
			$overview = imap_fetch_overview($inbox,$email_number,0);
			$result[] = array('subject' => $overview[0]->subject,'from' => $overview[0]->from,'date' => $overview[0]->date, 'uid' => $overview[0]->uid);
		}
	} 
	imap_close($inbox);
	
	return $result;
}


function purge_inbox_acknowledge_from_stmt($stmt,$field){
	global $app, $mysqli;
	
	$variables = array();
	$data = array();
	
	$inbox = imap_open($app['mail']['hostname'],$app['mail']['username'],$app['mail']['password']) or die('Cannot connect to IMAP: ' . imap_last_error());
	
	$fields = $stmt->result_metadata();
	while ($finfo = $fields->fetch_field()) {
		$variables[$finfo->name] = &$data[$finfo->name];
	}
	$fields->close();
	call_user_func_array(array($stmt, 'bind_result'), $variables);
	while($stmt->fetch()) {
		imap_delete($inbox,$variables[$field],FT_UID);
	}

	imap_expunge($inbox);
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