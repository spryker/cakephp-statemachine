<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineLock $stateMachineLock
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Form->postLink(__('Delete State Machine Lock'), ['action' => 'delete', $stateMachineLock->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stateMachineLock->id)]) ?> </li>
        <li class="nav-link"><?= $this->Html->link(__('List State Machine Locks'), ['action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="content action-view view large-9 medium-8 columns col-sm-8 col-12">
    <h2><?= h($stateMachineLock->id) ?></h2>
    <table class="table vertical-table">
            <tr>
            <th><?= __('Identifier') ?></th>
            <td><?= h($stateMachineLock->identifier) ?></td>
        </tr>
            <tr>
            <th><?= __('Expires') ?></th>
            <td><?= $this->Time->nice($stateMachineLock->expires) ?></td>
        </tr>
            <tr>
            <th><?= __('Created') ?></th>
            <td><?= $this->Time->nice($stateMachineLock->created) ?></td>
        </tr>
    </table>

</div>
