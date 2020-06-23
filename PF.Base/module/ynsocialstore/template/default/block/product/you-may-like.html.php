<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/15/16
 * Time: 11:59 AM
 */
?>
<div class="ynstore-product-most-block">
    <ul class="ynstore-product-most-items">
        {foreach from=$aProducts item=aItem}
            {template file='ynsocialstore.block.product.entry'}
        {/foreach}
    </ul>
</div>
