<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */
?>
<!-- Feed Provider Search Form Layout -->
<form method="post" action="{url link='admincp.foxfeedspro.approvalfeeds'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='foxfeedspro.search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <!-- RSS Provider Name Element -->
            <div class="form-group">
                <label for="">{phrase var='foxfeedspro.rss_provider_name'}:</label>
                <input type="text" class="form-control" name="search[feed_name]" value = "{if isset($aForm.feed_name)}{$aForm.feed_name}{/if}"/>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-priamry" />
        </div>
    </div>
</form>

<!-- Rss Provider Management Space -->
{if count($aFeeds) > 0}
<form action="{$sCurrentUrl}" method="post" id="order_display_sb">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <div class="table-responsive flex-sortable">
                    <table table class="table table-bordered">
                        <!-- Table Header -->
                        <thead>
                        <tr>
                            <th class="t_center w20"><input type="checkbox" onclick="foxfeedspro.checkAll();" id="feed_list_check_all" name="feed_list_check_all"/></th>
                            <th class="t_center w30">{phrase var='foxfeedspro.headline_logo'}</th>
                            <th class="t_center w100">{phrase var='foxfeedspro.rss_provider_name'}</th>
                            <th class="t_center w40">{phrase var='foxfeedspro.headline_feed_url'}</th>
                            <th class="t_center w80">{phrase var='foxfeedspro.category'}</th>
                            <th class="t_center w80">{phrase var='foxfeedspro.headline_last_update'}</th>
                            <th class="t_center w60">{phrase var='foxfeedspro.actions'}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Table Rows -->
                        {foreach from=$aFeeds key=iKey item=aFeed}
                        <tr id="foxfeedspro_item_{$aFeed.feed_id}" class="feed_row {if $iKey%2 == 0 } feed_row_even_background{else} feed_row_odd_background{/if}">
                            <!-- Check box element -->
                            <td class="t_center">
                                <input type = "checkbox" class="foxfeedspro_row_checkbox" id="feed_{$aFeed.feed_id}" name="feed_row[]" value="{$aFeed.feed_id}" position = "{$iKey}" approved ="{$aFeed.is_approved}" status="{$aFeed.is_active}" onclick="foxfeedspro.checkDisableStatus();"/>
                            </td>
                            <!-- Feed logo -->
                            <td class="t_center">
                                {if $aFeed.feed_logo}
                                    {img server_id = 0 path='core.url_pic' file='foxfeedspro/'.$aFeed.feed_logo suffix='' max_width='60' max_height='30'}
                                {else}
                                    {*phrase var="foxfeedspro.no_logo"*}
                                {/if}
                            </td>
                            <!-- RSS Provider Name -->
                            <td>
                                <a href="javascript:void(0);" title="{$aFeed.feed_name}">{$aFeed.feed_name|shorten:35:'...'}</a>
                            </td>
                            <!-- RSS Provider Link -->
                            <td  class="t_center">
                                <a href="{$aFeed.feed_url}" target="_blank" title="{$aFeed.feed_url}">{phrase var='foxfeedspro.rss_link'}</a>
                            </td>
                            <!-- RSS Provider Category -->
                            <td class="t_center">{$aFeed.category_name|shorten:30:'...'}</td>
                            <!-- RSS Provider Last Update -->
                            <td class="t_center"><?php echo date('d F, Y',$this->_aVars["aFeed"]["time_update"]); ?></td>
                            <!-- Status -->
                            <td class="t_center">
                                <div id ="item_update_approval_{$aFeed.feed_id}">
                                    <a href="javascript:void(0);" onclick="foxfeedspro.updateApprovalFeeds({$aFeed.feed_id},'1')">
                                        {phrase var="foxfeedspro.approve"}
                                    </a>
                                    &nbsp;&nbsp;&nbsp;
                                    <a href="javascript:void(0);" onclick="foxfeedspro.updateApprovalFeeds({$aFeed.feed_id},'2')">
                                        {phrase var="foxfeedspro.decline"}
                                    </a>

                                </div>
                            </td>
                        </tr>
                        {/foreach}
                        </tbody>
                     </table>
                </div>
            </div>
            <div class="form-group">
                <!-- Paginator -->
                {pager}
            </div>
        </div>
		 <!-- Management buttons -->
		<div class="panel-footer t_right">
			<input type="button" name="approve_selected" id="approve_selected" disabled value="{phrase var='foxfeedspro.approve_selected'}" class="btn btn-primary disabled" onclick="foxfeedspro.approveFeedBySelected(1);"/>
	     	{*<input type="button" name="decline_selected" id="decline_selected" disabled value="{phrase var='foxfeedspro.decline_selected'}" class="btn btn-primary disabled" onclick="foxfeedspro.approveFeedBySelected(2);"/>*}
	    </div>
</form>
{else}
<div class="panel panel-default">
    <div class="panel-body">
	    <span class="extra_info">{phrase var="foxfeedspro.no_rss_provider_found"}</span>
    </div>
</div>
{/if}
