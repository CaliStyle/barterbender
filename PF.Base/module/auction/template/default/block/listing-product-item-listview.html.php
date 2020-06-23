<?php

defined('PHPFOX') or exit('NO DICE!');

 ?>
<div class="auction-item" id ="js_auction_entry{$aProduct.product_id}">
    <div>
        {if ($sView == 'pending')}
        <div class="moderation_row">
            <label class="item-checkbox moderation_row">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aProduct.product_id}" id="check{$aProduct.product_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        <div class="row_edit_bar_parent">
            <div class="row_edit_bar_holder">
                <ul>
                </ul>
            </div>
            <div class="row_edit_bar">
            </div>
        </div>

        {/if}
    </div>
    <div class="auction-item-content">
        <div class="auction-item-images">
            <a href="{permalink module='auction.detail' id=$aProduct.product_id title=$aProduct.name}" title="{$aProduct.name|clean}">
                <span class="ynauction-photo-span" style="background-image: url(
                {if isset($aProduct.logo_path)}
                    {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400' return_url=true}
                {else}
                    {$aProduct.default_logo_path}
                {/if}
                )"></span>
            </a>
            <div class="watch_group">
                {if $sView == 'my-watch-list'}
                <a href="#{$aProduct.product_id}" onClick="$.ajaxCall('auction.removeFromWatchList', 'item_id={$aProduct.product_id}'); return false;" auctionid="{$aProduct.product_id}" >{phrase var='remove'}</a>
                {/if}
            </div>
        </div>
    	<div class="auction-item-info table_row">
            <div class="auction-item-info-content">
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
            <div class="auction-item-time-remain">
                {if $aProduct.end_time > PHPFOX_TIME}
                    <span>{$aProduct.remaining_time}</span>
                {/if}
                <span>
                     <span class="total_bids {if $aProduct.remaining_time == ''}none{/if}">{$aProduct.auction_total_bid}</span> {phrase var='bid_s'}
                </span>
            </div>
            <div class="auction-item-short-description item_view_content">
                {$aProduct.description|parse|strip_tags|clean|shorten:200:'...'|split:200}
            </div>
            <div class="auction-item-bids-owner">
                <div class="ynauction-md-6"><a href="{url link=$aProduct.user_name}" title="{$aProduct.full_name|clean}">{$aProduct.full_name|clean}</a></div>
                <div class="ynauction-md-6">
                    {if (isset($aProfileUser) && $bIsProfile) || $bIsInPages}
                    {else}
                    <div class="checkbox">
                        <label for="">
                            <input type="checkbox"
                                   data-path="{$sCorePath}"
                                   data-compareitemauctionid="{$aProduct.product_id}"
                                   data-compareitemname="{$aProduct.name}"
                                   data-compareitemlink="{permalink module='auction.detail' id=$aProduct.product_id title=$aProduct.name}"
                                   data-compareitemlogopath="{if isset($aProduct.logo_path)}{img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400' return_url=true}{else}
                                    {img server_id=$aProduct.server_id path='' file=$aProduct.default_logo_path suffix='' return_url=true}{/if}"
                                   onclick="ynauction.clickCompareCheckbox(this);"
                                   class="ynauction-compare-checkbox" /> {phrase var='add_to_compare'}
                        </label>
                    </div>
                    {/if}
                </div>
            </div>
            {if ($sView == 'myauctions')}
                <a href="#{$aProduct.product_id}" auctionid="{$aProduct.product_id}" class="moderate_link" rel="auction"><i class="fa"></i></a>
            {/if}
            </div>
        </div>
    </div>
</div>