<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_NewsFeed
 * @version        3.02p5
 *
 */
?>
<!-- News Item Search Form Layout -->
<form method="post" action="{url link='admincp.foxfeedspro.approvalitems'}">
	<!-- Form Header -->

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='foxfeedspro.search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <!-- RSS Provider Name Element -->
            <div class="form-group">
                <label for="">{phrase var='foxfeedspro.keywords'}:</label>
                <input type="text" class="form-control" name="search[item_title]" value = "{if isset($aForm.item_title)}{$aForm.item_title}{/if}"/>
            </div>
            <!-- RSS Provider Status Element -->
            <div class="form-group">
                <label for="">{phrase var='foxfeedspro.rss_provider_name'}</label>
                <select name="search[feed_id]" class="form-control">
                    <option value = ''>{phrase var="foxfeedspro.all"}</option>
                    {foreach from=$aFeedList item=aOption}
                    <option value = "{$aOption.feed_id}" {if isset($aForm.feed_id) and $aForm.feed_id == $aOption.feed_id} selected {/if}>{$aOption.feed_name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-primary" />
        </div>
    </div>
</form>

<!-- News Item Management Space -->
{if count($aNewsItems) > 0}
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
                                <th class="t_center w80">{phrase var='foxfeedspro.headline_title'}</th>
                                <th class="t_center w80">{phrase var='foxfeedspro.rss_provider_name'}</th>
                                <th class="t_center w100">{phrase var='foxfeedspro.headline_posted_date'}</th>
                                <th class="t_center w40">{phrase var='foxfeedspro.actions'}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Table Rows -->
                            {foreach from=$aNewsItems key=iKey item=aNews}
                            <tr id="foxfeedspro_item_{$aNews.item_id}" class="news_row {if $iKey%2 == 0 } feed_row_even_background{else} feed_row_odd_background{/if}">
                                <!-- Check box element -->
                                <td class="t_center">
                                    <input type = "checkbox" class="foxfeedspro_row_checkbox" id="news_{$aNews.item_id}" name="news_row[]" value="{$aNews.item_id}" position = "{$iKey}" onclick="foxfeedspro.checkDisableStatus();"/>
                                </td>
                                <!-- News title -->
                                <td>
                                    <a href="{url link='foxfeedspro.details.item_'.$aNews.item_id}" title="{$aNews.item_title}">{$aNews.item_title|shorten:35:'...'}</a>
                                </td>
                                <!-- RSS provider name -->
                                <td class="t_center">
                                    {$aNews.feed_name|shorten:35:'...'}
                                </td>
                                <!-- Posted date -->
                                <td class="t_center">
                                    {if $aNews.item_pubDate_parse}
                                        {$aNews.item_pubDate_parse}
                                    {else}
                                        <?php echo date("D, d M Y h:i:s e", $this->_aVars['aNews']['added_time']);?>
                                    {/if}
                                </td>
                                <!-- Status -->
                                <td class="t_center">
                                    <div id ="item_update_approval_{$aNews.item_id}">
                                        <a href="javascript:void(0);" onclick="foxfeedspro.updateApprovalNews({$aNews.item_id},'1')">
                                            {phrase var="foxfeedspro.approve"}
                                        </a>
                                        &nbsp;&nbsp;&nbsp;
                                        <a href="javascript:void(0);" onclick="foxfeedspro.updateApprovalNews({$aNews.item_id},'2')">
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
                    <!-- paginator -->
                    {pager}
                </div>
            </div>

             <!-- Management Buttons -->
             <div class="panel-footer t_right">
                <input type="button" name="approve_selected" id="approve_selected" disabled value="{phrase var='foxfeedspro.approve_selected'}" class="btn btn-primary disabled" onclick="foxfeedspro.approveNewsBySelected(1);"/>
                {*<input type="button" name="decline_selected" id="decline_selected" disabled value="{phrase var='foxfeedspro.decline_selected'}" class="btn btn-primary disabled" onclick="foxfeedspro.approveNewsBySelected(2);"/>*}
             </div>
        </div>
	</form>
{else}
<div class="panel panel-default">
    <div class="panel-body">
	    <span class="extra_info">{phrase var="foxfeedspro.no_news_item_found"}</span>
    </div>
</div>
{/if}

