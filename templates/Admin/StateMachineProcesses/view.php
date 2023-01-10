<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineProcess $stateMachineProcess
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Form->postLink(__('Delete State Machine Process'), ['action' => 'delete', $stateMachineProcess->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineProcess->id)]) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Processes'), ['action' => 'index']) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Item States'), ['controller' => 'StateMachineItemStates', 'action' => 'index']) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Timeouts'), ['controller' => 'StateMachineTimeouts', 'action' => 'index']) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Transition Logs'), ['controller' => 'StateMachineTransitionLogs', 'action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-12">
    <h2><?= h($stateMachineProcess->name) ?></h2>
    <table class="table vertical-table">
                <tr>
            <th><?= __('State Machine') ?></th>
            <td><?= h($stateMachineProcess->state_machine) ?></td>
        </tr>
            <tr>
            <th><?= __('Created') ?></th>
            <td><?= $this->Time->nice($stateMachineProcess->created) ?></td>
        </tr>
            <tr>
            <th><?= __('Modified') ?></th>
            <td><?= $this->Time->nice($stateMachineProcess->modified) ?></td>
        </tr>
    </table>

    <div class="related">
        <h3><?= __('Related State Machine Item States') ?></h3>
        <?php if (!empty($stateMachineProcess->state_machine_item_states)): ?>
        <table class="table table-striped">
            <tr>
                                    <th><?= __('State Machine Process Id') ?></th>
                        <th><?= __('Name') ?></th>
                        <th><?= __('Description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($stateMachineProcess->state_machine_item_states as $stateMachineItemStates): ?>
            <tr>
                                                <td><?= h($stateMachineItemStates->state_machine_process_id) ?></td>
                                <td><?= h($stateMachineItemStates->name) ?></td>
                                <td><?= h($stateMachineItemStates->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link($this->Icon->render('view'), ['controller' => 'StateMachineItemStates', 'action' => 'view', $stateMachineItemStates->id], ['escapeTitle' => false]); ?>
                    <?= $this->Form->postLink($this->Icon->render('delete'), ['controller' => 'StateMachineItemStates', 'action' => 'delete', $stateMachineItemStates->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItemStates->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h3><?= __('Related State Machine Timeouts') ?></h3>
        <?php if (!empty($stateMachineProcess->state_machine_timeouts)): ?>
        <table class="table table-striped">
            <tr>
                                    <th><?= __('State Machine Item State Id') ?></th>
                        <th><?= __('State Machine Process Id') ?></th>
                        <th><?= __('Identifier') ?></th>
                        <th><?= __('Event') ?></th>
                        <th><?= __('Timeout') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($stateMachineProcess->state_machine_timeouts as $stateMachineTimeouts): ?>
            <tr>
                                                <td><?= h($stateMachineTimeouts->state_machine_item_state_id) ?></td>
                                <td><?= h($stateMachineTimeouts->state_machine_process_id) ?></td>
                                <td><?= h($stateMachineTimeouts->identifier) ?></td>
                                <td><?= h($stateMachineTimeouts->event) ?></td>
                                <td><?= h($stateMachineTimeouts->timeout) ?></td>
                <td class="actions">
                    <?= $this->Html->link($this->Icon->render('view'), ['controller' => 'StateMachineTimeouts', 'action' => 'view', $stateMachineTimeouts->id], ['escapeTitle' => false]); ?>
                    <?= $this->Form->postLink($this->Icon->render('delete'), ['controller' => 'StateMachineTimeouts', 'action' => 'delete', $stateMachineTimeouts->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineTimeouts->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
