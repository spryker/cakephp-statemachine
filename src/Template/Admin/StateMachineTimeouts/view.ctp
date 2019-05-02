<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineTimeout $stateMachineTimeout
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(__('Delete State Machine Timeout'), ['action' => 'delete', $stateMachineTimeout->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineTimeout->id)]) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Timeouts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= h($stateMachineTimeout->id) ?></h2>
    <table class="table vertical-table">
            <tr>
            <th><?= __('State Machine Item State') ?></th>
            <td><?= $stateMachineTimeout->has('state_machine_item_state') ? $this->Html->link($stateMachineTimeout->state_machine_item_state->name, ['controller' => 'StateMachineItemStates', 'action' => 'view', $stateMachineTimeout->state_machine_item_state->id]) : '' ?></td>
        </tr>
            <tr>
            <th><?= __('State Machine Process') ?></th>
            <td><?= $stateMachineTimeout->has('state_machine_process') ? $this->Html->link($stateMachineTimeout->state_machine_process->name, ['controller' => 'StateMachineProcesses', 'action' => 'view', $stateMachineTimeout->state_machine_process->id]) : '' ?></td>
        </tr>
            <tr>
            <th><?= __('Identifier') ?></th>
            <td><?= h($stateMachineTimeout->identifier) ?></td>
        </tr>
            <tr>
            <th><?= __('Event') ?></th>
            <td><?= h($stateMachineTimeout->event) ?></td>
        </tr>
            <tr>
            <th><?= __('Timeout') ?></th>
            <td><?= $this->Time->nice($stateMachineTimeout->timeout) ?></td>
        </tr>
    </table>

</div>
