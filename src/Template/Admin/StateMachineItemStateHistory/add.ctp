<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItemStateHistory $stateMachineItemStateHistory
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List State Machine Item State History'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Item State'), ['controller' => 'StateMachineItemStates', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="content action-form form large-9 medium-8 columns col-sm-8 col-xs-12">
    <?= $this->Form->create($stateMachineItemStateHistory) ?>
    <fieldset>
        <legend><?= __('Add State Machine Item State History') ?></legend>
        <?php
            echo $this->Form->control('state_machine_item_state_id', ['options' => $stateMachineItemStates]);
            echo $this->Form->control('identifier');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
