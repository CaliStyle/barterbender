<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 5:05 PM
 */
?>
<div class="ynstore-product-most-block">
    <ul class="ynstore-product-most-items">
        {foreach from=$aItems item=aItem}
            {template file='ynsocialstore.block.product.entry'}
        {/foreach}
    </ul>
</div>
