<?php

?>

<h1>Product Details</h1>
<?php if ($product) : ?>
    <p>Name: <?= h($product['name']) ?></p>
    <p>Price: <?= h($product['price']) ?></p>
    <p>Quantity: <?= h($product['quantity']) ?></p>
    <p>Origin: <?= h($product['origin']) ?></p>
    <p>Email: <?= h($product['email']) ?></p>
<?php else : ?>
    <p>Product not found.</p>
<?php endif; ?>