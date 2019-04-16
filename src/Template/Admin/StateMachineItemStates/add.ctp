<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemState
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Process'), ['controller' => 'StateMachineProcesses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Item State History'), ['controller' => 'StateMachineItemStateHistory', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Item State History'), ['controller' => 'StateMachineItemStateHistory', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Timeouts'), ['controller' => 'StateMachineTimeouts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Timeout'), ['controller' => 'StateMachineTimeouts', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="content action-form form large-9 medium-8 columns col-sm-8 col-xs-12">
    <?= $this->Form->create($stateMachineItemState) ?>
    <fieldset>
        <legend><?= __('Add State Machine Item State') ?></legend>
        <?php
            echo $this->Form->control('state_machine_process_id', ['options' => $stateMachineProcesses]);
            echo $this->Form->control('name');
            echo $this->Form->control('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
