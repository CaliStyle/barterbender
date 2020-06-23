<div class="block">
    <div class="">
        <ul class="ynauction-detailcheckinlist">
            {if ($aAuction.product_status != 'draft')}
                <li>
                    <a id="ynauction_detailcheckinlist_comparebutton" auctionid="{$aAuction.product_id}" href="javascript:void(0)" onclick="ynauction.click_ynauction_detailauction_comparebutton(this, {$aAuction.product_id}); return false;"><i class="fa fa-files-o"></i> {phrase var='add_to_compare'}</a>
                    <div style="display: none;">
                        <input type="checkbox" 
                            data-compareitemauctionid="{$aAuction.product_id}"
                            data-compareitemname="{$aAuction.name|clean}"
                            data-compareitemlink="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}"
                            data-compareitemlogopath="{if isset($aAuction.logo_path)}{img server_id=$aAuction.server_id path='core.url_pic' file=$aAuction.logo_path suffix='_400' return_url=true}{else}
                                    {img server_id=$aAuction.server_id path='' file=$aAuction.default_logo_path suffix='' return_url=true}{/if}"
                            onclick="ynauction.clickCompareCheckbox(this);" 
                            class="ynauction-compare-checkbox"> {phrase var='add_to_compare'}                       
                    </div>
                </li>
            {/if}
            
            {if ($aAuction.product_status != 'draft')}
                <li>
                    <a href="javascript:void(0)" onclick="tb_show('{phrase var='share'}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=feed&amp;url={$aAuction.linkAuction}&amp;title={$aAuction.titleAuction}&amp;feed_id={$aAuction.product_id}&amp;is_feed_view=1&amp;sharemodule=auction')); return false;"><i class="fa fa-share"></i> {phrase var='share'}</a>
                </li>
            {/if}

            {if ($aAuction.product_status != 'draft' && Phpfox::isUser() && $aAuction.user_id != Phpfox::getUserId())}
                {if $aAuction.bIsInWatchList}
                    <li><a href="javascript:void(0)" onclick="$.ajaxCall('auction.removeFromWatchList', 'item_id={$aAuction.product_id}'); return false;"><i class="fa fa-arrow-right"></i> {phrase var='remove_from_watchlist'}</a></li>
                {else}
                    <li><a href="javascript:void(0)" onclick="$.ajaxCall('auction.addToWatchList', 'item_id={$aAuction.product_id}'); return false;"><i class="fa fa-arrow-right"></i> {phrase var='add_to_watchlist'}</a></li>
                {/if}
            {/if}

            {if ($aAuction.product_status != 'draft') && ($aAuction.user_id != Phpfox::getUserId())}
                <li><a href="javascript:void(0)" onclick="$Core.composeMessage({l}user_id: {$aAuction.user_id}{r}); return false;"><i class="fa fa-envelope"></i> {phrase var='message_owner'}</a></li>
            {/if}
            <li><a onclick="window.open('{permalink module='auction.print' id=$aAuction.product_id title=$aAuction.name}','_blank');return false;" href="#"><i class="fa fa-print"></i> {phrase var='print'}</a></li>
        </ul>
    </div>
</div>
