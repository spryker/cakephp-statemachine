<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $stateMachineTransitionLog
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List State Machine Transition Logs'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Process'), ['controller' => 'StateMachineProcesses', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="content action-form form large-9 medium-8 columns col-sm-8 col-xs-12">
    <?= $this->Form->create($stateMachineTransitionLog) ?>
    <fieldset>
        <legend><?= __('Add State Machine Transition Log') ?></legend>
        <?php
            echo $this->Form->control('state_machine_process_id', ['options' => $stateMachineProcesses]);
            echo $this->Form->control('identifier');
            echo $this->Form->control('locked');
            echo $this->Form->control('event');
            echo $this->Form->control('params');
            echo $this->Form->control('source_state');
            echo $this->Form->control('target_state');
            echo $this->Form->control('command');
            echo $this->Form->control('condition');
            echo $this->Form->control('is_error');
            echo $this->Form->control('error_message');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
