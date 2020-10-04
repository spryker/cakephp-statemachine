<?php
/**
 * @var \App\View\AppView $this
 * @var string $stateMachineName
 * @var \StateMachine\Dto\StateMachine\ProcessDto[] $processes
 */
?>

<nav class="large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Html->link(__('Back'), ['action' => 'index']) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('Items'), ['controller' => 'StateMachineItems', 'action' => 'index', '?' => ['state-machine' => $stateMachineName]]) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('Matrix Overview'), ['action' => 'overview', '?' => ['state-machine' => $stateMachineName]]) ?> </li>
    </ul>
</nav>
<div class="large-9 medium-8 columns content col-sm-8 col-12">
    <h1>Processes for "<?php echo h($stateMachineName); ?>" state machine</h1>

    <?php foreach ($processes as $process) { ?>
        <div class="large-4 medium-6 columns col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><?php echo h($process->getProcessName()); ?></h2>
                </div>
                <div class="panel-body">
                    <?php
                    $urlArray = ['controller' => 'Graph', 'action' => 'draw', '?' => ['process' => $process->getProcessName(), 'state-machine' => $stateMachineName]];
                    $url = $this->Url->build($urlArray);
                    echo $this->Html->link('<div class="state-machine-preview" style="font-size:10px;"><img src="'. $url . '" alt="' . $process->getProcessName() . '"/></div>', $url, ['escape' => false, 'target' => '_blank']);
                    ?>

                    <small>
                        <?php
                        $urlArray['?']['format'] = 'png';
                        echo $this->Html->link('png', $urlArray, ['target' => '_blank']);
                        ?>
                        |
                        <?php
                        $urlArray['?']['format'] = 'pdf';
                        echo $this->Html->link('pdf', $urlArray, ['target' => '_blank']);
                        ?>
                    </small>
                </div>

            </div>
        </div>
    <?php } ?>
</div>
