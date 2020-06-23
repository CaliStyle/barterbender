<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/28/16
 * Time: 10:26
 */
?>
{if count($aNewProducts)}
    <div class="ynstore-view-modes-block yn-viewmode-grid">
        <div class="yn-view-modes">
            <span data-mode="grid" class="yn-view-mode"><i class="ico ico-th"></i></span>
            <span data-mode="list" class="yn-view-mode"><i class="ico ico-list"></i></span>
        </div>

        <ul class="ynstore-items">
            {foreach from=$aNewProducts name=product item=aItem}
                {template file='ynsocialstore.block.product.entry'}
            {/foreach}
        </ul>
    </div>

    {literal}
    <script type="text/javascript">
        $Behavior.initViewMode = function(){
            ynsocialstore.initViewMode('js_block_border_ynsocialstore_product_new-arrivals');
        }
    </script>
    {/literal}
{else}
    {_p('no_products_found')}
{/if}