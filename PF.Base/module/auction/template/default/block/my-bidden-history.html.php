<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($aAuction.product_id)}
<div id="my_bidden_history_body">
    <div class="biddens_info">
        <div class="total_bidders"><span class="auction_label">{phrase var='bidders'}:</span> <span class="auction_value">{$iTotalBidders}</span> </div>
        <div class="total_bids"><span class="auction_label">{phrase var='bids'}:</span> <span class="auction_value">{$iTotalBids}</span> </div>
        {if $aAuction.time_left != ''}
            <div class="time_left"><span class="auction_label">{phrase var='time_left'}:</span> <span class="auction_value">{$aAuction.time_left}</span> </div>
        {/if}
        <div class="time_left"><span class="auction_label">{phrase var='duration'}:</span> <span class="auction_value">{$aAuction.duration}</span> </div>
    </div>
    <div id="my_bidden_history_loading" style="display: none;">{img theme='ajax/large.gif'}</div>
    <div class="list_holder" id="my_bidden_history_holder">
        {module name='auction.my-bidden-history-list' aAuction=$aAuction sCustomPagination=$sCustomPagination aRows=$aRows}
    </div>
    {if $bCanBidAuction && $aAuction.end_time > PHPFOX_TIME && $aAuction.product_status != 'draft' && $aAuction.product_status != 'pending' && $aAuction.product_status != 'denied'}
	        <div class="auction_bid_function">
	            {if ($aAuction.auction_latest_bidder != Phpfox::getUserId())}
		            <div class="bid_input"><input type="text" name="val[bid]" value="{$fSuggestBidPrice}" id="popup_bid_field_{$aAuction.product_id}" class="bid_field form-control" /></div>
		            <div class="bid_button">
		                <div class="popup_place_bid_loading_{$aAuction.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
		                <button id="popup_bid_button_{$aAuction.product_id}" type="button" name="val[place_bid]" class="btn btn-success" onclick="placeBidPopup({$aAuction.product_id});">{phrase var='place_bid'}</button>
		            </div>
	            {/if}
	        </div>
    {/if}
</div>
<div id="my_bidden_history_success" class="message" style="display: none;">{phrase var='bid_placed_successfully'}</div>
{else}
    <div class="error_message">{phrase var='auction_is_not_valid'}</div>
{/if}

{literal}
<script type="text/javascript">
    function paginationAjaxReload(sParams)
    {
        $('#my_bidden_history_loading').show();
        $('#my_bidden_history_holder').hide();
        $.ajaxCall('auction.reloadMyBiddenHistory', sParams);
    }
    function placeBidPopup(iAuctionId)
    {
        $('#popup_bid_button_' + iAuctionId).prop("disabled", true);
        $('.popup_place_bid_loading_' + iAuctionId).show();

        var fBidValue = $("#popup_bid_field_" + iAuctionId).val();

        $.ajaxCall('auction.placeBid', 'value=' + fBidValue + '&id=' + iAuctionId + '&popup=1');
    }
</script>
{/literal}