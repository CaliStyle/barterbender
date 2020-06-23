{if count($aAuctionsHomepage) > 0}
<div id="ynauction_block_homepage">
    <div class="ynauction_headline">
        <div id="ynauction_menu">
            {if isset($viewType)}<input type="hidden" value="{$viewType}" id="ynauction_menu_viewtype" name="ynauction_menu_viewtype" >{/if}
            <input type="hidden" value="0" id="ynauction_menu_viewtype_addcookie" name="ynauction_menu_viewtype_addcookie" >
            <input type="hidden" value="0" id="ynauction_menu_viewtype_addcookie_triggerclick" name="ynauction_menu_viewtype_addcookie_triggerclick" >
            <div title="{phrase var='list_view'}" id="ynauction_listview_menu" value="listview" class="homepage-view-menu view-menu-active"></div>
            <div title="{phrase var='grid_view'}" id="ynauction_gridview_menu" value="gridview" class="homepage-view-menu"></div>
            <div title="{phrase var='pinboard_view'}" id="ynauction_pinboardview_menu"  value="pinboardview"  class="homepage-view-menu"></div>
        </div>
        <div id="ynauction_title"> New Auctions</div>
    </div>

    <input type="hidden" value="listview" id="ynauction_view_hompage_input" name="ynauction_view_hompage_input">

    <div id="ynauction_view_hompage">

        <div id="ynauction_listview" class="ynauction_listview homepage-view" style="display:none;">
            {foreach from=$aAuctionsHomepage item=aProduct name=auction}
            {template file='auction.block.listing-product-item-listview'}
            {/foreach}
        </div>

        <div id="ynauction_gridview" class="ynauction_gridview homepage-view" style="display:none;">
            {foreach from=$aAuctionsHomepage item=aProduct name=auction}
            {template file='auction.block.listing-product-item-gridview'}
            {/foreach}
        </div>

        <div id="ynauction_pinboardview" class="ynauction_pinboardview homepage-view" style="display:none;">
            {foreach from=$aAuctionsHomepage item=aProduct name=auction}
            {template file='auction.block.listing-product-item-pinboardview'}
            {/foreach}
        </div>
    </div>
</div>

<div class="clear"></div>
{else}
<div class="extra_info">
    {phrase var='no_auctions_found'}
</div>
{/if}

