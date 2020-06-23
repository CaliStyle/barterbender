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
    #formaddfeed
    
    .table_left_addfeedback
    {
        float:left;
    }
    #col-add
    {
        float:right;
        width:60%;
        background: #fff;
        border-left: 1px solid #ccc;
    }
    #col-add > form
    {
         margin-left: 60px;
         margin-top: 15px;
    }
    #show_feedback
	{
        float:right;
        width:60%;
        display:none;
        background: url({/literal}{$path}{literal}module/feedback/static/image/background.png);
        border-left: 1px solid #ccc;
	}
    #col-view {
        float:left;
        width:39%;
        min-height: 400px; 
        /*padding:15px 0 15px 0px;;*/
    }

    .lightbox_feedback_list {        
        clear:both;
        float:left;
        padding:0px 0;
        width:90%;
        background: transparent;
/*       border-bottom: 1px solid transparent;*/
    }
/*    .lightbox_feedback_list:hover
{
        border: 1px solid #d5cec4;
        background: #ede5da;
        -moz-border-radius:4px;
        padding:10px 0;
}*/
    .feedbacks_lightbox_votes {
        float:left;
        margin-right:8px;
        overflow:hidden;
        text-align:center;
        width:60px;
        margin-top:5px;
    }
    .feedbacks_lightbox_votes .feedback_votes {
        -moz-border-radius:5px 5px 5px 5px;
        border:1px solid #CCCCCC;
        font-size:11px;
        padding:7px 1px;
        text-align:center;
        text-transform:lowercase;
    }
    .feedbacks_lightbox_votes .feedback_votes p {
        display:block;
        font-size:15px;
    }

    .feedbacks_lightbox_info_title {
        float:left;
        width:215px;
        margin-top:5px;
    }
    .feedbacks_lightbox_info_title a
    {
       font-size:13px;
       font-weight: bold
    }
    div.feedbacks_lightbox_info {
        float:left;
        /*width:130px;*/
    }

    .feedbacks_lightbox_info_date {
        color:#B6B6B6;
        font-size:11px;
        padding: 5px 1px !important;
    }

    .lightbox_vote_button a, .lightbox_vote_button a:visited,.lightbox_vote_button span.feedback-novote {
        -moz-border-radius:3px 3px 3px 3px;
        background-color:#333;
        border-bottom-color:#888888;
        color:#fff !important;
        cursor:pointer;
        display:inline-block;
        font-size:10px;
        font-weight:bold;
        margin-top:10px;
        padding:5px 10px;
        float:right;

    }
    .top-feedback
    {
        float:left;
        margin-bottom:15px;
    }
    div.feedback-forum
    {
        clear: both;
        margin-top: 15px;
    }
    .feedback-forum .lightbox_more.button {
        text-align:center;
        width:60%;
        display: block;
        font-size: 12px;
        -moz-border-radius:4px 4px 4px 4px;   
        border:medium none;
        color:#FFFFFF;
        cursor:pointer;
        font-weight:bold;
        margin:0;
        overflow:visible;
        padding:4px;
        vertical-align:middle;
        text-decoration: none;
        margin-left:50px;
    }

    span.feedback_status_Started.feedback_status
    {
        color:#6FBC00;
    }

    span.feedback_status_Planned.feedback_status
    {
        color:#F0BA00;
    }

    span.feedback_status_Under.Review.feedback_status
    {
        color:#F53B60;
    }
    span.feedback_status_Completed.feedback_status
    {
        color:#3366ff;
    }
    span.feedback_status_Declined.feedback_status
    {
        color:#00cc33;
    }

    span.feedback_status_Response.feedback_status
    {
        color:#000000;
    }

    span.feedback_status
    {
        color: #195B85;
    }
span.featured {
        float: right;
        margin-top: 5px;
    }
    .feedbacks_lightbox_info_title .item-flag-icon{
        position: absolute;
        right: 5px;
        top: 0;
    }
    .lightbox_vote_button
    {
         margin-top: -10px;
    }

    span.feedback-novote.inactive
    {
        color: #ccc !important;
        font-weight: normal !important;
        background-color:#888888;
    }

    #formaddfeed h3
    {
        margin: 0 0 10px;
    }
    #TB_window {
        top:47% !important;
    }

    span.feedback-novote.inactive {
        background-color:#888888 !important;
    }
    .featuredbg
    {
        
    }
    .js_box_content
    {
        padding:0;
    }
    .table_right
    {
        margin-left: 22%;
        text-align: left;
    }
    .table_clear
    {
        margin-left: 22%;
    }
    .feedbacks_browse_info
    {
        padding:15px;
    }
    .feedbacks_browse_votes
    {
        height: 75px;
        width: 75px;
        border: 1px solid #ccc;
        margin-top:15px;
    }
    .menu_feedback form
    {
        float: right;
    }
    .header_feedback .menu
    {
        margin: 6px 0 6px 0px;
    }
    .header_feedback .menu a
    {
    	float:left;
        padding: 10px 15px;
        font-size:14px;
        color: #fff;
    }
    .header_feedback .menu a:hover,
    .header_feedback .menu a.active
    {
        background: #fff;
        color: #000;
        text-decoration: none;
    }
    .header_feedback ul li:hover
    {
        cursor: pointer;
    }
    .js_box div.row1,.js_box div.row2
    {
        display: block;
        overflow: hidden;
        padding-left:15px;
        padding-bottom:8px;
        padding-top:0px;
    }
    div.row1:hover, 
    div.row2:hover
    {
        background: #f2f2f2;
    }
     .button.vote_button
    {
    	border-radius:3px;
    	padding:1px;
    	font-size:10px;
    	margin-top:12px;
    }
