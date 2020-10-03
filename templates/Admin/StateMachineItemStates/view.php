<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItemState $stateMachineItemState
 * @var \StateMachine\Model\Entity\StateMachineItemStateLog $stateMachineItemStateLogs
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(__('Delete State Machine Item State'), ['action' => 'delete', $stateMachineItemState->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItemState->id)]) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Item States'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Item State History'), ['controller' => 'StateMachineItemStateHistory', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Timeouts'), ['controller' => 'StateMachineTimeouts', 'action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= h($stateMachineItemState->name) ?></h2>
    <table class="table vertical-table">
            <tr>
            <th><?= __('State Machine Process') ?></th>
            <td><?= $stateMachineItemState->has('state_machine_process') ? $this->Html->link($stateMachineItemState->state_machine_process->name, ['controller' => 'StateMachineProcesses', 'action' => 'view', $stateMachineItemState->state_machine_process->id]) : '' ?></td>
        </tr>
                <tr>
            <th><?= __('Description') ?></th>
            <td><?= h($stateMachineItemState->description) ?></td>
        </tr>
    </table>

    <div class="related">
        <h3><?= __('Related State Machine Timeouts') ?></h3>
        <?php if (!empty($stateMachineItemState->state_machine_timeouts)): ?>
        <table class="table table-striped">
            <tr>
                                    <th><?= __('State Machine Item State Id') ?></th>
                        <th><?= __('State Machine Process Id') ?></th>
                        <th><?= __('Identifier') ?></th>
                        <th><?= __('Event') ?></th>
                        <th><?= __('Timeout') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($stateMachineItemState->state_machine_timeouts as $stateMachineTimeouts): ?>
            <tr>
                                                <td><?= h($stateMachineTimeouts->state_machine_item_state_id) ?></td>
                                <td><?= h($stateMachineTimeouts->state_machine_process_id) ?></td>
                                <td><?= h($stateMachineTimeouts->identifier) ?></td>
                                <td><?= h($stateMachineTimeouts->event) ?></td>
                                <td><?= h($stateMachineTimeouts->timeout) ?></td>
                <td class="actions">
                    <?= $this->Html->link($this->Format->icon('view'), ['controller' => 'StateMachineTimeouts', 'action' => 'view', $stateMachineTimeouts->id], ['escapeTitle' => false]); ?>
                    <?= $this->Html->link($this->Format->icon('edit'), ['controller' => 'StateMachineTimeouts', 'action' => 'edit', $stateMachineTimeouts->id], ['escapeTitle' => false]); ?>
                    <?= $this->Form->postLink($this->Format->icon('delete'), ['controller' => 'StateMachineTimeouts', 'action' => 'delete', $stateMachineTimeouts->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineTimeouts->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
