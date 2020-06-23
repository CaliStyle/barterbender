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
 ul.feedbacks_browse > li .feedbacks_browse_options {
        float:right;
        overflow:hidden;
        padding-left:20px;
    }
 ul.feedbacks_browse > li .feedbacks_browse_options > a {
    clear:both;
    display:block;
    font-size:13px;
    margin:5px;
    padding-bottom:2px;
    padding-top:2px;
}

a.buttonlink {
    background-position:0 0;
    background-repeat:no-repeat;
    display:inline-block;
    font-weight:700;
    padding-left:21px;
}

ul.feedbacks_browse li.other
{
    border-bottom:1px solid #ccc;
    margin:0 0 5px;
    padding:5px 5px 15px;
}

ul.feedbacks_browse li.lastest
{
    border: none;
}
 .featuredbg
    {
        background:#FEFBD9;
    }
      a.feedback_status:hover
    {
         text-decoration: none;
    }
    .view_more2
    {
        position: relative;
        margin-top: 30px;
    }
    ul.item_menu
    {
        position: absolute;
        right: 0;
        top:-20px;
    }
</style>
{/literal}
{if count($aMyFeedBacks) <= 0}
	{if $isSearch}<div class="error_message">{phrase var = 'feedback.no_feedbacks_found'}</div>
	{else}<div class="extra_info">{_p var='you_have_not_added_any_feedbacks'}</div>
	{/if}
{else}
<ul class="feedbacks_browse">
{foreach from=$aMyFeedBacks key=iKey item=aFeedBack name=fbl}
    <li class="{if $phpfox.iteration.fbl eq count($aMyFeedBacks)}lastest{else}other{/if} {if $aFeedBack.is_featured == 1} featuredbg{/if}">
    <div class="feedbacks_browse_votes">
            <div class="feedback_votes">
                    <p id="feedback_voting_{$aFeedBack.feedback_id}">
                        <span>{$aFeedBack.total_vote}</span>
                    </p>
                  <span id="feedback_voting_title_{$aFeedBack.feedback_id}"> {if $aFeedBack.total_vote == 1} {phrase var='feedback_vote'} {else} {phrase var='feedback_votes_n'} {/if} </span>
            </div>
            <div class="vote_button">
                {if $aFeedBack.isVoted}                 
                   <div id="feedback_vote_{$aFeedBack.feedback_id}">
                      <a href="javascript:updatevote({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'down');">{_p var='remove_feedback'}</a>
               </div>
                {else}
                <div id="feedback_vote_{$aFeedBack.feedback_id}">
                      <a href="javascript:updatevote({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'up');">{_p var='vote_feedback'}</a>
               </div>
                {/if}
                

            </div>
    </div>
    <div class="feedbacks_browse_info">
    <p class="feedbacks_browse_info_title">
            <a href="{$aFeedBack.bookmark_url}">{$aFeedBack.title}</a>
            {if $aFeedBack.is_featured == 1}
                <span class="featured">
                    <img src="{$core_path}/module/feedback/static/image/feedback_goldmedal1.gif" alt="" title="Feedback is featured" />
                </span>
            {/if}
    </p>
    <p class="feedbacks_browse_info_date">
            {$aFeedBack.total_view}{if $aFeedBack.total_view == 1} {_p var='view'}{else}{_p var='views'} {/if}, {$aFeedBack.total_comment} {if $aFeedBack.total_comment == 1} {_p var='comment'}{else} {_p var='comments'}{/if}, {$aFeedBack.total_attachment} {if $aFeedBack.total_attachment == 1} {_p var='picture'}{else}{_p var='pictures_n'}{/if}, {$aFeedBack.info}
    </p>
    {if !empty($aFeedBack.category_name)}
     <p>{_p var='category'}: {$aFeedBack.category_url}</p>
     {/if}
    <p class="feedbacks_browse_info_blurb">{$aFeedBack.feedback_description|shorten:150:'...'}</p>
     {if $aFeedBack.status != '' && $aFeedBack.status != null }
    <div class="feedback_status_box">
        <b>{_p var='status'}:</b> <a class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.color}">{$aFeedBack.status}</a>
        <p class="feedbacks_browse_info_blurb">{$aFeedBack.feedback_status} </p>
    </div>
    {/if}
  </div>
    <div style="margin-top: 10px;display:block;width:100%;clear:both"></div>
     <div class="view_more2">
      <div class="t_right">
           <ul class="item_menu">
             <li><a class="buttonlink icon_feedback_viewall" href="{url link='feedback.detail'}{$aFeedBack.title_url}">View Feedback</a></li>
			<li><a  class="buttonlink inlinePopup" href="#?call=feedback.editFeedBack&amp;height=400&amp;width=500&amp;feedback_id={$aFeedBack.feedback_id}" title="Edit a Feedback">{_p var='edit_feedback'}</a></li>
			<li><a  class="buttonlink icon_feedback_image_new" href="{url link='feedback.up' feedback =$aFeedBack.feedback_id}">{_p var='add_picture'}</a></li>
			<li><a  class="buttonlink icon_feedback_delete" href="#" onclick="deleteFeedBack({$aFeedBack.feedback_id}); return false;">{_p var='delete_feedback'}</a></li>
            </ul>
        </div>
     </div>
 </li>
{/foreach}
</ul>
{/if}
{pager}
