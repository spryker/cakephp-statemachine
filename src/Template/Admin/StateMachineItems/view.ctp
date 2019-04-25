<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItem $stateMachineItem
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List State Machine Items'), ['action' => 'index']) ?> </li>
        <?php if ($stateMachineItem->state_machine_transition_log) { ?>

        <?php } ?>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-xs-12">
    <h2><?= h($stateMachineItem->state_machine) ?> <?= h($stateMachineItem->identifier) ?></h2>
    <table class="table vertical-table">
        <tr>
            <th><?= __('State') ?></th>
            <td><?= h($stateMachineItem->state) ?></td>
        </tr>
        <tr>
            <th><?= __('Identifier') ?></th>
            <td><?= $this->Number->format($stateMachineItem->identifier) ?></td>
        </tr>
            <tr>
            <th><?= __('State Machine') ?></th>
            <td><?= h($stateMachineItem->state_machine) ?></td>
        </tr>
                <tr>
            <th><?= __('State Machine Transition Log Id') ?></th>
            <td><?= $this->Number->format($stateMachineItem->state_machine_transition_log_id) ?></td>
        </tr>
    </table>

    <?php if ($stateMachineItem->state_machine_transition_log) { ?>
        <table class="table vertical-table">
            <tr>
                <th><?= __('Process') ?></th>
                <td><?= h($stateMachineItem->state_machine_transition_log->state_machine_process->name) ?></td>
            </tr>
            <tr>
                <th><?= __('Transition') ?></th>
                <td>
                    <?php if ($stateMachineItem->state_machine_transition_log->source_state || $stateMachineItem->state_machine_transition_log->target_state) { ?>
                    <?= h($stateMachineItem->state_machine_transition_log->source_state) ?> ... <?= h($stateMachineItem->state_machine_transition_log->target_state) ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th><?= __('Time') ?></th>
                <td><?= $this->Time->nice($stateMachineItem->state_machine_transition_log->created) ?></td>
            </tr>
            <tr>
                <th><?= __('Command') ?></th>
                <td><?= h($stateMachineItem->state_machine_transition_log->command) ?></td>
            </tr>
            <tr>
                <th><?= __('Condition') ?></th>
                <td><?= h($stateMachineItem->state_machine_transition_log->condition) ?></td>
            </tr>
            <tr>
                <th><?= __('Error') ?></th>
                <td><?php if ($stateMachineItem->state_machine_transition_log->is_error) {
                    echo 'Error: ' . h($stateMachineItem->state_machine_transition_log->error_message);
                    } ?></td>
            </tr>
        </table>
    <?php } ?>

</div>
