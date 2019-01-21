<?php
/**
 * @var \App\View\AppView $this
 */
?>

<div class="row">
	<div class="col-md-12">
		<h1>Active State Machines</h1>
	</div>

	<ul>
		<?php foreach ($stateMachines as $stateMachine) { ?>
		<li><?php echo $this->Html->link($stateMachine, ['action' => 'process', '?' => ['state-machine' => $stateMachine]]); ?></li>
		<?php } ?>
	</ul>
</div>
