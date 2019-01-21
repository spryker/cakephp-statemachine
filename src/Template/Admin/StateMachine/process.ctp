<?php
/**
 * @var \App\View\AppView $this
 */
?>

<div class="row">
	<div class="col-md-12">
		<h1>Processes for "<?php echo h($stateMachineName); ?>" state machine</h1>
	</div>

	<?php foreach ($processes as $process) { ?>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2><?php echo h($process->getProcessName()); ?></h2>
				</div>
				<div class="panel-body">
					<?php echo $this->Html->link($process->getProcessName(), ['controller' => 'Graph', 'action' => 'draw', '?' => ['process' => $process->getProcessName(), 'state-machine' => $stateMachineName]]); ?>

					<div>
					<a style="font-size:10px;" href="{{ url('/state-machine/graph/draw-preview-version', {process: process.processName}) }}" target="_blank">[preview-version]</a>
					</div>
				</div>

			</div>
		</div>
	<?php } ?>
</div>
