<?php
$emails = email_inbox();
?>
			<h1 class="page-header"><i class='fa fa-envelope-o fa-fw' aria-hidden='true' style='float:right;'></i>Bandeja de entrada</h1>
			<table class='table table-condensed table-striped small'>
				<thead>
					<tr>
						<th>Asunto</th>
						<th style="white-space:nowrap">Remitente</th>
						<th style="white-space:nowrap">Fecha</th>
					</tr>
				<thead>
				<tbody>
<?php
$emails = array_reverse($emails);
foreach($emails as $value){
	echo "<tr>";
	echo "<td><a href='?p=email.php&uid=".$value['uid']."'>".htmlentities(imap_utf8($value['subject']))."</a></td><td>".htmlentities(imap_utf8($value['from']))."</td><td style='white-space:nowrap'>".$value['date']."</td>";
	echo "<td><a href='#'><i class='fa fa-trash-o fa-fw fa-2x' aria-hidden='true'></i><span class='sr-only'>[Borrar]</span></a></td>";

	echo "</tr>";
}
?>
				</tbody>
			</table>