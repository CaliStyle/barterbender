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
<script type="text/javascript">
var core_url = '{$core_path}';
</script>
<div class="ynf_item_view clearfix">
    {if $aFeedBack.is_approved == 0}
    {literal}
    <script type="text/javascript">
        $Behavior.sub = function() {
            $("#left > .sub_section_menu > ul li").each(function (i, el) {
                $(el).removeClass("active");
                if (i == 3) {
                    $(el).addClass("active");
                }
            });
        }
    </script>
    {/literal}
    {/if}
	{if $aFeedBack.is_approved != 1 && $bCanApprove}
        {template file='core.block.pending-item-action'}
    {elseif $aFeedBack.is_approved != 1}
        <div class="message js_moderation_off" id="js_approve_message" style="margin-bottom: 10px;">
            {_p var='feedback_this_feedback_is_pending_an_admins_approval'}
        </div>
    {else}
    {/if}
    <div class="feedback_content" style="position: relative">
        <div class="ynf_feeback_item_img">
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
                    <button class="btn btn-success btn-small vote_button_feedback" onclick="updatevote({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'down');">{_p var='remove_feedback'}</button>
                </div>
                {else}
                <div id="feedback_vote_{$aFeedBack.feedback_id}">
                    <button class="btn btn-success btn-small vote_button_feedback" onclick="updatevote({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'up');">{_p var='vote_feedback'}</button>
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
        </div>

        <div class="detail_feedback_title_info_popup">
            <div class="ynf_title">
               
                {$aFeedBack.title}
            </div>

            <div class="extra_info">
                <div>
                    <b>{$aFeedBack.total_view}</b> {if $aFeedBack.total_view == 1}{_p var='view'}{else}{_p var='views_n'}{/if}&nbsp;.
                    <b>{$aFeedBack.total_comment}</b> {if $aFeedBack.total_comment == 1}{_p var='comment'}{else}{_p var='comments_n'}{/if}&nbsp;.
                    <b>{$aFeedBack.total_attachment}</b> {if $aFeedBack.total_attachment == 1}{_p var='picture'}{else}{_p var='pictures_n'}{/if}
                </div>

                <div>
                    {$aFeedBack.info}
                </div>
                
                <div>
                    {if !empty($aFeedBack.category_name)}
                        {_p var='category'}: {$aFeedBack.category_url}
                    {/if}
                </div>

                <div class="ynf_item_view_content">{$aFeedBack.feedback_description|parse}</div>
                {if isset($aFeedBack.tag_list)}
                        {module name='tag.item' sType='feedback' sTags=$aFeedBack.tag_list iItemId=$aFeedBack.feedback_id iUserId=$aFeedBack.user_id sMicroKeywords='keywords'}
                    {/if}
            </div>
        </div>
              

        {if $aFeedBack.canDoAction}
        <div class="item_bar">
            <div class="item_bar_action_holder">
                <a role="button" data-toggle="dropdown" class="item_bar_action" href="#"><span>{_p var='action'}</span><i class="ico ico-gear-o"></i></a>

                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='feedback.block.link'}
                </ul>
            </div>
        </div>
        {/if}
        
        
        <div class="feedback_image_detail">
            {if (!empty($aFeedBack.feedback_servertity_name) && Phpfox::isAdmin()) || (!empty($aFeedBack.status))}
            <div class="feedback_status_box" >
                {if $aFeedBack.feedback_servertity_name != '' && Phpfox::isAdmin()}
                <div class="feedback_servertity_entry">
                    <span>{_p var='serverity'}:</span>
                    <span class="feedback_servertity_{$aFeedBack.status} feedback_servertity" style="background-color:#{$aFeedBack.feedback_serverity_color};">{$aFeedBack.feedback_servertity_name}</span></div>
                {/if}
                {if !empty($aFeedBack.status)}
                <div class="feedback_status_entry">
                    <span>{_p var='status'}:</span>
                    <span>
                        <a class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.color};{if phpfox::isAdmin()}{/if}">{$aFeedBack.status}
                        </a>
                    </span>
                    <div class="ynf_item_view_content" style="margin-top:10px">
            			{$aFeedBack.feedback_status|parse|split:55|shorten:100:'feedback.see_more':true}
         			</div>
                </div>
                {/if}

            </div>
            {/if}

            <div class="feedback_images" id="feedback_images">
                {if isset($aFeedBackPics)}
                <div class="ynf_small_thumb clearfix">
                    {foreach from=$aFeedBackPics  item=aPic name=aPic}
                    <div class="feedback_img feedback-item-img">
                        <a href="javascript:void(0);"><img id="img_thumb{$aPic.picture_id}"  onclick="tb_show({_p var='feedback_photo'}, $.ajaxBox('feedback.getPictureFeedbackBlock','height={$aPic.height}&amp;width={$aPic.width}&amp;link='+this.getAttribute('rel'))); adjustPopup({$aPic.height}); return false;" rel="{$aPic.picture_id}"  src="{$core_path}file/pic/feedback/{$aPic.thumb_url}" alt=""  /></a>
                        {if $aFeedBack.user_id == Phpfox::getUserId() || Phpfox::IsAdmin()} <span onclick="deletePic({$aPic.picture_id},{$aFeedBack.feedback_id});return false;" style="display:block;text-align: center;cursor: pointer;color: #0D8AE1;font-size: 13px;">{_p var='delete'}</span> {/if}
                    </div>
                    {/foreach}
                </div>
                {/if}
            </div>
        </div>
        {addthis url=$sBookmarkUrl title=$aFeedBack.title}
        <div class="js_moderation_on">
            <div class="item-detail-feedcomment">
                {module name='feed.comment'}
            </div>
        </div>
    </div>
</div>

{literal}
<script type="text/javascript">
    $Behavior.fixNavigationFeedbackImage = function(){
        $(function() {
        //$('#feedback_images a').lightBox({fixedNavigation:true});
        });
    }
</script>
{/literal}