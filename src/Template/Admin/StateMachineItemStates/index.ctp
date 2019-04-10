<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItemState[]|\Cake\Collection\CollectionInterface $stateMachineItemStates
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New State Machine Item State'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Process'), ['controller' => 'StateMachineProcesses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Item State History'), ['controller' => 'StateMachineItemStateHistory', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Item State History'), ['controller' => 'StateMachineItemStateHistory', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Timeouts'), ['controller' => 'StateMachineTimeouts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Timeout'), ['controller' => 'StateMachineTimeouts', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= __('State Machine Item States') ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('state_machine_process_id') ?></th>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateMachineItemStates as $stateMachineItemState): ?>
            <tr>
                <td><?= $stateMachineItemState->has('state_machine_process') ? $this->Html->link($stateMachineItemState->state_machine_process->name, ['controller' => 'StateMachineProcesses', 'action' => 'view', $stateMachineItemState->state_machine_process->id]) : '' ?></td>
                <td><?= h($stateMachineItemState->name) ?></td>
                <td><?= h($stateMachineItemState->description) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Format->icon('view'), ['action' => 'view', $stateMachineItemState->id], ['escapeTitle' => false]); ?>
                <?= $this->Html->link($this->Format->icon('edit'), ['action' => 'edit', $stateMachineItemState->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Format->icon('delete'), ['action' => 'delete', $stateMachineItemState->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItemState->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
