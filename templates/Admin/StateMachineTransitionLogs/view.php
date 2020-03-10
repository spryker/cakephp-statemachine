<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineTransitionLog $stateMachineTransitionLog
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(__('Delete State Machine Transition Log'), ['action' => 'delete', $stateMachineTransitionLog->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineTransitionLog->id)]) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Transition Logs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('List State Machine Processes'), ['controller' => 'StateMachineProcesses', 'action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= h($stateMachineTransitionLog->id) ?></h2>
    <table class="table vertical-table">
            <tr>
            <th><?= __('State Machine Process') ?></th>
            <td><?= $stateMachineTransitionLog->has('state_machine_process') ? $this->Html->link($stateMachineTransitionLog->state_machine_process->name, ['controller' => 'StateMachineProcesses', 'action' => 'view', $stateMachineTransitionLog->state_machine_process->id]) : '' ?></td>
        </tr>
            <tr>
            <th><?= __('Identifier') ?></th>
            <td><?= h($stateMachineTransitionLog->identifier) ?></td>
        </tr>
            <tr>
            <th><?= __('Locked') ?></th>
            <td><?= $this->Format->yesNo($stateMachineTransitionLog->locked) ?></td>
        </tr>
            <tr>
            <th><?= __('Event') ?></th>
            <td><?= h($stateMachineTransitionLog->event) ?></td>
        </tr>
            <tr>
            <th><?= __('Params') ?></th>
            <td><?= h($stateMachineTransitionLog->params) ?></td>
        </tr>
            <tr>
            <th><?= __('Source State') ?></th>
            <td><?= h($stateMachineTransitionLog->source_state) ?></td>
        </tr>
            <tr>
            <th><?= __('Target State') ?></th>
            <td><?= h($stateMachineTransitionLog->target_state) ?></td>
        </tr>
            <tr>
            <th><?= __('Command') ?></th>
            <td><?= h($stateMachineTransitionLog->command) ?></td>
        </tr>
            <tr>
            <th><?= __('Condition') ?></th>
            <td><?= h($stateMachineTransitionLog->condition) ?></td>
        </tr>
            <tr>
            <th><?= __('Is Error') ?></th>
            <td><?= $this->Format->yesNo($stateMachineTransitionLog->is_error) ?></td>
        </tr>
            <tr>
            <th><?= __('Created') ?></th>
            <td><?= $this->Time->nice($stateMachineTransitionLog->created) ?></td>
        </tr>
    </table>
    <div class="row">
        <h3><?= __('Error Message') ?></h3>
        <?= $this->Text->autoParagraph(h($stateMachineTransitionLog->error_message)); ?>
    </div>

</div>
