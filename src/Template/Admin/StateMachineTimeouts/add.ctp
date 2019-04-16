<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineTimeout $stateMachineTimeout
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List State Machine Timeouts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Item State'), ['controller' => 'StateMachineItemStates', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Process'), ['controller' => 'StateMachineProcesses', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="content action-form form large-9 medium-8 columns col-sm-8 col-xs-12">
    <?= $this->Form->create($stateMachineTimeout) ?>
    <fieldset>
        <legend><?= __('Add State Machine Timeout') ?></legend>
        <?php
            echo $this->Form->control('state_machine_item_state_id', ['options' => $stateMachineItemStates]);
            echo $this->Form->control('state_machine_process_id', ['options' => $stateMachineProcesses]);
            echo $this->Form->control('identifier');
            echo $this->Form->control('event');
            echo $this->Form->control('timeout');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
