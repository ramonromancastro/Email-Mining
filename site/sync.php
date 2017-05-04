<?php
collect_mail();
?>
			<h1 class="page-header"><i class='fa fa-refresh fa-fw' aria-hidden='true' style='float:right;'></i>Sincronizar ahora!</h1>
			<h2 class="sub-header">Resultados de la sincronización</h2>
			<div class="row">
				<div class="col-xs-4 col-sm-4">
					<div class="alert alert-<?php echo ($config['app']['runtime']['sync']['analyzed'])?"success":"info"; ?> text-center">
						<?php echo "<span class='number'>".$config['app']['runtime']['sync']['analyzed']."</span><span class='text'> correo(s) analizado(s)</span>"; ?>
					</div>
				</div>
				<div class="col-xs-4 col-sm-4">
					<div class="alert alert-<?php echo ($config['app']['runtime']['sync']['included'])?"success":"info"; ?> text-center">
						<?php echo "<span class='number'>".$config['app']['runtime']['sync']['included']."</span><span class='text'> correo(s) añadidos(s)</span>"; ?>
					</div>
				</div>
				<div class="col-xs-4 col-sm-4">
					<div class="alert alert-<?php echo ($config['app']['runtime']['sync']['excluded'])?"danger":"success"; ?> text-center">
						<?php echo "<span class='number'>".$config['app']['runtime']['sync']['excluded']."</span><span class='text'> correo(s) excluidos</span>"; ?>
					</div>
				</div>
			</div>