</style>
{/literal}
<div class="feedback_popup_right">  
{if count($topVotedFeedBacks) > 0}
	{foreach from=$topVotedFeedBacks item=aFeedBack name=fbl}
		 <div id="js_feedback_entry{$aFeedBack.feedback_id}" class="js_blog_parent {if is_int($phpfox.iteration.fbl/2)}row1{else}row2{/if}{if $phpfox.iteration.fbl == 1 && !PHPFOX_IS_AJAX} row_first{/if}{if $aFeedBack.is_approved != 1} {/if}">
                <div class="feedbacks_lightbox_votes">
                    <div class="feedback_votes">
                        <p id="feedback_voting_popup_{$aFeedBack.feedback_id}">
                            {$aFeedBack.total_vote}
                        </p>
                        <span id="feedback_voting_popup_title_{$aFeedBack.feedback_id}">
                            {if $aFeedBack.total_vote == 1}
                                {_p var='feedback_vote'}
                            {else}
                                {_p var='feedback_votes_n'}
                            {/if}
                        </span>
                    </div>
					<div class="lightbox_vote_button" id="feedback_vote_{$aFeedBack.feedback_id}">
                    {if $aFeedBack.isVoted}
                    	<div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
                        	<input type="button" class="btn btn-success btn-xs"  onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'down',{$user_id});return false;" value="{_p var='remove_feedback'}" />
                    	</div>
                    {else}
                    <div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
                        <input type="button" class="btn btn-success btn-xs" onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'up',{$user_id});return false;" value="{_p var='vote_feedback'}" />
                    </div>
                    {/if}
                </div>
                </div>
                <div class="feedbacks_lightbox_info_title">
                	<input type="hidden" id="feedback_show_{$$aFeedBack.feedback_id}" value="{$aFeedBack.feedback_id}"/>
                    <a title="{$aFeedBack.title}"  href="javascript:void(0);" onclick="javascript:viewFeedback({$aFeedBack.feedback_id});return false;">{$aFeedBack.title|shorten:25:'...'}</a>
                </div>
				{if $aFeedBack.is_featured == 1}
                    <div class="item-flag-icon">
                      <div class="sticky-label-icon sticky-featured-icon">
                          <span class="flag-style-arrow"></span>
                          <i class="ico ico-diamond"></i>
                      </div>
                    </div>
                {/if}
                <div class="feedbacks_lightbox_info">
                    <p class="extra_info">
                        {$aFeedBack.total_view} {if $aFeedBack.total_view == 1}{_p var='view'}{else}{_p var='views_n'}{/if},&nbsp;
                        {$aFeedBack.total_comment} {if $aFeedBack.total_comment == 1}{_p var='comment'}{else}{_p var='comments_n'}{/if},&nbsp;
                        {$aFeedBack.total_attachment} {if $aFeedBack.total_attachment == 1}{_p var='picture'}{else}{_p var='pictures_n'}{/if}<br/>
                        {$aFeedBack.info|shorten:36:'...'}
                    </p>
                    <div class="servertity_status_feedback">
                        {if !empty($aFeedBack.status)}
                        	<span class="feedbacks_lightbox_info_date" style="float:left">
                            	<b class="extra_info">{_p var='status'}: </b><span class="feedback_status_{$aFeedBack.status} feedback_status" style="border-radius: 2px 2px 2px 2px; background-color:#{$aFeedBack.color}; color:#FFFFFF; font-weight:700; padding:2px 3px;">{$aFeedBack.status}</span>
                        	</span>
                        {/if}
                        {if !empty($aFeedBack.feedback_servertity_name) && Phpfox::isAdmin()}
    						<span class="feedbacks_lightbox_info_date" {if empty($aFeedBack.status)}style="float:left"{else}style="float:right"{/if}>
    							<b class="extra_info">{_p var='serverity'}: </b><span class="feedback_status" style="border-radius: 2px 2px 2px 2px; background-color:#{$aFeedBack.feedback_serverity_color}; color:#FFFFFF; font-weight:700; padding:2px 3px;">{$aFeedBack.feedback_servertity_name}</span>
    						</span>
    					{/if}
    					</div>
                </div>
				<div class="clear"></div>
                
             </div> 
            {/foreach}     
{else}
    <p style="text-align:center;margin-top:15px;">{_p var='no_feedback_found'}</p>
{/if}
       
   </div>