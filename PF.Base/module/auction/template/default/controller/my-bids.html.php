<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if $aProducts}
<div class="my_bids">
    <div class="total_auctions">
        {if $iTotalAuctionsNotBanned==1}
            {phrase var='number_auction' number=$iTotalAuctionsNotBanned}
        {else}
            {phrase var='number_auctions' number=$iTotalAuctionsNotBanned}
        {/if}
    </div>
    <div class="auctions_list">
        {foreach from=$aProducts item=aProduct}
        {if $aProduct.user_group_id != 5}
        <div class="auction_item">
            <div class="auction_logo">
                {if isset($aProduct.logo_path)}
                    {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400'}
                {else}
                    <img src="{$aProduct.default_logo_path}" alt="">
                {/if}
            </div>
            <div class="auction_info">
                <div class="auction_title">
                    <a href="{$aProduct.link}" title="{$aProduct.name|clean}">{$aProduct.name|clean|shorten:75:'...'|split:75}</a>
                </div>
                <div class="auction_user_bids">
                    <div class="auction_user"><span class="user_icon"></span>{phrase var='by'} <a href="{url link=$aProduct.user_name}" title="{$aProduct.full_name|clean}">{$aProduct.full_name|clean}</a></div>
                    <div class="auction_bid_number"><span class="bid_icon"></span>{if $aProduct.auction_total_bid > 1}{phrase var='number_bids' number=$aProduct.auction_total_bid}{else}{phrase var='number_bid' number=$aProduct.auction_total_bid}{/if}</div>
                </div>
                <div class="auction_details">
                    <div class="detail1">
                        <div class="auction_current_bid">{phrase var='current_bid'}: <span class="currency">{$aProduct.sSymbolCurrency}{$aProduct.auction_latest_bid_price|number_format:2}</span></div>
                        <div class="auction_time_left">{phrase var='time_left'}: {$aProduct.remaining_time}</div>
                        <div class="auction-item-compare">
                            <input type="checkbox"
                                data-compareitemauctionid="{$aProduct.product_id}"
                                data-compareitemname="{$aProduct.name}"
                                data-compareitemlink="{$aProduct.link}"
                                data-compareitemlogopath="{if isset($aProduct.logo_path)}{img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400' return_url=true}{else}
                                    {img server_id=$aProduct.server_id path='' file=$aProduct.default_logo_path suffix='' return_url=true}{/if}"
                                onclick="ynauction.clickCompareCheckbox(this);"
                                class="ynauction-compare-checkbox" /> {phrase var='add_to_compare'}
                        </div>
                    </div>
                    <div class="detail2">
						{if $bCanBidAuction}
							{if ($aProduct.auction_latest_bidder != Phpfox::getUserId())}
		                        <div class="auction_bid_function">
		                            <div class="bid_input"><input type="text" name="val[bid]" value="{if isset($aProduct.fSuggestBidPrice)}{$aProduct.fSuggestBidPrice}{/if}" id="bid_field_{$aProduct.product_id}" class="bid_field form-control"  /></div>
		                            <div class="bid_button">
		                                <div class="place_bid_loading_{$aProduct.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
		                                <input id="bid_button_{$aProduct.product_id}" type="button" name="val[place_bid]" value="{phrase var='place_bid'}" class="button btn btn-success place_bid" onclick="placeBid({$aProduct.product_id});"/>
		                            </div>
		                        </div>
	                        {/if}
						{/if}
                        <div class="auction_bid_history">
                            <a href="javascript:;" onclick="tb_show('{phrase var='my_bidden_history' phpfox_squote=true}', $.ajaxBox('auction.getMyBiddenHistory', 'height=400&amp;width=600&amp;id={$aProduct.product_id}'), null, '', false, 'POST');">{phrase var='my_bidden_history'}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/if}
        {/foreach}
    </div>
</div>
{else}
    <div>{phrase var='no_auctions_found'}</div>
{/if}

{literal}
<script type="text/javascript">
    function placeBid(iAuctionId)
    {
        $('#bid_button_' + iAuctionId).prop("disabled", true);
        $('.place_bid_loading_' + iAuctionId).show();

        var fBidValue = $("#bid_field_" + iAuctionId).val();

        $.ajaxCall('auction.placeBid', 'value=' + fBidValue + '&id=' + iAuctionId);
    }
    $Behavior.initAuctionMyBidsPage = function(){
        ynauction.initAdvancedSearch();
    }
</script>
{/literal}