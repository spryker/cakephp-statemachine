<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineItem $stateMachineItem
 * @var array $events
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Items'), ['action' => 'index']) ?> </li>
        <?php if ($stateMachineItem->state_machine_transition_log) { ?>

        <?php } ?>
        <li class="nav-link"><?= $this->Form->postLink($this->Icon->render('delete') . ' ' . __('Delete'), ['action' => 'delete', $stateMachineItem->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineItem->id)]); ?></li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-12">
    <h2><?= h($stateMachineItem->state_machine) ?> <?= h($stateMachineItem->identifier) ?></h2>
    <table class="table vertical-table">
        <tr>
            <th><?= __('Identifier') ?></th>
            <td><?= $this->StateMachine->itemLink($stateMachineItem) ?></td>
        </tr>
        <tr>
            <th><?= __('State Machine') ?></th>
            <td><?= h($stateMachineItem->state_machine) ?></td>
        </tr>
        <tr>
            <th><?= __('State') ?></th>
            <td>
                <?= h($stateMachineItem->state) ?>
                <div>
                <?php
                foreach ($events as $event) {
                    $url = ['prefix' => 'Admin', 'plugin' => 'StateMachine', 'controller' => 'Trigger', 'action' => 'event', '?' => ['state-machine' => $stateMachineItem->state_machine, 'process' => $stateMachineItem->process, 'state' => $stateMachineItem->state, 'identifier' => $stateMachineItem->identifier, 'event' => $event, 'catch' => true]];
                    echo $this->Form->postLink($event, $url, ['class' => 'button btn btn-secondary', 'confirm' => 'Sure?']) . ' ';
                }
                ?>
                </div>
            </td>
        </tr>
    </table>

    <?php
    $url = [
        'controller' => 'Graph',
        'action' => 'draw',
        '?' => [
            'state-machine' => $stateMachineItem->state_machine,
            'process' => $stateMachineItem->process,
            'highlight-state' => $stateMachineItem->state,
        ],
    ];
    $image = $this->Html->image($url);
    echo $this->Html->link($image, $url, ['escapeTitle' => false, 'target' => '_blank']);
    ?>

    <?php if ($stateMachineItem->state_machine_transition_log) { ?>
        <table class="table vertical-table">
            <tr>
                <th><?= __('Process') ?></th>
                <td><?= h($stateMachineItem->state_machine_transition_log->state_machine_process->name) ?></td>
            </tr>
            <tr>
                <th><?= __('Event') ?></th>
                <td><?= h($stateMachineItem->state_machine_transition_log->event) ?></td>
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
