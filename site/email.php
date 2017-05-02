			<h1 class="page-header">Correo electrónico</h1>
			<div class="row">
				<div class="col-xs-12 col-sm-12">
<?php
	$uid=$_GET['uid'];
	$inbox = imap_open($app['mail']['hostname'],$app['mail']['username'],$app['mail']['password']) or die('Cannot connect to IMAP: ' . imap_last_error());
	if ($overview = imap_fetch_overview($inbox,$uid,FT_UID)){
		$message = imap_fetchbody($inbox,$uid,'1',FT_UID);
		if(!strlen($message)>0){
			$message = imap_fetchbody($inbox,$uid,'2',FT_UID);
		}
		echo "<div class='email small'>";
		echo "<div class='email-meta'>";
		echo "<p><span class='email-meta-section'>Subject:</span> ".htmlentities(imap_utf8($overview[0]->subject))."</p>";
		echo "<p><span class='email-meta-section'>From:</span> ".htmlentities(imap_utf8($overview[0]->from))."</p>";
		echo "<p><span class='email-meta-section'>Date:</span> ".$overview[0]->date."</p>";
		echo "<p><span class='email-meta-section'>To:</span> ".htmlentities(imap_utf8($overview[0]->to))."</p>";
		echo "</div>";
		echo "<div class='email-body'><p>".nl2br($message)."</p></div>";
		echo "</div>";
	}
	else{
		echo "<p>No se ha podido desargar el correo electrónico seleccionado.</p>";
	}
	imap_close($inbox);
?>
				</div>
			</div>