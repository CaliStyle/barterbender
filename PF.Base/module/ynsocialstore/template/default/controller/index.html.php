<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !PHPFOX_IS_AJAX} <!-- BEGIN IF NOT AJAX -->
<div id="ynsocialstore_indexproduct">
    <div class="ynsocialstore-hiddenblock">
        <input type="hidden" value="indexproduct" id="ynsocialstore_pagename" name="ynsocialstore_pagename">
    </div>
</div>
{module name='ynsocialstore.product.search'}
{/if} <!-- END IF NOT AJAX -->

{if !$bIsHomepage}
    {if count($aProducts)}
    {if !PHPFOX_IS_AJAX} <!-- BEGIN IF NOT AJAX -->
        {if isset($sView) && $sView == 'friendbuy'}
            <div class="yn-viewmode-grid ynstore-product-friend-buy-page" id="js_block_border_ynsocialstore_product_listingproduct">
        {else}
            <div class="ynstore-view-modes-block yn-viewmode-grid" id="js_block_border_ynsocialstore_product_listingproduct">
        {/if}
        <div class="yn-view-modes yn-nomargin">
            <span data-mode="grid" class="yn-view-mode"><i class="ico ico-th"></i></span>
            <span data-mode="list" class="yn-view-mode"><i class="ico ico-list"></i></span>
        </div>

        <ul class="ynstore-items">
    {/if} <!-- END IF NOT AJAX -->
            {foreach from=$aProducts key=iKey item=aItem}
                {template file='ynsocialstore.block.product.entry'}
            {/foreach}
            {pager}
            {if (Phpfox::isAdmin() || !empty($bShowModeration)) && empty($bIsNoModerate)}
                {moderation}
            {/if}
    {if !PHPFOX_IS_AJAX} <!-- BEGIN IF NOT AJAX -->
        </ul>
        <div id="ynstore_new_store_map" style="width: 100%;height:400px;" ></div>
    </div>
    {/if} <!-- END IF NOT AJAX -->
    {literal}
    <script type="text/javascript">
        $Behavior.initViewMode = function(){
            ynsocialstore.initViewMode('js_block_border_ynsocialstore_product_listingproduct');
        }
    </script>
    {/literal}
    {elseif $iPage <= 1}
        <div class="extra_info">
            {_p var='ynsocialstore.no_products_found'}
        </div>
    {/if}
{/if}