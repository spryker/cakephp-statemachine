<?php
/**
 * @var \App\View\AppView $this
 * @var \StateMachine\Model\Entity\StateMachineLock[]|\Cake\Collection\CollectionInterface $stateMachineLocks
 */
?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Html->link(__('Back'), ['controller' => 'StateMachine', 'action' => 'index']) ?></li>
    </ul>
</nav>
<div class="content action-index index large-9 medium-8 columns col-sm-8 col-12">
    <h2><?= __('State Machine Locks') ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('identifier') ?></th>
                <th><?= $this->Paginator->sort('expires', null, ['direction' => 'desc']) ?></th>
                <th><?= $this->Paginator->sort('created', null, ['direction' => 'desc']) ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateMachineLocks as $stateMachineLock): ?>
            <tr>
                <td><?= h($stateMachineLock->identifier) ?></td>
                <td><?= $this->Time->nice($stateMachineLock->expires) ?></td>
                <td><?= $this->Time->nice($stateMachineLock->created) ?></td>
                <td class="actions">
                <?= $this->Html->link($this->Format->icon('view'), ['action' => 'view', $stateMachineLock->id], ['escapeTitle' => false]); ?>
                <?= $this->Form->postLink($this->Format->icon('delete'), ['action' => 'delete', $stateMachineLock->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $stateMachineLock->id)]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->element('Tools.pagination'); ?>
</div>
