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

<!-- Feed Provider Search Form Layout -->
<form method="get" action="{url link='admincp.foxfeedspro.feeds'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <!-- Form Header -->
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
            <div class="form-group">
                <label for="">
                    {phrase var='foxfeedspro.status'}
                </label>
                <select class="form-control" name="search[status]">
                    {foreach from=$aStatusOptions item=aOption}
                    <option value = "{$aOption.value}" {if isset($aForm.status) and $aForm.status == $aOption.value} selected {/if}>{$aOption.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="">
                    {phrase var='foxfeedspro.category_name'}:
                </label>
                <select class="form-control" name="search[category_id]">
                    <option value = ''>{phrase var="foxfeedspro.all"}</option>
                    {*foreach from = $aCategories item = aCat}
                    <option value ='{$aCat.category_id}' {if isset($aForm.category_id) and $aForm.category_id == $aCat.category_id}selected{/if}>{$aCat.name}</option>
                    {/foreach*}
                    {$sCategoryOptions}
                </select>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-primary" />
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
                                <th class="t_center w80">{phrase var='foxfeedspro.headline_logo'}</th>
                                <th class="t_center w80">{phrase var='foxfeedspro.rss_provider_name'}</th>
                                <th class="t_center w100">{phrase var='foxfeedspro.headline_feed_url'}</th>
                                <th class="t_center w80">{phrase var='foxfeedspro.category'}</th>
                                <th class="t_center w80">{phrase var='foxfeedspro.headline_last_update'}</th>
                                <th class="t_center w40">{phrase var='foxfeedspro.order'}</th>
                                <th class="t_center w100">{phrase var='foxfeedspro.headline_options'}</th>
                                <th class="t_center w40">{phrase var='foxfeedspro.headline_status'}</th>
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
                                        <img src="{$sFilePath}{$aFeed.feed_logo}" alt=""/>
                                    {else}
                                        {*phrase var="foxfeedspro.no_logo"*}
                                    {/if}
                                </td>
                                <!-- RSS Provider Name -->
                                <td>
                                    <a href="{url link='admincp.foxfeedspro.items.feed_'.$aFeed.feed_id}" title="{$aFeed.feed_name}">{$aFeed.feed_name|shorten:25:'...'}</a>
                                </td>
                                <!-- RSS Provider Link -->
                                <td class="t_center">
                                    <a href="{$aFeed.feed_url}" target="_blank" title="{$aFeed.feed_url}">{phrase var='foxfeedspro.rss_link'}</a>
                                </td>
                                <!-- RSS Provider Category -->
                                <td class="t_center">{$aFeed.category_name|shorten:20:'...'}</td>
                                <!-- RSS Provider Last Update -->
                                <td  class="t_center"><?php echo date('d F, Y',$this->_aVars["aFeed"]["time_update"]); ?></td>
                                <!-- RSS Provider Order -->
                                <td class="t_center">
                                    <input style="width:30px"; size="5" type="text"  name="feed_order[{$aFeed.feed_id}]" value="{$aFeed.order_display}"/>
                                </td>
                                <!-- Options -->
                                <td class="t_center">
                                    <a href="{url link='admincp.foxfeedspro.addfeed.feed_'.$aFeed.feed_id}" >{phrase var='foxfeedspro.edit'}</a>
                                    |
                                 {if $aFeed.is_approved == 1}
                                  <div id="feed_getdata_{$aFeed.feed_id}" style="margin-top:5px;">
                                    <a href="javascript:void(0);" onclick="foxfeedspro.getData({$aFeed.feed_id},{$bIsAdminPanel},'normal');">{phrase var='foxfeedspro.get_data'}</a>
                                  </div>
                                 {else}
                                    <span class="gray">{phrase var='foxfeedspro.get_data'}</span>
                                 {/if}
                                </td>
                                <!-- Status -->
                                <td class="t_center">
                                    {if $aFeed.is_approved == 1}
                                        <div id="feed_update_status_{$aFeed.feed_id}">
                                            <a href="javascript:void(0);" onclick="foxfeedspro.updateFeedStatus({$aFeed.feed_id},{$aFeed.is_active})" >
                                                {if $aFeed.is_active eq 1}
                                                    {phrase var='foxfeedspro.active'}
                                                {else}
                                                    {phrase var='foxfeedspro.inactive'}
                                                {/if}
                                            </a>
                                        </div>
                                    {elseif $aFeed.is_approved == 2}
                                        <span style="color:gray">{phrase var='foxfeedspro.declined'}</span>
                                    {else}
                                        <span style="color:gray">{phrase var='foxfeedspro.pending'}</span>
                                    {/if}
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
			<input type="button" name="get_data" id="get_data" disabled value="{phrase var='foxfeedspro.get_data'}" class="btn btn-primary disabled" onclick="foxfeedspro.getDataBySelected({$bIsAdminPanel});" />
			<input type="button" name="inactive_active" id ="inactive_active" disabled value="{phrase var='foxfeedspro.inactive_active'}" class="btn btn-primary disabled" onclick="foxfeedspro.updateStatusBySelected()" />
	        <input type="submit" name="delete_selected" id="delete_selected" disabled value="{phrase var='foxfeedspro.delete_selected'}" class="sJsConfirm btn btn-primary disabled" />
	        <input type="submit" name="save_display_order" id="save_display_order" value="{phrase var='foxfeedspro.save_display_order'}" class="btn btn-primary"/>
	    </div>
    </div>
</form>
{else}
<div class="panel panel-default">
    <div class="panel-body">
	    <span class="extra_info">{phrase var="foxfeedspro.no_rss_provider_found"}</span>
    </div>
</div>
{/if}
