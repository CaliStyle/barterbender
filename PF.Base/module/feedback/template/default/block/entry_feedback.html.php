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
   .feedback_entry_popup
   {
       padding-left:15px;
       padding-right:10px;
   }
   .title_feedback_entry
   {
       font-size: 13px;
        font-weight: bold;
        padding: 3px 0 10px;
   }
   .entry_feedback_status_box {
    background-color:#F4F9FB;
    border:5px solid #DDECF3;
    clear:both;
    margin-top:10px;
    padding:12px;
    margin-left: 0px; 
	margin-right: 10px;
}
	a.feedback_status
	{
		color: #FFFFFF !important;
    	font-weight: 700;
    	padding: 2px 3px;
	}
	.feedbacks_browse_info_blurb
	{
		margin-right:10px;
	}
	#post_your_feedback .button
	{
		margin-left: 120px; 
		margin-bottom:15px;
	}
	.servertity_entry_popup
	{
		margin-bottom:6px;
	}
</style>
{/literal}
<div class="item_view feedback_entry_popup"> 	
	<div class="title_feedback_entry"><a href="{url link='feedback.detail'}{$aFeedBack.title_url}">{$aFeedBack.title}</a></div>
	<div class="item_info">
        {$aFeedBack.total_view} {if $aFeedBack.total_view == 1}{_p var='view'}{else}{_p var='views_n'}{/if},&nbsp;
        {$aFeedBack.total_comment} {if $aFeedBack.total_comment == 1}{_p var='comment'}{else}{_p var='comments_n'}{/if},&nbsp;
        {$aFeedBack.total_attachment} {if $aFeedBack.total_attachment == 1}{_p var='picture'}{else}{_p var='pictures_n'}{/if}<br/>
        {$aFeedBack.info}
    	{if !empty($aFeedBack.category_name)}
        	<br/>{_p var='category'}: {$aFeedBack.category_url}
     	{/if}
     </div>
   	<div class="item_content item_view_content">{$aFeedBack.feedback_description'}</div>
   	{if (!empty($aFeedBack.feedback_servertity_name) && Phpfox::isAdmin()) || (!empty($aFeedBack.status))} 
   		<div class="entry_feedback_status_box">
   		{if !empty($aFeedBack.feedback_servertity_name) && phpfox::isAdmin()}
   			<div class="servertity_entry_popup">  		
        		<b>{_p var='serverity'}:</b> <span class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.feedback_serverity_color}; color:#FFFFFF; font-weight:700; padding:2px 3px; border-radius:2px;">{$aFeedBack.feedback_servertity_name}</span>
        	</div>
    	{/if}
    	{if !empty($aFeedBack.feedback_status)}
    		<div class="status_entry_popup">
        		<b>{_p var='status'}:</b> <span class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.color}; color:#FFFFFF; font-weight:700; border-radius:2px; padding:2px 3px;{if phpfox::isAdmin()}{/if}">{$aFeedBack.status}</span>
        		<p class="feedbacks_browse_info_blurb" style="margin-top:5px;">{$aFeedBack.feedback_status} </p>
        	</div>
    	{/if}
    	</div>
    {/if}
	<div style="clear:both"></div>
	
	{module name='feed.comment' aFeed=$aFeedBack.aFeed}
	
    <div id="post_your_feedback" class="feedback-forum">
    	<button class="btn btn-primary" onclick="javascript:showFormPostFeedBack();return false;">{_p var='post_feedback'}</button>
    </div>
</div>

