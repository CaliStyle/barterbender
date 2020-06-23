{if count($aAuctions) > 0}
    {if !PHPFOX_IS_AJAX}
    <div class="table-responsive">
        <table id="ynauction_table_manage-auction" class="table table-striped table-bordered ynecommerce_full_table">
                <tr>
                    <th>{phrase var='auction'}</th>
                    <th>{phrase var='category'}</th>
                    <th>{phrase var='reserve_price'}</th>
                    <th>{phrase var='buy_now_price'}</th>

                    <th>{phrase var='bid_s'}</th>
                    <th>{phrase var='status'}</th>

                    <th>{phrase var='current_bidder'}</th>
                    <th>{phrase var='winner'}</th>
                    <th>{phrase var='current_bid'}</th>
                    <th>{phrase var='option'}</th>
                </tr>
    {else}
        <table id="page2" style="display: none" class="ynecommerce_full_table">
    {/if}
            {foreach from=$aAuctions key=iKey item=aAuction}
                {php}
                        $aAuction = $this->_aVars['aAuction'];
                {/php}
                <tr id="js_row{$aAuction.product_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td><a href="{url link='auction.detail.'.$aAuction.product_id}">{$aAuction.name|convert|shorten:75:'...'|split:75}</a></td>
                    <td><?php echo Phpfox::isPhrase($aAuction['category_title']) ? _p($aAuction['category_title']) : Phpfox::getLib('locale')->convert($aAuction['category_title']);?></td>
                    <td class="price">{$aAuction.sSymbolCurrency}{$aAuction.auction_item_reserve_price|number_format:2}</td>
                    <td class="price">{$aAuction.sSymbolCurrency}{$aAuction.auction_item_buy_now_price|number_format:2}</td>
                    <td>{$aAuction.auction_total_bid}</td>
                    <td>{phrase var=''$aAuction.product_status}</td>
                    <td>{if $aAuction.latest_bidder_full_name == ''}{else}<a href="{url link=$aAuction.latest_bidder_user_name}">{$aAuction.latest_bidder_full_name|convert|shorten:25:'...'}</a>&nbsp;{/if}</td>
                    <td>{if $aAuction.won_bid_full_name == ''}{else}<a href="{url link=$aAuction.won_bid_user_name}">{$aAuction.won_bid_full_name|convert|shorten:25:'...'}</a>&nbsp;{/if}</td>
                    <td>{if $aAuction.auction_latest_bid_price == 0}{else}{$aAuction.sSymbolCurrency}{$aAuction.auction_latest_bid_price}{/if}</td>
                    <td class="action">
                        {if $aAuction.product_status == 'draft' || $aAuction.product_status == 'denied'}
                           <span><a href="{url link='auction.edit.id_'.$aAuction.product_id.'.action_publish'}">{phrase var='publish'}</a></span>
                        {/if}
                        <span><a href="{url link='auction.dashboard.id_'.$aAuction.product_id}">{phrase var='dashboard'}</a></span>
                        {if $aAuction.product_status != 'completed'}
                            {*if auction has bid hor won,so it cannot be deleted*}
                            <span><a href="javascript:void(0);" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_delete_this_auction"}'{r}, function(){l}$.ajaxCall('auction.deleteAuction', 'iProductId={$aAuction.product_id}');{r}, function(){l}{r}); return false;">{phrase var='delete'}</a></span>
                            {*/if*}
                        {/if}
                        {if $aAuction.product_status == 'running' || $aAuction.product_status == 'bidden' || $aAuction.product_status == 'approved'}
                            {*if auction has bid hor won,so it can be closed*}
                            <span><a href="javascript:void(0);" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_close_this_auction_notice_it_cannot_be_re_opened"}'{r}, function(){l}$.ajaxCall('auction.closeProduct', 'id={$aAuction.product_id}');{r}, function(){l}{r}); return false;">{phrase var='close'}</a></span>
                            {if Phpfox::getUserParam('auction.can_feature_auction')}
                                <span><a href="javascript:void(0);" onclick="ynauction.featureBox({$aAuction.product_id});">{phrase var='feature'}</a></span>
                            {/if}
                            {*/if*}
                        {/if}
                        <span><a href="{url link='auction.add.cloneid_'.$aAuction.product_id}">{phrase var='clone'}</a></span>
                    </td>
                </tr>
            {/foreach}
        </table>
        {pager}
    </div>
{else}
    {if $iPage == 0}
        <div class="p_4">
            {phrase var='no_auctions_have_been_created'}
        </div>
    {/if}
{/if}
{literal}
<script type="text/javascript">
    $Behavior.onLoadSellerSection = function(){
        if ($('#page2').length > 0 && $('#page2 tbody').length > 0 && $('#ynauction_table_manage-auction tbody').length > 0)
        {
            $('#ynauction_table_manage-auction tbody').append($('#page2 tbody').html());
            $('#page2').remove();
        }
    }
</script>
{/literal}
