<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if $aProductSeller}
<div class="my_won_bids">
    <div class="total_auctions">
        {if $iTotalAuctions==1}
            {phrase var='number_auction' number=$iTotalAuctions}
        {else}
            {phrase var='number_auctions' number=$iTotalAuctions}
        {/if}
    </div>
    <div class="auctions_list">
        {foreach from=$aProductSeller  item=aProducts}
            {php}
                $aSellerInfo = $this->_aVars['aSellerInfo'] ;
                $aProducts = $this->_aVars['aProducts'] ;
                $aMultiCartIds = $this->_aVars['aMultiCartIds'] ;
                $aTotalCarts = $this->_aVars['aTotalCarts'] ;

                $this->_aVars['sSellerName'] = $aSellerInfo[$aProducts[0]['user_id']]['full_name'];
                $this->_aVars['sMultiCartId'] = $aMultiCartIds[$aProducts[0]['user_id']];
                $this->_aVars['iTotalCartPrice'] = $aTotalCarts[$aProducts[0]['user_id']]['total_cart_price'];
            {/php}
            <div class="seller_section_name">
                {phrase var='seller_seller_name' seller_name=$sSellerName}
            </div>
            {foreach from=$aProducts key=keyProduct item=aProduct}
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
                        <div class="auction_bid_number">
                            <a href="javascript:;" onclick="tb_show('{phrase var='my_bidden_history' phpfox_squote=true}', $.ajaxBox('auction.getMyBiddenHistory', 'height=400&amp;width=600&amp;id={$aProduct.product_id}'), null, '', false, 'POST');">
                                <span class="bid_icon"></span>{if $aProduct.auction_total_bid > 1}{phrase var='number_bids' number=$aProduct.auction_total_bid}{else}{phrase var='number_bid' number=$aProduct.auction_total_bid}{/if}
                            </a>
                        </div>
                    </div>
                    <div class="auction_details">
                        <div class="detail1">
                            <div class="auction_winning_bid">{phrase var='winning_bid'}: <span class="currency">{$aProduct.sSymbolCurrency}{$aProduct.auction_won_bid_price|number_format:2}</span></div>
                            <div class="auction_time_left">{phrase var='end'}: {$aProduct.end_time|date:'core.global_update_time'}</div>
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

                    {if isset($aProduct.iEndTimeForBuying) && $aProduct.iEndTimeForBuying > PHPFOX_TIME}
                        <div class="detail2">
                            <div class="add_to_cart_function">
                                <input id="add_to_cart_button_{$aProduct.product_id}" type="button" name="add_to_cart" value="{phrase var='add_to_cart'}" class="btn btn-sm btn-success" onclick="addToCart({$aProduct.product_id});"/>
                                <div id="add_to_cart_loading_{$aProduct.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
                            </div>
                            <span class="time_left">{$aProduct.time_left_for_buying} {phrase var='left_for_buying'}</span>
                        </div>
                    {/if}
                    </div>
                </div>
            </div>
            {/foreach}

            {if isset($aProduct.iEndTimeForBuying) && $aProduct.iEndTimeForBuying > PHPFOX_TIME}
            <div class="seller_summary">
                <div class="holder">&nbsp;</div>
                <div class="summary_info">
                    <div class="cart_price">
                        <span class="total">{phrase var='total'} :</span>
                        <span class="price">{$aProduct.sSymbolCurrency}{$iTotalCartPrice}</span>
                    </div>
                    <div class="add_to_cart_function">
                        <input id="add_to_cart_button_multicart" type="button" name="add_to_cart" value="{phrase var='add_all_to_cart_for_this_seller'}" class="btn btn-sm btn-success" onclick="addMultiToCart('{$sMultiCartId}');"/>
                        <div id="add_to_cart_loading_multicart" style="display: none;">{img theme='ajax/add.gif'}</div>
                    </div>
                </div>
            </div>
            {/if}
        {/foreach}
        {pager}
    </div>
</div>
{else}
    <div>{phrase var='no_auctions_found'}</div>
{/if}

{literal}
<style type="text/css">
    .header_bar_menu{
        display: none;
    }
</style>
<script type="text/javascript">
    function addToCart(iAuctionId)
    {
        $('#add_to_cart_button_' + iAuctionId).prop("disabled", true);
        $('#add_to_cart_loading_' + iAuctionId).show();

        $.ajaxCall('auction.addToCart', 'id=' + iAuctionId + '&type=bid');
    }

    function addMultiToCart(iAuctionIds)
    {

        $('#add_to_cart_button_multicart_' + iAuctionIds).prop("disabled", true);
        $('#add_to_cart_loading_multicart_' + iAuctionIds).show();

        $.ajaxCall('auction.addMultiToCart', 'ids=' + iAuctionIds + '&type=bid');
    }

    $Behavior.initAuctionMyOffersPage = function(){
        ynauction.initAdvancedSearch();
    }
</script>
{/literal}