<?php

?>

<h1>Product List</h1>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Actions</th>
            <th>ModifyDate</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product) : ?>
            <tr>
                <td><?= h($product->name) ?></td>
                <td><?= h($product->price) ?></td>
                <td><?= h($product->quantity) ?></td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'get', $product->id]) ?>
                </td>
                <td>
                    <?= $this->Html->link('ModifyDate', ['action' => 'addProductionDate', $product->id]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>