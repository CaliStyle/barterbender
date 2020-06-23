<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/28/16
 * Time: 10:26
 */
?>
<div class="yn-viewmode-grid">
    <ul class="ynstore-items">
        {foreach from=$aProducts name=product item=aItem}
            {template file='ynsocialstore.block.product.entry'}
        {/foreach}
    </ul>
</div>