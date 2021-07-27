<?php
/**
 * @var \App\View\AppView $this
 * @var string $stateMachineName
 * @var int[] $matrix
 */
?>

<nav class="large-3 medium-4 columns col-sm-4 col-12" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="nav-item heading"><?= __('Actions') ?></li>
        <li class="nav-link"><?= $this->Html->link(__('Back'), ['action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="large-9 medium-8 columns content col-sm-8 col-12">
    <h1>Matrix for "<?php echo h($stateMachineName); ?>" state machine</h1>

    <?php foreach ($matrix as $state => $itemCount) { ?>
        <div class="large-4 medium-6 columns col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><?php echo h($state); ?></h2>
                </div>
                <div class="panel-body">
                    <?php
                    echo $this->Html->link($itemCount, ['controller' => 'StateMachineItems', 'action' => 'index', '?' => ['state' => $state]]);
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
