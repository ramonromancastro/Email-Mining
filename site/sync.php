			<h1 class="page-header">Sincronizar ahora!</h1>
			<h2 class="sub-header">Resultados de la sincronización</h2>
<?php
	collect_mail();
	
	echo (isset($app['runtime']['emails']))?"<p class='alert alert-success'><strong>".$app['runtime']['emails']."</strong> correo(s) analizado(s) en la última sincronización.</p>":"<p class='alert alert-info'>No se han analizado correos en la última sincronización.</p>";
	
	if (isset($app['runtime']['unknown'])){
		echo "<h3>Emails no procesados</h3>";
		foreach($app['runtime']['unknown'] as $line){
			echo "<p>$line</p>";
		}
	}
	else{
		echo "<p class='alert alert-success'>No existen correos sin minar en la última sincronización.</p>";
	}
?>