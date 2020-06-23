<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="most_liked_auctions" class="">
    <ul>
        {foreach from=$aMostLikedAuctions item=aAuction}
            <li>
                <div class="auction_photo_info">
                    <div class="auction_icon">
                        <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}">
                            {if isset($aAuction.logo_path)}
                            {img server_id=$aAuction.server_id path='core.url_pic' file=$aAuction.logo_path suffix='_100_square'}
                            {else}
                            <img src="{$aAuction.default_logo_path}" alt="">
                            {/if}
                        </a>
                    </div>
                </div>
                <div class="auction_info">
                    <div class="auction_title">
                        <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}">
                            {$aAuction.name|clean|shorten:25:'...'|split:25}
                        </a>
                    </div>
                    <div class="auction_price">
                        {if $aAuction.auction_latest_bid_price == '0.00'}
                           {$aAuction.sSymbolCurrency}{$aAuction.auction_item_reserve_price|number_format:2}
                        {else}
                            {$aAuction.sSymbolCurrency}{$aAuction.auction_latest_bid_price|number_format:2}
                        {/if}
                    </div>
                    <div class="auction_owner">
                        <span class="auction_user_icon"></span> {phrase var='by'} <a href="{url link=$aAuction.user_name}" title="{$aAuction.full_name|clean}">{$aAuction.full_name|clean|shorten:25:'...'|split:10}</a>
                    </div>
                    <div class="auction_end_time">
                        <span class="auction_clock_icon"></span>{$aAuction.remaining_time}
                    </div>
                    <div class="auction_bidden_info">
                        <div class="auction_bid_number">
                            {if $aAuction.auction_total_bid == 1}
                                <span class="auction_bid_icon"></span> {phrase var='number_bid' number=$aAuction.auction_total_bid}
                            {else}
                                <span class="auction_bid_icon"></span> {phrase var='number_bids' number=$aAuction.auction_total_bid}
                            {/if}
                        </div>
                        <div class="auction_like_number">
                            {if $aAuction.total_like == 1}
                                <span class="auction_like_icon"></span> {phrase var='number_like' number=$aAuction.total_like}
                            {else}
                                <span class="auction_like_icon"></span> {phrase var='number_likes' number=$aAuction.total_like}
                            {/if}
                        </div>
                    </div>
                </div>
            </li>
        {/foreach}
    </ul>
</div>