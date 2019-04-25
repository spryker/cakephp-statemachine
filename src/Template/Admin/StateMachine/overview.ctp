<?php
/**
 * @var \App\View\AppView $this
 * @var string $stateMachineName
 * @var int[] $matrix
 */
?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav nav nav-pills nav-stacked">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Back'), ['action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="large-9 medium-8 columns content">
    <h1>Matrix for "<?php echo h($stateMachineName); ?>" state machine</h1>

    <?php foreach ($matrix as $state => $itemCount) { ?>
        <div class="large-4 medium-6 columns col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><?php echo h($state); ?></h2>
                </div>
                <div class="panel-body">
                    <?php
                    echo $itemCount;
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
