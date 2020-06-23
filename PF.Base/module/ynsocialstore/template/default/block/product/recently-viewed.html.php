<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/27/16
 * Time: 10:32 AM
 */
?>
<div class="ynstore-product-most-block">
    <ul class="ynstore-product-most-items">
        {foreach from=$aItems item=aItem}
            {template file='ynsocialstore.block.product.entry'}
        {/foreach}
    </ul>
</div>
