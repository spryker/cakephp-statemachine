<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineTimeout[]|\Cake\Collection\CollectionInterface $stateMachineTimeouts
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Html->link(__('Back'), ['controller' => 'StateMachine', 'action' => 'index']) ?></li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?></li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-12">
    <h2><?= __('State Machine Timeouts') ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('state_machine_item_state_id') ?></th>
                <th><?= $this->Paginator->sort('state_machine_process_id') ?></th>
                <th><?= $this->Paginator->sort('identifier') ?></th>
                <th><?= $this->Paginator->sort('event') ?></th>
                <th><?= $this->Paginator->sort('timeout', null, ['direction' => 'desc']) ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateMachineTimeouts as $stateMachineTimeout): ?>
            <tr>
                <td><?= $stateMachineTimeout->has('state_machine_item_state') ? $this->Html->link($stateMachineTimeout->state_machine_item_state->name, ['controller' => 'StateMachineItemStates', 'action' => 'view', $stateMachineTimeout->state_machine_item_state->id]) : '' ?></td>
                <td><?= $stateMachineTimeout->has('state_machine_process') ? $this->Html->link($stateMachineTimeout->state_machine_process->name, ['controller' => 'StateMachineProcesses', 'action' => 'view', $stateMachineTimeout->state_machine_process->id]) : '' ?></td>
                <td><?= h($stateMachineTimeout->identifier) ?></td>
                <td><?= h($stateMachineTimeout->event) ?></td>
                <td><?= $this->Time->nice($stateMachineTimeout->timeout) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Icon->render('view'), ['action' => 'view', $stateMachineTimeout->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Icon->render('delete'), ['action' => 'delete', $stateMachineTimeout->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineTimeout->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
