<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $stateMachineProcess
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $stateMachineProcess->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineProcess->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Item State'), ['controller' => 'StateMachineItemStates', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Timeouts'), ['controller' => 'StateMachineTimeouts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Timeout'), ['controller' => 'StateMachineTimeouts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Transition Logs'), ['controller' => 'StateMachineTransitionLogs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Transition Log'), ['controller' => 'StateMachineTransitionLogs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="content action-form form large-9 medium-8 columns col-sm-8 col-xs-12">
    <?= $this->Form->create($stateMachineProcess) ?>
    <fieldset>
        <legend><?= __('Edit State Machine Process') ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('state_machine');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
