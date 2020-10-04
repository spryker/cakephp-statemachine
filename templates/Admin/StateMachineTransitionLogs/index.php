<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineTransitionLog[]|\Cake\Collection\CollectionInterface $stateMachineTransitionLogs
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-12">
    <h2><?= __('State Machine Transition Logs') ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('state_machine_process_id') ?></th>
                <th><?= $this->Paginator->sort('identifier') ?></th>
                <th><?= $this->Paginator->sort('locked') ?></th>
                <th><?= $this->Paginator->sort('event') ?></th>
                <th><?= $this->Paginator->sort('params') ?></th>
                <th><?= $this->Paginator->sort('source_state') ?></th>
                <th><?= $this->Paginator->sort('target_state') ?></th>
                <th><?= $this->Paginator->sort('command') ?></th>
                <th><?= $this->Paginator->sort('condition') ?></th>
                <th><?= $this->Paginator->sort('is_error') ?></th>
                <th><?= $this->Paginator->sort('created', null, ['direction' => 'desc']) ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateMachineTransitionLogs as $stateMachineTransitionLog): ?>
            <tr>
                <td><?= $stateMachineTransitionLog->has('state_machine_process') ? $this->Html->link($stateMachineTransitionLog->state_machine_process->name, ['controller' => 'StateMachineProcesses', 'action' => 'view', $stateMachineTransitionLog->state_machine_process->id]) : '' ?></td>
                <td><?= h($stateMachineTransitionLog->identifier) ?></td>
                <td><?= $this->Format->yesNo($stateMachineTransitionLog->locked) ?></td>
                <td><?= h($stateMachineTransitionLog->event) ?></td>
                <td><?= h($stateMachineTransitionLog->params) ?></td>
                <td><?= h($stateMachineTransitionLog->source_state) ?></td>
                <td><?= h($stateMachineTransitionLog->target_state) ?></td>
                <td><?= h($stateMachineTransitionLog->command) ?></td>
                <td><?= h($stateMachineTransitionLog->condition) ?></td>
                <td><?= $this->Format->yesNo($stateMachineTransitionLog->is_error) ?></td>
                <td><?= $this->Time->nice($stateMachineTransitionLog->created) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Format->icon('view'), ['action' => 'view', $stateMachineTransitionLog->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Format->icon('delete'), ['action' => 'delete', $stateMachineTransitionLog->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineTransitionLog->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
