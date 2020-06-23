<?php
    /*
    * @copyright        [YouNet_COPYRIGHT]
    * @author           YouNet Company
    * @package          Module_FeedBack
    * @version          2.01
    *
    */
    defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
	<style type="text/css">
		.admin_feedback_description
		{
			padding-top:4px;
		}
		.extra_info
		{
			font-size:11px;
		}
	</style>
{/literal}
<form method="get" accept-charset="utf-8" action="{$sFormUrl}" id="form_admin_search">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='search_feedbacks'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='keyword'}</label>
                {$aFilters.keyword}
            </div>
            <div class="form-group">
                <label>
                    {_p var='category'}
                </label>
                {$aFilters.type_cats}
            </div>
            <div class="form-group">
                <label>
                    {_p var='status'}
                </label>
                {$aFilters.type_status}
            </div>
            <div class="form-group">
                <label>
                    {_p var='browse_by'}
                </label>
                {$aFilters.sort}
            </div>
        </div>
    <div class="panel-footer">
        <input type="submit" name="search[submit]" value="{_p var='search'}" class="btn btn-primary"/>
    </div>
    </div>
</form>
{if count($aFeedBacks) <=0}
    <div class="extra_info">
        {_p var='no_feedbacks_found'}
    </div>
{else}
    <form action="{url link='current'}" method="post" id="order_display_sb"
        onsubmit="return getsubmit();">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p var='feedback_management'}
                </div>
            </div>
            <div class="ynfeed_back table-responsive">
                <table align="center" class="table table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" value="" name="checkAll" id="js_check_box_all" class="main_checkbox" /></th>
                            <th class="w200">{_p var='feedback_information'}</th>
                            <th>{_p var='category'}</th>
                            <th>{_p var='severity'}</th>
                            <th>{_p var='status'}</th>
                            <th>{_p var='visibility'}</th>
                            <th>{_p var='posted_user'}</th>
                            <th>{_p var='featured'}</th>
                            <th>{_p var='votable'}</th>
                            <th class="w120">{_p var='options'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aFeedBacks key=iKey item=aFeedBack}
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td style="width: 10px"><input type="checkbox" id="js_id_row{$aFeedBack.feedback_id}" class="checkbox" value="{$aFeedBack.feedback_id}" name="is_selected" /></td>
                            <td>
                                <div style="font-weight:bold;"><a target="_blank" href="{url link='feedback.detail'}{$aFeedBack.title_url}">{$aFeedBack.title|shorten:30:'..'}</a></div>
                                <div class="item_content item_view_content admin_feedback_description">{$aFeedBack.feedback_description|shorten:80:'...'}</div>
                                <div class="extra_info">{$aFeedBack.time_stamp|date:'feed.feed_display_time_stamp'}</div>
                            </td>
                            <td>{if $aFeedBack.category != "" && $aFeedBack.category != null}{$aFeedBack.category}{else}{phrase var ='feedback.uncategorized'}{/if}</td>
                            <td>
                                <a id="edit_{$aFeedBack.feedback_id}"  class="inlinePopup"  style="border-radius: 2px 2px 2px 2px;color: #FFFFFF !important;padding: 2px 3px;background-color: #{if !empty($aFeedBack.serverity)}{$aFeedBack.feedback_serverity_color}{/if};"><span>{if !empty($aFeedBack.serverity)}{$aFeedBack.serverity}{else}{/if}</span></a>

                            </td>
                            <td>
                                 <a id="edit_{$aFeedBack.feedback_id}" href="#?call=feedback.updateStatus&amp;height=300&amp;width=400&amp;feedback_id={$aFeedBack.feedback_id}" class="inlinePopup yn_feed_back_inlinePopup" title="{_p var='update_status'}" style="border-radius: 2px 2px 2px 2px;color: #FFFFFF !important;padding: 2px 3px;background-color: #{if $aFeedBack.status != "" && $aFeedBack.status != null}{$aFeedBack.color}{else}195B85{/if};"><span>{if $aFeedBack.status != "" && $aFeedBack.status != null}{$aFeedBack.status}{else}{phrase var ='feedback.unstatus'}{/if}</span></a>
                            </td>
                            <td>{if $aFeedBack.privacy == 1}{_p var='public'} {else}
                                { if $aFeedBack.privacy == 2}{phrase
                                var='feedback.private'}{else}{_p var='pending'}{/if}{/if}</td>
                            {*<td>{$aFeedBack.time_stamp|date:'feed.feed_display_time_stamp'}</td>*}
                            <td>{if !empty($aFeedBack.full_name)}{$aFeedBack.full_name}{else}{$aFeedBack.visitor}{/if}</td>
                            <td>
                                <div id="item_update_featured_{$aFeedBack.feedback_id}">
                                    <a href="javascript:updatefeatured({$aFeedBack.feedback_id},{if $aFeedBack.is_featured}1{else}0{/if});" title="{if $aFeedBack.is_featured}{_p var='click_to_clear_featured'}{else}{_p var='click_to_set_as_featured'}{/if}">{if
                                        $aFeedBack.is_featured eq 1}{_p var='yes'}{else}{_p var='no'}{/if}</a></div>
                            </td>
                            <td>
                                <div id="item_update_votable_{$aFeedBack.feedback_id}">
                                    <a href="javascript:updatevotable({$aFeedBack.feedback_id},{if $aFeedBack.votable}1{else}0{/if});" title="{if $aFeedBack.votable}{_p var='click_to_clear_votable'}{else}{_p var='click_to_set_as_votable'}{/if}">{if
                                        $aFeedBack.votable eq 1}{_p var='enabled'}{else}{_p var='disabled'}{/if}</a></div>
                            </td>
                            <td class="last_td"><a id="edit_{$aFeedBack.feedback_id}"
                                    href="#?call=feedback.callEditFeedBack&amp;height=400&amp;width=500&amp;feedback_id={$aFeedBack.feedback_id}"
                                    class="inlinePopup" title="{_p var='edit_the_feedback'}">{phrase
                                    var='feedback.edit'}</a> | <a id="delete_{$aFeedBack.feedback_id}" href="javascript:void(0);"
                                    onclick="deleteAdminFeedBack({$aFeedBack.feedback_id}); return false;"
                                    title="{_p var='delete_the_feedback'}">{_p var='delete'}</a></td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <input type="hidden" value="" name="arr_selected" id="arr_selected" />
                <input type="hidden" value="" name="feed_selected" id="feed_selected" />
                <input type="submit" name="deleteselect" value="{_p var='delete_feedback'}" class="btn btn-danger sJsCheckBoxButton sJsConfirm disabled" onclick="setValue();/*getFeed();*/" />
            </div>
        </div>
    </form>
    {pager}
{/if} 
