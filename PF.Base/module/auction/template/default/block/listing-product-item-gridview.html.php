<?php

defined('PHPFOX') or exit('NO DICE!');

 ?>
<div class="auction-item image_hover_holder " id ="js_auction_entry{$aProduct.product_id}">
    <div class="auction-item-content">
    	<div class="auction-item-info">
    		<div class="auction-item-images">
    	        <a href="{permalink module='auction.detail' id=$aProduct.product_id title=$aProduct.name}" title="{$aProduct.name|clean}">
                    <span class="ynauction-photo-span" style="background-image: url(
                    {if isset($aProduct.logo_path)}
                        {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400' return_url=true}
                    {else}
                        {$aProduct.default_logo_path}
                    {/if}
                    )"></span>
                    <span class="ynauction-photo-pinterest">
                        {if isset($aProduct.logo_path)}
                            {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400'}
                        {else}
                            <img src="{$aProduct.default_logo_path}" alt="">
                        {/if}
                    </span>
                </a>
            </div>
            {if (isset($aProfileUser) && $bIsProfile) || $bIsInPages}
            {else}
            <div class="auction-item-compare">
                <input type="checkbox"
                       data-compareitemauctionid="{$aProduct.product_id}"
                       data-compareitemname="{$aProduct.name}"
                       data-compareitemlink="{permalink module='auction.detail' id=$aProduct.product_id title=$aProduct.name}"
                       data-compareitemlogopath="{if isset($aProduct.logo_path)}{img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400' return_url=true}{else}
                                    {img server_id=$aProduct.server_id path='' file=$aProduct.default_logo_path suffix='' return_url=true}{/if}"
                       onclick="ynauction.clickCompareCheckbox(this);"
                       class="ynauction-compare-checkbox" /> {phrase var='add_to_compare'}
            </div>
            {/if}

            <div class="auction-item-title-price">
                <div class="auction-item-title">
                    <a href="{permalink module='auction.detail' id=$aProduct.product_id title=$aProduct.name}" id="js_auction_edit_inner_title{$aProduct.product_id}" class="link ajax_link ynauction-text-overflow">{$aProduct.name|clean|shorten:75:'...'|split:75}</a>
                </div>
                <div class="auction-item-price">
                     {if $aProduct.auction_latest_bid_price == 0.00}
                        {$aProduct.sSymbolCurrency}{$aProduct.auction_item_reserve_price|number_format:2}
                    {else}
                        {$aProduct.sSymbolCurrency}{$aProduct.auction_latest_bid_price|number_format:2}
                    {/if}
                </div>
            </div>
            <div class="auction-item-time-remain">
            	{if $sView == 'myauctions'  || $bIsProfile}
	            	{if ($aProduct.product_status == 'running') || ($aProduct.product_status == 'bidden')}
	            		{$aProduct.remaining_time}
	            	{else}
            			{phrase var=''.$aProduct.product_status}
	            	{/if}
	            {else}
	            	{$aProduct.remaining_time}
            	{/if}
            </div>
            <div class="auction-item-bids-owner">
                <div class="ynauction-md-6">
                    <span class="auction_bid_icon"></span>
                    {$aProduct.auction_total_bid}
                </div>
                <div class="ynauction-md-6">
                    <a href="{url link=$aProduct.user_name}" title="{$aProduct.full_name|clean}">
                        <span class="auction_user_icon"></span>
                        {$aProduct.full_name|clean}</a>
                </div>
            </div>

            {if ($sView == 'myauctions')}
                <a href="#{$aProduct.product_id}" auctionid="{$aProduct.product_id}" class="moderate_link" rel="auction"><i class="fa"></i></a>
            {/if}

        </div>
    </div>
</div>