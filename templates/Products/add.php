<h1>Add Product</h1>
<?= $this->Form->create() ?>
<?= $this->Form->control('name', ['label' => 'Product Name']) ?>
<?= $this->Form->control('price', ['label' => 'Price']) ?>
<?= $this->Form->control('quantity', ['label' => 'Quantity']) ?>
<?= $this->Form->control('origin', ['label' => 'Origin']) ?>
<?= $this->Form->control('email', ['label' => 'Email']) ?>
<?= $this->Form->control('password', ['label' => 'Password', 'type' => 'password']) ?>
<?= $this->Form->button(__('Submit')) ?>
<?= $this->Form->end() ?>