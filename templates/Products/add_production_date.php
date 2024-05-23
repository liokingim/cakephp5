<h1>Add Production Date for <?= h($product->name) ?></h1>

<?php if ($latestProductionDate) : ?>
    <h3>Latest Production Date:</h3>
    <p>Year: <?= h($latestProductionDate->birthYear) ?></p>
    <p>Month: <?= h($latestProductionDate->birthMonth) ?></p>
    <p>Day: <?= h($latestProductionDate->birthDay) ?></p>
<?php else : ?>
    <h3>No production date found.</h3>
<?php endif; ?>

<?= $this->Form->create($productionDate, ['url' => ['controller' => 'Products', 'action' => 'registProductionDate']]) ?>
<fieldset>
    <legend><?= __('Enter Production Date') ?></legend>
    <?= $this->Form->control('birth_year', ['label' => 'Year', 'value' => $latestProductionDate ? $latestProductionDate->birthYear : '2024']) ?>
    <?= $this->Form->control('birth_month', ['label' => 'Month', 'value' => $latestProductionDate ? $latestProductionDate->birthMonth : '7']) ?>
    <?= $this->Form->control('birth_day', ['label' => 'Day', 'value' => $latestProductionDate ? $latestProductionDate->birthDay : '20']) ?>
</fieldset>
<?= $this->Form->button(__('Submit')) ?>
<?= $this->Form->end() ?>

<?= $this->Html->link(__('Back to Products'), ['action' => 'index']) ?>