<?php

?>
<a href="<?= $this->Url->build(['controller' => 'Products', 'action' => 'add'], ['fullBase' => true]) ?>">상품추가</a>

<h1>Product List</h1>

<div class="tabs" id="touchArea">
    <ul>
        <li>Categories</li>
        <li><a href="">All</a></li>
        <li><a href="">Featured</a></li>
        <li><a href="">News</a></li>
        <li><a href="">Events</a></li>
        <li><a href="">Sports</a></li>
        <li><a href="">Business</a></li>
        <li><a href="">Money</a></li>
        <li><a href="">Travel</a></li>
        <li><a href="">Environment</a></li>
        <li><a href="">Technology</a></li>
        <li><a href="">리스트</a></li>
    </ul>
</div>

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

<script>
    const touchArea = document.getElementById('touchArea');

    let isTouching = false;
    let startX;
    let scrollLeft;

    touchArea.addEventListener('touchstart', function(e) {
        isTouching = true;
        startX = e.touches[0].pageX - touchArea.offsetLeft;
        scrollLeft = touchArea.scrollLeft;
    });

    touchArea.addEventListener('touchmove', function(e) {
        if (!isTouching) return;
        const x = e.touches[0].pageX - touchArea.offsetLeft;
        const walk = (x - startX) * 2; // Increase the multiplier to scroll faster
        touchArea.scrollLeft = scrollLeft - walk;
        e.preventDefault(); // Prevent default scrolling behavior
    });

    touchArea.addEventListener('touchend', function() {
        isTouching = false;
    });
</script>