<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItem[]|\Cake\Collection\CollectionInterface $stateMachineItems
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Back'), ['controller' => 'StateMachine', 'action' => 'index']); ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= __('State Machine Items') ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('identifier') ?></th>
                <th><?= $this->Paginator->sort('state_machine') ?></th>
                <th><?= $this->Paginator->sort('state') ?></th>
                <th><?= $this->Paginator->sort('state_machine_transition_log_id', __('Last change')) ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateMachineItems as $stateMachineItem): ?>
            <tr>
                <td><?= h($stateMachineItem->identifier) ?></td>
                <td><?= h($stateMachineItem->state_machine) ?></td>
                <td><?= h($stateMachineItem->state) ?></td>
                <td><?= $this->Time->nice($stateMachineItem->state_machine_transition_log->created) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Format->icon('view'), ['action' => 'view', $stateMachineItem->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Format->icon('delete'), ['action' => 'delete', $stateMachineItem->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItem->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
