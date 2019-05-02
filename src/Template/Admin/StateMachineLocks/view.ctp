<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $stateMachineLock
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit State Machine Lock'), ['action' => 'edit', $stateMachineLock->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete State Machine Lock'), ['action' => 'delete', $stateMachineLock->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineLock->id)]) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Locks'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New State Machine Lock'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= h($stateMachineLock->id) ?></h2>
    <table class="table vertical-table">
            <tr>
            <th><?= __('Identifier') ?></th>
            <td><?= h($stateMachineLock->identifier) ?></td>
        </tr>
            <tr>
            <th><?= __('Expires') ?></th>
            <td><?= $this->Time->nice($stateMachineLock->expires) ?></td>
        </tr>
            <tr>
            <th><?= __('Created') ?></th>
            <td><?= $this->Time->nice($stateMachineLock->created) ?></td>
        </tr>
    </table>

</div>
