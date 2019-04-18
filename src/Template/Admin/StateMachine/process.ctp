<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Dto\StateMachine\ProcessDto[] $processes
 */
?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Back'), ['action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="large-9 medium-8 columns content">
    <h1>Processes for "<?php echo h($stateMachineName); ?>" state machine</h1>

    <?php foreach ($processes as $process) { ?>
        <div class="large-4 medium-6 columns col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><?php echo h($process->getProcessName()); ?></h2>
                </div>
                <div class="panel-body">
                    <?php
                    $url = $this->Url->build(['controller' => 'Graph', 'action' => 'draw', '?' => ['process' => $process->getProcessName(), 'state-machine' => $stateMachineName]]);
                    echo $this->Html->link('<div class="state-machine-preview" style="font-size:10px;"><img src="'. $url . '" alt="' . $process->getProcessName() . '"/></div>', $url, ['escape' => false]);
                    ?>
                </div>

            </div>
        </div>
    <?php } ?>
</div>
