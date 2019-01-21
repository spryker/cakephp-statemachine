<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $stateMachineProcesses
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New State Machine Process'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Item State'), ['controller' => 'StateMachineItemStates', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Timeouts'), ['controller' => 'StateMachineTimeouts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Timeout'), ['controller' => 'StateMachineTimeouts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Machine Transition Logs'), ['controller' => 'StateMachineTransitionLogs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State Machine Transition Log'), ['controller' => 'StateMachineTransitionLogs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= __('State Machine Processes') ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('state_machine') ?></th>
                <th><?= $this->Paginator->sort('created', null, ['direction' => 'desc']) ?></th>
                <th><?= $this->Paginator->sort('modified', null, ['direction' => 'desc']) ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateMachineProcesses as $stateMachineProcess): ?>
            <tr>
                <td><?= h($stateMachineProcess->name) ?></td>
                <td><?= h($stateMachineProcess->state_machine) ?></td>
                <td><?= $this->Time->nice($stateMachineProcess->created) ?></td>
                <td><?= $this->Time->nice($stateMachineProcess->modified) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Format->icon('view'), ['action' => 'view', $stateMachineProcess->id], ['escapeTitle' => false]); ?>
                <?= $this->Html->link($this->Format->icon('edit'), ['action' => 'edit', $stateMachineProcess->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Format->icon('delete'), ['action' => 'delete', $stateMachineProcess->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineProcess->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
