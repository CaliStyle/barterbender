<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<form method="post" action="{url link='admincp.auction'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='auction_name'}:</label>
                {$aFilters.search}
            </div>
            <div class="form-group">
                <label for="">{phrase var='seller'}:</label>
                {$aFilters.user}
            </div>
            <div class="form-group">
                <label for="">{phrase var='category'}:</label>
                {$aFilters.category_id}
            </div>
            <div class="form-group">
                <label for="">{phrase var='status'}:</label>
                {$aFilters.product_status}
            </div>
            <div class="form-group">
                <label for="">{phrase var="auction.featured"}:</label>
                {$aFilters.featured}
            </div>
            <div class="form-group">
                <label for="">{phrase var='display'}:</label>
                {$aFilters.display}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-primary" />
        </div>
    </div>
</form>

{pager}

{if count($aAuctions)}

<form method="post" action="{url link='admincp.auction'}" id="list_auctions_form">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="table-responsive flex-sortable">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="t_center w10"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                            <th>&nbsp;</th>
                            <th>{phrase var='auction'}</th>
                            <th>{phrase var='seller'}</th>
                            <th>{phrase var='category'}</th>
                            <th>{phrase var='reserve_price'}</th>
                            <th>{phrase var='start_date'}</th>
                            <th>{phrase var='end_date'}</th>
                            <th>{phrase var='status'}</th>
                            <th>{phrase var='featured'}</th>

                            <th>{phrase var='current_bidder'}</th>
                            <th>{phrase var='winner'}</th>
                            <th>{phrase var='current_bid'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aAuctions key=iKey item=aAuction}
                            {php}
                                    $aAuction = $this->_aVars['aAuction'];
                            {/php}
                            <tr id="js_row{$aAuction.product_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                                <td><input type="checkbox" name="id[]" class="checkbox" value="{$aAuction.product_id}" id="js_id_row{$aAuction.product_id}" /></td>
                                <td class="t_center">
                                    <a href="javascript:;" class="js_drop_down_link" title="Options"></a>
                                    <div class="link_menu">
                                        <ul>
                                            <li><a href="{url link='auction.edit' id=$aAuction.product_id}" >{phrase var='edit'}</a></li>
                                            {if in_array($aAuction.product_status, array('pending'))}
                                                <li><a href="{url link='admincp.auction' approveid=$aAuction.product_id}">{phrase var='approve'}</a></li>
                                            {/if}
                                            {if !in_array($aAuction.product_status, array('denied', 'completed'))}
                                                <li><a href="{url link='admincp.auction' denyid=$aAuction.product_id}">{phrase var='deny'}</a></li>
                                            {/if}
                                                <li><a href="javascript:;" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_delete_this_auction"}'{r}, function(){l}$.ajaxCall('auction.deleteAuction', 'iProductId={$aAuction.product_id}');{r}, function(){l}{r}); return false;">{phrase var='delete'}</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}">
                                        {$aAuction.name|clean|shorten:75:'...'|split:75}
                                    </a>
                                </td>
                                <td>{$aAuction|user}</td>
                                <td><?php echo Phpfox::isPhrase($aAuction['category_title']) ? _p($aAuction['category_title']) : Phpfox::getLib('locale')->convert($aAuction['category_title']);?></td>
                                <td>{$aAuction.sSymbolCurrency}{$aAuction.auction_item_reserve_price|number_format:2}</td>
                                <td>{$aAuction.start_time|date:'core.global_update_time'}</td>
                                <td>{$aAuction.end_time|date:'core.global_update_time'}</td>
                                <td>{phrase var=''$aAuction.product_status}</td>
                                <td class="t_center">
                                    <div class="js_item_is_active"{if !$aAuction.featured} style="display:none;"{/if}>
                                        <a href="#?call=auction.updateFeatureAction&amp;id={$aAuction.product_id}&amp;active=0" class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                                    </div>
                                    <div class="js_item_is_not_active"{if $aAuction.featured} style="display:none;"{/if}>
                                        <a href="#?call=auction.updateFeatureAction&amp;id={$aAuction.product_id}&amp;active=1" class="js_item_active_link" title="{phrase var='activate'}"></a>
                                    </div>
                                </td>
                                <td><a href="{url link=$aAuction.latest_bidder_user_name}">{$aAuction.latest_bidder_full_name|convert|shorten:25:'...'}</a>&nbsp;</td>
                                <td><a href="{url link=$aAuction.won_bid_user_name}">{$aAuction.won_bid_full_name|convert|shorten:25:'...'}</a>&nbsp;</td>
                                <td>{$aAuction.sSymbolCurrency}{$aAuction.auction_latest_bid_price|number_format:2}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </form>
        <div class="panel-footer t_right">
            <input type="button" name="delete" value="{phrase var='delete_selected'}" class="delete btn btn-primary sJsCheckBoxButton disabled" disabled="true" onclick="ynauction_admin.confirmDeleteAuctions('list_auctions_form');"/>
        </div>
    </div>
	{else}
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="p_4">
                    {phrase var='no_auctions_have_been_created'}
                </div>
            </div>
        </div>
	{/if}
</form>

{pager}