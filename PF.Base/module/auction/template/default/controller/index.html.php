<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if $iPage == 0}
<div id="ynauction_index">
	<div class="ynauction-hiddenblock">
		<input type="hidden" value="index" id="ynauction_pagename" name="ynauction_pagename">
	</div>
	<div>
		<input type="hidden" id='ynauction_condition' name="ynauction_condition" value="{if isset($sCondition)}{$sCondition}{/if}">
	</div>
	<div>
	{/if}
        {if $bIsHomepage}
        <div id="yndirectory_homepage">
                {module name='auction.featured-auctions'}
                {module name='auction.weekly-hot-auctions'}
                {module name='auction.new-auctions' page=$iPage viewType=$sViewType aItem=$aItems iCnt=$iCnt}
        </div>

        {else}
        {if !count($aItems)}
            {if $iPage <=1}
            <div class="extra_info">
                {phrase var='no_auctions_found'}
            </div>
            {/if}
        {else}
            {if ($sView != 'myauctions') && $bIsProfile == false}
                {if $sView == 'friend'}
                    <div class="ynauction-content">
                        <div id="ynauction_listview" class="ynauction_listview homepage-view ynauction-clearfix">
                            {foreach from=$aItems item=aProduct name=auction}
                                {if $aProduct.user_group_id != 5}
                                    {template file='auction.block.listing-product-item-listview'}
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                {elseif $sView == 'bidden-by-my-friends'}
                    <div class="ynauction-content">
                        <div id="ynauction_listview" class="ynauction_listview homepage-view ynauction-clearfix">
                            {foreach from=$aItems item=aProduct name=auction}
                                {if $aProduct.usergroupId != 5}
                                    {template file='auction.block.listing-product-item-listview'}
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                {else}
                    {if $iPage == 0}
                        {if (isset($sortTitle) && ($sortTitle != ""))}
                            <h1><a href="{$sortUrl}">{$sortTitle}</a></h1>
                        {/if}
                    {/if}
                    <div class="ynauction-content">
                        <div id="ynauction_listview" class="ynauction_listview homepage-view ynauction-clearfix">
                            {foreach from=$aItems item=aProduct name=auction}
                                {template file='auction.block.listing-product-item-listview'}
                            {/foreach}
                        </div>
                    </div>
                {/if}
            {elseif $sView == 'myauctions'  || $bIsProfile}
                <div class="ynauction-content">
                    <div id="ynauction_gridview" class="ynauction_gridview">
                        {foreach from=$aItems item=aProduct name=auction}
                            {template file='auction.block.listing-product-item-gridview'}
                        {/foreach}
                    </div>
                </div>
            {/if}
            {pager}
            {if $sView == 'myauctions' || $sView == 'pending'}
                 {if !PHPFOX_IS_AJAX && $bShowModerator}
                     {moderation}
                 {/if}
            {/if}
                    <div class="clear"></div>
                {/if}
        {/if}
{if $iPage == 0}
    </div>
</div>
{/if}
{literal}
<script>
var first = 0;
    $Behavior.initAuctionIndexPage = function(){
        ynauction.changeViewHomePage(first);
        if(first == 0){
            first = 1;
        }
        ynauction.initAdvancedSearch();
    }
</script>
{/literal}