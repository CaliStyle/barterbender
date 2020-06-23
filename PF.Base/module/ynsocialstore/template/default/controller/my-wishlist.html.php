<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:35 PM
 */
?>

{if count($aMyWishList)}
    <!--  Check load ajax  -->
    {if !PHPFOX_IS_AJAX}
    <div class="ynstore-view-modes-block yn-viewmode-grid" id="js_block_border_ynsocialstore_product_mywishlistproduct">
        <div class="yn-view-modes">
            <span data-mode="grid" class="yn-view-mode"><i class="ico ico-th"></i></span>
            <span data-mode="list" class="yn-view-mode"><i class="ico ico-list"></i></span>
        </div>

        <ul class="ynstore-items">
    <!--Endif load ajax-->
    {/if}
            {foreach from=$aMyWishList key=iKey item=aItem}
                {template file='ynsocialstore.block.product.entry' bIsWishList=true}
            {/foreach}
            {pager}
    <!--  Check load ajax  -->
    {if !PHPFOX_IS_AJAX}
        </ul>
    </div>
    {literal}
    <script type="text/javascript">
        $Behavior.initViewMode = function(){
            ynsocialstore.initViewMode('js_block_border_ynsocialstore_product_mywishlistproduct');
        }
    </script>
    {/literal}
    <!--Endif load ajax-->
    {/if}
{elseif !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {_p var='ynsocialstore.no_products_found'}
    </div>
{/if}

