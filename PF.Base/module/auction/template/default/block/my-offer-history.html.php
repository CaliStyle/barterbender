<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($aProduct.product_id)}
<div id="my_offer_history_body">
    <div class="offers_info">
        <div class="my_offers">
            <span class="auction_label">{phrase var='my_offers'}:</span>
        <span class="auction_value">{$iTotalOffers}</span>
        </div>
    </div>
    <div id="my_offer_history_loading" style="display: none;">{img theme='ajax/large.gif'}</div>
    <div class="list_holder" id="my_offer_history_holder">
        {module name='auction.my-offer-history-list' aProduct=$aProduct sCustomPagination=$sCustomPagination aRows=$aRows}
    </div>
    {if $bCanMakeOffer}
        <div class="auction_offer_function">
            <div class="offer_input"><input type="text" name="val[offer]" value="{$fSuggestOfferPrice}" id="popup_offer_field_{$aProduct.product_id}" class="offer_field form-control" /></div>
            <div class="offer_button">
                <div class="popup_place_offer_loading_{$aProduct.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
                <button id="popup_offer_button_{$aProduct.product_id}" type="button" name="val[make_offer]" class="btn btn-sm btn-warning" onclick="makeOfferPopup({$aProduct.product_id});">{phrase var='make_offer'}</button>
            </div>
        </div>
    {/if}
</div>
<div id="my_offer_history_success" class="message" style="display: none;">{phrase var='make_offer_successfully'}</div>
{else}
    <div class="error_message">{phrase var='auction_is_not_valid'}</div>
{/if}

{literal}
<script type="text/javascript">
    function paginationAjaxReload(sParams)
    {
        $('#my_offer_history_loading').show();
        $('#my_offer_history_holder').hide();
        $.ajaxCall('auction.reloadMyOfferHistory', sParams);
    }
    function makeOfferPopup(iAuctionId)
    {
        $('#popup_offer_button_' + iAuctionId).prop("disabled", true);
        $('.popup_place_offer_loading_' + iAuctionId).show();

        var fOfferValue = $("#popup_offer_field_" + iAuctionId).val();

        $.ajaxCall('auction.makeOffer', 'value=' + fOfferValue + '&id=' + iAuctionId + '&popup=1');
    }
</script>
{/literal}