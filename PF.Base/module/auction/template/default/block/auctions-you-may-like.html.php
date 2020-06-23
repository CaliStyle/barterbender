<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if $aAuctionsYouMayLike}
	<div class="ynauction-suggested-block">
		<h3>{phrase var='auctions_you_may_like'}</h3>
		<div id="ynauction_gridview">
			{foreach from=$aAuctionsYouMayLike item=aProduct name=auction}
                <div class="auction-item image_hover_holder " id ="js_auction_entry{$aProduct.product_id}">
                    <div class="auction-item-content">

                        <div class="auction-item-info">
                            <div class="auction-item-images">
                                <a href="{permalink module='auction.detail' id=$aProduct.product_id title=$aProduct.name}" title="{$aProduct.name|clean}">
                                    {if isset($aProduct.logo_path)}
                                    {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_100_square'}
                                    {else}
                                    <img src="{$aProduct.default_logo_path}" alt="">
                                    {/if}
                                </a>

                                <div class="auction-item-title">
                                    <a href="{permalink module='auction.detail' id=$aProduct.product_id title=$aProduct.name}" id="js_auction_edit_inner_title{$aProduct.product_id}" class="link ajax_link ynauction-text-overflow">{$aProduct.name|clean|shorten:75:'...'|split:75}</a>
                                </div>
                                <div class="auction-item-price">
                                    {$aProduct.sSymbolCurrency}{$aProduct.auction_latest_bid_price|number_format:2}
                                </div>

                                <div class="auction_owner">
                                    <span class="auction_user_icon"></span> {phrase var='by'} <a href="{url link=$aProduct.user_name}" title="{$aProduct.full_name|clean}">{$aProduct.full_name|clean|shorten:25:'...'|split:25}</a>
                                </div>

                                <div class="auction-item-time-remain">
                                    {$aProduct.remaining_time}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			{/foreach}
		</div>
	</div>
{/if}