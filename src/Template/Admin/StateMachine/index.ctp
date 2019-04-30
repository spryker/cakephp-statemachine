<?php
/**
 * @var \App\View\AppView $this
 * @var string[] $stateMachines
 * @var int[] $itemsPerStateMachine
 */
use Cake\Core\Configure;
?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <?php if (Configure::read('debug')) { ?>
        <li><?= $this->Form->postLink(__('Reset State Machine'), ['action' => 'reset'], ['confirm' => 'Sure? It will nuke all data.']) ?> </li>
        <?php } ?>
    </ul>
</nav>
<div class="large-9 medium-8 columns content">
    <h1>Active State Machines</h1>

    <ul>
        <?php foreach ($stateMachines as $stateMachine) { ?>
        <li>
            <?php echo $this->Html->link($stateMachine, ['action' => 'process', '?' => ['state-machine' => $stateMachine]]); ?>
            <?php
                if (!empty($itemsPerStateMachine[$stateMachine])) {
                    echo ' (' . $itemsPerStateMachine[$stateMachine] . ' items)';
                }
            ?>
        </li>
        <?php } ?>
    </ul>
</div>
