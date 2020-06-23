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
<form method="post" accept-charset="utf-8"  action="{url link='admincp.feedback.statusfeedback'}" >
 <div class="table_header">
   {_p var='search_feedbacks'}
</div>
<div class="table form-group">
        <div class="table_left">
            {_p var='keyword'}
        </div>
    <div class="table_right">
        {$aFilters.keyword}
    </div>
    <div class="clear"></div>
</div>
<div class="table form-group">
        <div class="table_left">
            {_p var='category'}
        </div>
    <div class="table_right">
          {$aFilters.type_cats}
    </div>
    <div class="clear"></div>
</div>
<div class="table form-group">
        <div class="table_left">
            {_p var='status'}
        </div>
    <div class="table_right">
         {$aFilters.type_status}
    </div>
    <div class="clear"></div>
</div>
 <div class="table form-group">
        <div class="table_left">
            {_p var='browse_by'}
        </div>
    <div class="table_right">
         {$aFilters.sort}
    </div>
    <div class="clear"></div>
</div>
 <div class="table_clear">
    <input type="submit" name="search[submit]" value="{_p var='search'}" class="button" />
</div>
</form>
<div style="clear: both;"  ></div>

<div class="table_header">
   Status Feedback Management
</div>
{if count($aFeedBacks)>0}
<form action="{url link='current'}" method="post" id="order_display_sb" >
    <table align="center" style="text-align:left;">
    <tr>
        <th>{_p var='name'</th>
        <th>{_p var='status'}</th>
        <th style="width:300px;">{_p var='status_description'}</th>
        <th>{phrase var='feedback_total_comments'}</th>
        <th>{phrase var='feedback_total_comments'}</th>
        <th>{_p var='posted_time'}</th>
        <th>{_p var='posted_user'}</th>
        <th>{_p var='options'}</th>
    </tr>
    {foreach from=$aFeedBacks key=iKey item=aFeedBack}
    <tr  class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
        <td><a target="_blank" href="{url link='feedback.detail'}{$aFeedBack.title_url}">{$aFeedBack.title|shorten:40:'...'}</a></td>
        <td style="color:#{$aFeedBack.color}">{$aFeedBack.status}</td>
        <td>{$aFeedBack.feedback_status|shorten:50:'...'}</td>
        <td>{$aFeedBack.total_vote}</td>
         <td>{$aFeedBack.total_comment}</td>
        <td>{$aFeedBack.time_stamp|date:'feed.feed_display_time_stamp'}</td>
        <td>{if !empty($aFeedBack.full_name)}{$aFeedBack.full_name}{else}{$aFeedBack.visitor}{/if}</td>
       <td>
        <a id="edit_{$aFeedBack.feedback_id}" href="#?call=feedback.updateStatus&amp;height=300&amp;width=400&amp;feedback_id={$aFeedBack.feedback_id}" class="inlinePopup" title="Update Status">update status</a>
       </td>
    </tr>
    {/foreach}
    </table>
  </form>
{else}
{_p var='no_status_found'}
{/if}
{pager}
