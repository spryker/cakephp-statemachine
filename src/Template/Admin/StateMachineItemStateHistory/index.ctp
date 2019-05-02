<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItemStateHistory $stateMachineItemStateHistory
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= __('State Machine Item State History') ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('state_machine_item_state_id') ?></th>
                <th><?= $this->Paginator->sort('identifier') ?></th>
                <th><?= $this->Paginator->sort('created', null, ['direction' => 'desc']) ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateMachineItemStateHistory as $stateMachineItemStateHistory): ?>
            <tr>
                <td><?= $stateMachineItemStateHistory->has('state_machine_item_state') ? $this->Html->link($stateMachineItemStateHistory->state_machine_item_state->name, ['controller' => 'StateMachineItemStates', 'action' => 'view', $stateMachineItemStateHistory->state_machine_item_state->id]) : '' ?></td>
                <td><?= h($stateMachineItemStateHistory->identifier) ?></td>
                <td><?= $this->Time->nice($stateMachineItemStateHistory->created) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Format->icon('view'), ['action' => 'view', $stateMachineItemStateHistory->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Format->icon('delete'), ['action' => 'delete', $stateMachineItemStateHistory->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItemStateHistory->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
