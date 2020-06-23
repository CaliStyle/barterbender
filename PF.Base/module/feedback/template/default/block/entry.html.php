<?php
/*
* @copyright        [YouNet_COPYRIGHT]
* @author           YouNet Company
* @package          Module_FeedBack
* @version          3.01
*
*/
defined('PHPFOX') or exit('NO DICE!');
?>

{*{if $bWidth}
{literal}
<script type="text/javascript">
$(function(){
$('.content3').css('width', '100%');
});
</script>
{/literal}
{/if}*}

<div id="js_feedback_entry{$aFeedBack.feedback_id}" class="moderation_row js_feedback_parent">
   <div class="ynf_feeback_item clearfix">
       {if $aFeedBack.canDoAction}
         <div class="row_edit_bar_parent" style="display: block;">
            <div class="row_edit_bar">  
            <a href="#" class="row_edit_bar_action" data-toggle="dropdown"><i class="fa fa-action"></i></a>
               <ul class="dropdown-menu dropdown-menu-right">
                  {template file='feedback.block.link'}
               </ul>
            </div>
         </div>
         {/if}


      <div class="ynf_feeback_item_img table_row">

         <div class="feedback_votes">

            <p id="feedback_voting_{$aFeedBack.feedback_id}">
               <span>{$aFeedBack.total_vote}</span>
            </p>
            <span id="feedback_voting_title_{$aFeedBack.feedback_id}">
                {if $aFeedBack.total_vote == 1}
                    {_p var='feedback_vote'}
                {else}
                    {_p var='feedback_votes_n'}
                {/if}
            </span>
         </div>
         <div class="vote_button">
            {if $aFeedBack.votable && Phpfox::isUser()}
            {if $aFeedBack.isVoted}
            <div id="feedback_vote_{$aFeedBack.feedback_id}">
               <button class="btn btn-success vote_button_feedback" onclick="updatevote({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'down');">{_p var='remove_feedback'}</button>
            </div>
            {else}
            <div id="feedback_vote_{$aFeedBack.feedback_id}">
               <button class="btn btn-success vote_button_feedback" onclick="updatevote({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'up');">{_p var='vote_feedback'}</button>
            </div>
            {/if}
            {elseif Phpfox::isUser()}
            <div id="feedback_votable_{$aFeedBack.feedback_id}" style="margin-top: 2px;">
               <span class="votable" >{_p var='vote_closed'}</span>
            </div>
            {/if}
         </div>
        {if $aFeedBack.is_featured == 1}
            <div class="item-flag-icon">
              <div class="sticky-label-icon sticky-featured-icon">
                  <span class="flag-style-arrow"></span>
                  <i class="ico ico-diamond"></i>
              </div>
            </div>
            {/if}
         {if Phpfox::getUserParam('feedback.can_approve_feedbacks') || Phpfox::getUserParam('feedback.delete_user_feedback')}
          <div class="moderation_row" style="position: absolute;top: 0;">
              <label class="item-checkbox">
                  <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aFeedBack.feedback_id}" id="check{$aFeedBack.feedback_id}" />
                  <i class="ico ico-square-o"></i>
              </label>
          </div>
         {/if}
      </div>

      <div class="ynf_feeback_item_info">
         <div id="js_blog_edit_title{$aFeedBack.feedback_id}" class="ynf_title">
            <a id="js_blog_edit_inner_title{$aFeedBack.feedback_id}" class="link ajax_link" href="{$aFeedBack.bookmark_url}">
                
                {$aFeedBack.title}
            </a>
         </div>

         <div class="extra_info">
            <div>
               <b>{$aFeedBack.total_view}</b> {if $aFeedBack.total_view == 1}{_p var='view'}{else}{_p var='views_n'}{/if}&nbsp;.
               <b>{$aFeedBack.total_comment}</b> {if $aFeedBack.total_comment == 1}{_p var='comment'}{else}{_p var='comments_n'}{/if}&nbsp;.
               <b>{$aFeedBack.total_attachment}</b> {if $aFeedBack.total_attachment == 1}{_p var='picture'}{else}{_p var='pictures_n'}{/if}
            </div>
            
            <div>
                {phrase var="feedback.user_posted_feedback" username=''} {if $aFeedBack.user_id != 0}<span>{$aFeedBack|user}</span>{else}{$aFeedBack.visitor}{/if}
            </div>
           
           <div>
               {if $aFeedBack.category_name}
                  {_p var='category'}: {$aFeedBack.category_url}
               {/if}
           </div>
         </div>
         {if $aFeedBack.is_approved != 1}
         <div class="message js_moderation_off pending_feedback_entry" id="js_approve_message">
            {_p var='feedback_this_feedback_is_pending_an_admins_approval'}
         </div>
         {/if}
         <div id="js_blog_edit_text{$aFeedBack.feedback_id}">
            <div class="ynf_item_view_content">
               {$aFeedBack.feedback_description|parse|split:55|shorten:150:'feedback.see_more':true}
            </div>
         </div>

      </div>

      {if ($aFeedBack.feedback_servertity_name != '' && Phpfox::isAdmin()) || (!empty($aFeedBack.status))}
      <div class="feedback_status_box">
         {if $aFeedBack.feedback_servertity_name != '' && Phpfox::isAdmin()}
            <div class="feedback_servertity_entry">
            <span>{_p var='serverity'}:</span>
            <span class="feedback_servertity_{$aFeedBack.status} feedback_servertity" style="background-color:#{$aFeedBack.feedback_serverity_color};">{$aFeedBack.feedback_servertity_name}</span></div>
         {/if}
         {if !empty($aFeedBack.status) }
         <div class="feedback_status_entry">
            <span>{_p var='status'}:</span>
            <a class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.color}">{$aFeedBack.status}</a></div>
            <div class="ynf_item_view_content">
            	{$aFeedBack.feedback_status|parse|split:55|shorten:100:'feedback.see_more':true}
         	</div>
         {/if}
      </div>
      {/if}

      {*<div style="margin-left:80px;">
         {module name='feed.comment' aFeed=$aFeedBack.aFeed}
      </div>*}
      
   </div>
   
</div>

{literal}
<style type="text/css">
#my .fa
{
	display:none;
}
</style>
{/literal}
