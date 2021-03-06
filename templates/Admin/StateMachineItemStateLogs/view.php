<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItemStateLog $stateMachineItemStateLog
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Form->postLink(__('Delete State Machine Item State Log'), ['action' => 'delete', $stateMachineItemStateLog->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItemStateLog->id)]) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Item State Log'), ['action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-12">
    <h2><?= h($stateMachineItemStateLog->id) ?></h2>
    <table class="table vertical-table">
            <tr>
            <th><?= __('State Machine Item State') ?></th>
            <td><?= $stateMachineItemStateLog->has('state_machine_item_state') ? $this->Html->link($stateMachineItemStateLog->state_machine_item_state->name, ['controller' => 'StateMachineItemStates', 'action' => 'view', $stateMachineItemStateLog->state_machine_item_state->id]) : '' ?></td>
        </tr>
            <tr>
            <th><?= __('Identifier') ?></th>
            <td><?= h($stateMachineItemStateLog->identifier) ?></td>
        </tr>
            <tr>
            <th><?= __('Created') ?></th>
            <td><?= $this->Time->nice($stateMachineItemStateLog->created) ?></td>
        </tr>
    </table>

</div>
