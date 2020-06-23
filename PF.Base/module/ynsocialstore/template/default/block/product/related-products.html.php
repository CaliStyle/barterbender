<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/3/16
 * Time: 09:35
 */
?>
<div class="ynstore-product-most-block">
    <ul class="ynstore-product-most-items">
        {foreach from=$aItems item=aItem}
            {template file='ynsocialstore.block.product.entry'}
        {/foreach}
    </ul>
</div>
