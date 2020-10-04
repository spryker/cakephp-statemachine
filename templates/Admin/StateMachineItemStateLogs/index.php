<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItemStateLog $stateMachineItemStateLogs
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-12">
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
            <?php foreach ($stateMachineItemStateLogs as $stateMachineItemStateLog): ?>
            <tr>
                <td><?= $stateMachineItemStateLog->has('state_machine_item_state') ? $this->Html->link($stateMachineItemStateLog->state_machine_item_state->name, ['controller' => 'StateMachineItemStates', 'action' => 'view', $stateMachineItemStateLog->state_machine_item_state->id]) : '' ?></td>
                <td><?= h($stateMachineItemStateLog->identifier) ?></td>
                <td><?= $this->Time->nice($stateMachineItemStateLog->created) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Format->icon('view'), ['action' => 'view', $stateMachineItemStateLog->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Format->icon('delete'), ['action' => 'delete', $stateMachineItemStateLog->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItemStateLog->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
