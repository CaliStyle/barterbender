<?php
    /*
    * @copyright        [YouNet_COPYRIGHT]
    * @author           YouNet Company
    * @package          Module_FeedBack
    * @version          2.01
    *
    */
?>

{literal}
<style type="text/css">
    
.table_left_addfeedback {
    float: left;
}

input:not([type="checkbox"]):not([type="radio"]){
    width: 100% !important;
}

.mb-feedback-forum{
    display: none;
}

#col-add {
    float: right;
    min-height: 400px;
    width: 53%;
    background: #fff;
    border-left: 1px solid #ccc;
    padding: 10px;
box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}

#col-add > form .table_right{
    padding: 0px;
    border: none;
}
#col-add > form input:not([type="submit"]),
#col-add > form textarea{
    padding: 10px;
    background: #f4f4f4;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}
#col-add > form select{
    margin: 0px;
}
#show_feedback {
    float: right;
    width: 55%;
    display: none;
    min-height: 400px;
    border-left: 1px solid #ccc;
}

#col-view {
    float: left;
    width: 47%;
    padding: 10px;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}

.lightbox_feedback_list {
    clear: both;
    float: left;
    padding: 0px 0;
    width: 100%;
    background: transparent;
    position: relative;
}

.feedbacks_lightbox_votes {
    float: left;
    margin-right: 8px;
    overflow: hidden;
    text-align: center;
    width: 60px;
}

.feedbacks_lightbox_votes .feedback_votes {
    font-size: 11px;
    padding: 7px 1px;
    text-align: center;
    text-transform: uppercase;
    background-color: #247BC2;
    color: #FFF;
}

.feedbacks_lightbox_votes .feedback_votes p {
    display: block;
    font-size: 15px;
}

.feedbacks_lightbox_info_title {
    float: left;
    width: 280px;
    overflow: hidden;
}

.feedbacks_lightbox_info_title a {
    font-size: 13px;
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

div.feedbacks_lightbox_info {
    float: left;
    width: 280px;
    overflow: hidden;
    font-size: 13px;
}

#category_feedback_show {
    /*width: 380px;*/
}

.feedbacks_lightbox_info_date {
    color: #B6B6B6;
    font-size: 11px;
    padding: 5px 1px !important;
}

.lightbox_vote_button a,
.lightbox_vote_button a:visited,
.lightbox_vote_button span.feedback-novote {
    -moz-border-radius: 3px 3px 3px 3px;
    background-color: #333;
    border-bottom-color: #888888;
    color: #fff !important;
    cursor: pointer;
    display: inline-block;
    font-size: 10px;
    font-weight: bold;
    margin-top: 10px;
    padding: 5px 10px;
    float: right;
}

.top-feedback {
    float: left;
    width: 100%;
}

.feedback_no_found {
    text-align: center;
    padding-top: 12px;
}

div.feedback-forum {
    clear: both;
    margin-top: 15px;
    margin-bottom: 15px;
}

.feedback-forum .lightbox_more.button {
    text-align: center;
    width: 60%;
    display: block;
    font-size: 12px;
    -moz-border-radius: 4px 4px 4px 4px;
    border: medium none;
    color: #FFFFFF;
    cursor: pointer;
    font-weight: bold;
    margin: 0;
    overflow: visible;
    padding: 4px;
    vertical-align: middle;
    text-decoration: none;
    margin-left: 50px;
}

span.feedback_status_Started.feedback_status {
    color: #6FBC00;
}

span.feedback_status_Planned.feedback_status {
    color: #F0BA00;
}

span.feedback_status_Under.Review.feedback_status {
    color: #F53B60;
}

span.feedback_status_Completed.feedback_status {
    color: #3366ff;
}

span.feedback_status_Declined.feedback_status {
    color: #00cc33;
}

span.feedback_status_Response.feedback_status {
    color: #000000;
}

span.feedback_status {
    color: #195B85;
}

span.featured {
    float: right;
    position: absolute;
    right: 5px;
}
.feedbacks_lightbox_info_title .item-flag-icon{
    position: absolute;
    right: 5px;
    top: 0;
}
.lightbox_vote_button {
    margin-top: -10px;
    padding-top: 5px;
}

span.feedback-novote.inactive {
    color: #ccc !important;
    font-weight: normal !important;
    background-color: #888888;
}

#formaddfeed h3 {
    margin: 0 0 10px;
}

span.feedback-novote.inactive {
    background-color: #888888 !important;
}

.js_box_content {
    /*padding: 0;*/
}

.body_feedback{
    background: #f1f1f1
}
.body_feedback .table_right {
    margin-left: 22%;
    text-align: left;
}

.table_clear {
}

.feedbacks_browse_info {
    padding: 15px;
}

.feedbacks_browse_votes {
    height: 75px;
    width: 75px;
    border: 1px solid #ccc;
    margin-top: 15px;
}

.menu_feedback {
    min-height: 30px;
    background: #9a9a9a;
}

.menu_feedback form {
    float: right;
}

.menu_feedback span.site_title {
    margin-top: 10px;
    padding: 3px;
}

.header_feedback {
    float: left;
    width: 100%;
}

.header_feedback .menu {
    margin: 6px 0 6px 0px;
}

.header_feedback .menu a {
    float: left;
    padding: 10px 15px;
    font-size: 14px;
    color: #fff;
}

.header_feedback .menu a:hover,
.header_feedback .menu a.active {
    background: #fff;
    color: #000;
    text-decoration: none;
}

.header_feedback ul li:hover {
    cursor: pointer;
}

.js_box div.row1,
.js_box div.row2 {
    display: block;
    overflow: hidden;
    padding: 5px;
    margin-bottom: 10px;
}

.js_box .js_feed_comment_border {
    width: 98%;
}

.header_feedback div.menu_cat {
    float: left;
}

.js_box .loadding {
    height: 300px;
    margin: 0 auto;
    text-align: center;
    vertical-align: middle;
}

.js_box .loadding img {
    margin: 50px;
}

.js_box .fb_search {
    float: right;
}

.fb_search .form-group input[type="submit"]:focus{
    background: #5cb85c;
}

.button.vote_button {
    padding: 1px;
    font-size: 10px;
    margin-top: 12px;
}

#menu_bar_fb {
    width: 100%;
    overflow: hidden;
    float: left;
}

#menu_bar_fb .menu_cat a {
    font-size: 12px;
    text-transform: uppercase;
}

.js_box .fb_search input.button:focus{
    padding-right: 20px;
}

.body_feedback .p_top_8 {
    padding-top: 4px;
    float: left;
    margin-right: 15px;
}

.email_popup_feedback {
    width: 80%;
}

.fullname_popup_feedback {
    width: 80%;
}

#js_form_feedback .table_left {
    float: left;
    width: 22%;
}

.votable {
    color: #FF5656;
    padding: 1px;
    font-size: 10px;
    border-radius: 0;
    text-transform: uppercase;
}
.ynf_box_header{
    background: #9A9A9A !important;
    padding-bottom: 0px;
    border-radius: 0 !important;
}
.js_box_title.ui-draggable-handle{
    min-height: 45px !important;
}
.js_box.ui-draggable{
    background: #FFF;
    border-radius: 0;
    --webkit-border-radius: 0;
}
.servertity_status_feedback span{
    text-transform: uppercase;
    font-weight: normal !important;
}

.servertity_status_feedback .feedback_status{
    padding: 5px 10px !important;
    border-radius: 0 !important;
}
.ynfb_table{
    margin-bottom: 10px;
}
.ynfb_table input{
    text-indent: 0 !important;
}
.lightbox_vote_button button.btn.btn-success.btn-xs {
    width: 100%;
}
</style>
{/literal}


<script src="{$core_path}module/feedback/static/jscript/feedback.js"></script> 

<div class="js_box_title ynf_box_header clearfix" style="display:block">
    <div class="fb_search">

        <form class="form-inline" method="GET" accept-charset="utf-8"  action="{$sFormUrl}" onbeforesubmit="$Core.Search.checkDefaultValue(this,'Search Feedback...');" >
            <div class="form-group" style="float:left; width: 300px; margin-right: 10px;">
                <input type="text" class="form-control" style="width:100%" name="search[search]" />
            </div>
            <div class="form-group" style="float:left;">
                <input type="submit" name="search[submit]" value="{_p var='search'}" class="btn btn-success" style="margin-top: 0px;" />
            </div>
        </form>

    </div>

    <div class="menu_feedback clearfix">
        <div class="header_feedback">

        <div class="menu">
            <div id="menu_bar_fb">
            	<div id="dbTmContent382" style="width:100%; left:0px; position:relative;overflow-x: auto">
            		<div class="menu_cat"><a  class="category_feedback_entry active" id="feedback_category_a" href="javascript:void(0);" onclick="javascript:viewFeedbackByCategory('a');return false">{_p var='all'}</a></div>
            		{foreach from=$aCats item=aCat}
            		<div class="menu_cat"><a class="category_feedback_entry" id="feedback_category_{$aCat.category_id}" href="javascript:void(0);" onclick="javascript:viewFeedbackByCategory({$aCat.category_id});return false">{$aCat.name}</a></div>
            		{/foreach}
            		<div class="menu_cat"><a class="category_feedback_entry" id="feedback_category_0" href="javascript:void(0);" onclick="javascript:viewFeedbackByCategory(0);return false;">{_p var='uncategorized'}</a></div>
            	</div>
            </div>
        </div>

       </div>
    </div>
</div>

<div class="body_feedback clearfix"> 
    <div id="formaddfeed">
        <div id="col-add">
            <form method="post" enctype="multipart/form-data" id="js_form_feedback" action="{url link='feedback.addfeedback'}" onsubmit="$(this).ajaxCall('feedback.addFeed');return requestform(); return false;">
                {if phpfox::getUserId()} {_p var='post_a_feedback'}{else}{$visitor}{/if}
                <div  id="errofeedback"></div>
                <input type="hidden" value="1" name="post_ajax_feedback" id="post_ajax_feedback"/>
                <div class="ynfb_table form-group" style="margin-top: 10px;">
                    <label>
                        {_p var='title'}{required}
                    </label>
                    <input type="text" class="form-control" name="val[title]" value="{value type='input' id='title'}" id="title"  />
                </div>
                <div class="ynfb_table form-group">
                    <label>
                        {_p var='description'}{required}
                    </label>
                    <textarea class="form-control"  name="val[description]" cols="30" rows="5" ></textarea>
                    <div class="clear"></div>
                </div>
                <div class="ynfb_table form-group">
                    <label>
                        {_p var='category'}
                    </label>
                        <select class="form-control" id="cat[category_id]" name="val[category_id]">
                            <option label="{_p var='select'}:" value="0">{_p var='select'}:</option>
                            {foreach from=$aCats item=cat}
                            <option value="{$cat.category_id}">{$cat.name}</option>            
                            {/foreach}        
                        </select>       
                </div>
                <div class="ynfb_table form-group">
                    <label>
                        {_p var='serverity'}
                    </label>
                        <select class="form-control" id="ser[serverity_id]" name="val[serverity_id]">
                            <option label="{_p var='select'}:" value="0">{_p var='select'}:</option>
                            {foreach from=$aSers item=aSer}
                            <option value="{$aSer.serverity_id}">{$aSer.name}</option>
                            {/foreach}
                        </select>
                </div>
                {if Phpfox::getUserId()}
                <div class="ynfb_table form-group">
                    <label>
                        {_p var='feedback_visibility'}
                    </label>
                        <select class="form-control" id="privacy[serverity_id]" name="val[privacy]">
                            <option label="{_p var='public'}" value="1">{_p var='public'}</option>
                            <option value="2" label="{_p var='private'}">{_p var='private'}</option>
                        </select>
                </div>
                {/if}
                {if !Phpfox::getUserId()}
                <div class="ynfb_table form-group">
                    <label>
                        {_p var='email'}{required}
                    </label>
                        <input class="email_popup_feedback form-control" type="text" name="val[email]" value="{value type='input' id='email'}" id="email" size="40" />
                </div>
                <div class="ynfb_table form-group">
                    <label>
                        {_p var='full_name'}{required}
                    </label>
                        <input class="fullname_popup_feedback form-control"  type="text" name="val[full_name]" value="{value type='input' id='full_name'}" id="full_name" size="40" />
                </div>
                {/if}
                {if Phpfox::isModule('tag')}{module name='tag.add' sType=feedback}{/if}
                <div class="table_clear">
                    <input style="margin:7px 0 0 0;" id="js_submit_form_feedback" type="submit" name="submit1" value="{_p var='post_feedback'}" class="btn btn-primary" onclick="$(this).addClass('disabled').attr('disabled','disabled');$('#js_form_feedback').ajaxCall('feedback.addFeed');return false;" />
                </div>
                <div class="mb-feedback-forum">
                    <button onclick=" window.location='{$feedback}';" class="btn btn-success" style="width:100%;margin-top: 2px;" >{_p var='go_to_feedback_forum'}</button>
                </div>

            </form>
        </div>
        <div id="show_feedback">
     	    <div class="feedbacks_browse_info">
    		    <p class="feedbacks_browse_info_title"></p>
    		    <p class="feedbacks_browse_info_date"></p>
    		    <p class="feedback_description"></p>
    	    </div>
        </div>
    </div>
    <div id="col-view">
        <div class="clear"></div>
        <div id="category_feedback_show" class="clearfix">
         <div id="feedback_show_by_cat_a" class="top-feedback">
        {if count($aAllFeedBacks) > 0}
        	    <div class="feedback_popup_right">   
                {foreach from=$aAllFeedBacks item=aFeedBack name=fbl}
                <div id="js_feedback_entry{$aFeedBack.feedback_id}" class="js_blog_parent {if is_int($phpfox.iteration.fbl/2)}row1{else}row2{/if}{if $phpfox.iteration.fbl == 1 && !PHPFOX_IS_AJAX} row_first{/if}{if $aFeedBack.is_approved != 1} {/if}">
                <div class="lightbox_feedback_list  {if $aFeedBack.is_featured == 1}featuredbg{/if}">
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
			
                        {if $aFeedBack.votable && Phpfox::isUser()}
                        <div class="lightbox_vote_button" id="feedback_vote_{$aFeedBack.feedback_id}">
                        {if $aFeedBack.isVoted}
                    	    <div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
                        	    <button class="btn btn-success btn-xs"  type="button" onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'down',{$user_id});return false;" >{_p var='remove_feedback'}</button>
                    	    </div>
                        {else}
                        <div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
                            <button class="btn btn-success btn-xs" type="button"  onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'up',{$user_id});return false;" >{_p var='vote_feedback'}</button>
                        </div>
                        {/if}
			</div>
                        {elseif Phpfox::isUser()}
                            <div id="feedback_votable_{$aFeedBack.feedback_id}" style="margin-top: 2px;">
                              <span class="votable" >{_p var='vote_closed'}</span>
                            </div>
                        {/if}
                        
                    </div>
                    <div class="feedbacks_lightbox_info_title">
                	    <input type="hidden" id="feedback_show_{$$aFeedBack.feedback_id}" value="{$aFeedBack.feedback_id}"/>
                        <a class="link ajax_link" title="{$aFeedBack.title}"  href="{$aFeedBack.bookmark_url}">{$aFeedBack.title|shorten:36:'...'}</a>
                        {if $aFeedBack.is_featured == 1}
	                        <div class="item-flag-icon">
                              <div class="sticky-label-icon sticky-featured-icon">
                                  <span class="flag-style-arrow"></span>
                                  <i class="ico ico-diamond"></i>
                              </div>
                            </div>
                     	{/if}
                    </div>
                    <div class="feedbacks_lightbox_info">
                        <p class="extra_info">
                            {$aFeedBack.total_view} {if $aFeedBack.total_view == 1}{_p var='view'}{else}{_p var='views_n'}{/if}&nbsp;.
                            {$aFeedBack.total_comment} {if $aFeedBack.total_comment == 1}{_p var='comment'}{else}{_p var='comments_n'}{/if}&nbsp;.
                            {$aFeedBack.total_attachment} {if $aFeedBack.total_attachment == 1}{_p var='picture'}{else}{_p var='pictures_n'}{/if}<br/>
							{$aFeedBack.info}
                        </p>
                        <div class="servertity_status_feedback">
                        {if !empty($aFeedBack.status)}
                        	<span class="feedbacks_lightbox_info_date" style="float:left">
                            	<span style="text-transform: uppercase" class="extra_info">{_p var='status'}: </span><span class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.color}; color:#FFFFFF; padding:5px 10px; text-transform: uppercase">{$aFeedBack.status}</span>
                        	</span>
                        {/if}
                        {if !empty($aFeedBack.feedback_servertity_name) && Phpfox::isAdmin()}
    						<span class="feedbacks_lightbox_info_date" {if empty($aFeedBack.status)}style="float:left"{else}style="float:right"{/if}>
    							<span style="text-transform: uppercase" class="extra_info">{_p var='serverity'}: </span>
                                <span class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.feedback_serverity_color}; color:#FFFFFF; padding:5px 10px; text-transform: uppercase">{$aFeedBack.feedback_servertity_name}</span>
    						</span>
    					{/if}
    					</div>
                    </div>
                </div>
                   
                </div>
                {/foreach}        
            </div> 
            
       
        {else}
        	<div class="extra_info feedback_no_found">{_p var='no_feedback_found'}</div>
        {/if}
         </div>
        {if count($aCategoryFeedBacks) > 0}
        	{foreach from=$aCategoryFeedBacks key="iCategoryId" item="aCatFeedBacks"}
        		<div id="feedback_show_by_cat_{$iCategoryId}" class="top-feedback" style="display:none;">
        		{if count($aCatFeedBacks) > 0}
        		{foreach from=$aCatFeedBacks item=aFeedBack name=fbl}
        			<div id="js_feedback_entry{$aFeedBack.feedback_id}" class="js_blog_parent {if is_int($phpfox.iteration.fbl/2)}row1{else}row2{/if}{if $phpfox.iteration.fbl == 1 && !PHPFOX_IS_AJAX} row_first{/if}{if $aFeedBack.is_approved != 1} {/if}">
		                <div class="lightbox_feedback_list  {if $aFeedBack.is_featured == 1}featuredbg{/if}">
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
				{if $aFeedBack.votable && Phpfox::isUser()}
                                        <div class="lightbox_vote_button" id="feedback_vote_{$aFeedBack.feedback_id}">
		                        {if $aFeedBack.isVoted}
		                    	    <div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
		                        	    <button class="btn btn-success btn-xs"  type="button" onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'down',{$user_id});return false;">{_p var='remove_feedback'}</button>
		                    	    </div>
		                        {else}
		                        <div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
		                            <button class="btn btn-success btn-xs" type="button"  onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'up',{$user_id});return false;">{_p var='vote_feedback'}</button>
		                        </div>
		                        {/if}
		                    </div>
                                           {elseif Phpfox::isUser()}
                            <div id="feedback_votable_{$aFeedBack.feedback_id}" style="margin-top: 2px;">
                              <span class="votable" >{_p var='vote_closed'}</span>
                            </div>
                        {/if}
                                        
		                    </div>
		                    <div class="feedbacks_lightbox_info_title">
		                	    <input type="hidden" id="feedback_show_{$$aFeedBack.feedback_id}" value="{$aFeedBack.feedback_id}"/>
		                        <a class="link ajax_link" title="{$aFeedBack.title}"  href="{$aFeedBack.bookmark_url}">{$aFeedBack.title|shorten:36:'...'}</a>
		                        {if $aFeedBack.is_featured == 1}
	                       			 <div class="item-flag-icon">
                                      <div class="sticky-label-icon sticky-featured-icon">
                                          <span class="flag-style-arrow"></span>
                                          <i class="ico ico-diamond"></i>
                                      </div>
                                    </div>
		                     	{/if}
		                    </div>
		                    <div class="feedbacks_lightbox_info">
		                        <p class="extra_info">
                                    {$aFeedBack.total_view} {if $aFeedBack.total_view == 1}{_p var='view'}{else}{_p var='views_n'}{/if},&nbsp;
                                    {$aFeedBack.total_comment} {if $aFeedBack.total_comment == 1}{_p var='comment'}{else}{_p var='comments_n'}{/if},&nbsp;
                                    {$aFeedBack.total_attachment} {if $aFeedBack.total_attachment == 1}{_p var='picture'}{else}{_p var='pictures_n'}{/if}<br/>
                                    {$aFeedBack.info}
		                        </p>
		                        <div class="servertity_status_feedback">
		                        {if !empty($aFeedBack.status)}
		                        	<span class="feedbacks_lightbox_info_date" style="float:left">
		                            	<b class="extra_info">{_p var='status'}: </b><span class="feedback_status_{$aFeedBack.status} feedback_status" style="border-radius: 2px 2px 2px 2px; background-color:#{$aFeedBack.color}; color:#FFFFFF; font-weight:700; padding:2px 3px;">{$aFeedBack.status}</span>
		                        	</span>
		                        {/if}
		                        {if !empty($aFeedBack.feedback_servertity_name) && Phpfox::isAdmin()}
		    						<span class="feedbacks_lightbox_info_date" {if empty($aFeedBack.status)}style="float:left"{else}style="float:right"{/if}>
		    							<b class="extra_info">{_p var='serverity'}: </b><span class="feedback_status_{$aFeedBack.status} feedback_status" style="border-radius: 2px 2px 2px 2px; background-color:#{$aFeedBack.feedback_serverity_color}; color:#FFFFFF; font-weight:700; padding:2px 3px;">{$aFeedBack.feedback_servertity_name}</span>
		    						</span>
		    					{/if}
		    					</div>
		                    </div>
		                    <div style="clear:both;"></div>
		                </div>
	                    
                	</div>
        		{/foreach} 		
        		{else}
        			<div class="extra_info feedback_no_found">{_p var='no_feedback_found'}</div>
        		{/if}
        		</div>
        	{/foreach}
        {/if}
		<div>
		<div id="feedback_show_by_cat_0" class="top-feedback" style="display:none;">
        {if count($aUncategorizedFeedBacks) > 0}
        	    <div class="feedback_popup_right">   
                {foreach from=$aUncategorizedFeedBacks item=aFeedBack name=fbl}
                <div id="js_feedback_entry{$aFeedBack.feedback_id}" class="js_blog_parent {if is_int($phpfox.iteration.fbl/2)}row1{else}row2{/if}{if $phpfox.iteration.fbl == 1 && !PHPFOX_IS_AJAX} row_first{/if}{if $aFeedBack.is_approved != 1} {/if}">
                <div class="lightbox_feedback_list  {if $aFeedBack.is_featured == 1}featuredbg{/if}">
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
                        {if $aFeedBack.votable && Phpfox::isUser()}
						<div class="lightbox_vote_button" id="feedback_vote_{$aFeedBack.feedback_id}">
                        {if $aFeedBack.isVoted}
                    	    <div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
                        	    <button class="btn btn-success btn-xs"  type="button" onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'down',{$user_id});return false;">{_p var='remove_feedback'}</button>
                    	    </div>
                        {else}
                        <div id="feedback_vote_popup_{$aFeedBack.feedback_id}">
                            <button class="btn btn-success btn-xs" type="button"  onclick="updatevotepopup({$aFeedBack.feedback_id},{$aFeedBack.total_vote},'up',{$user_id});return false;">{_p var='vote_feedback'}</button>
                        </div>
                        {/if}
                    </div>
                                               {elseif Phpfox::isUser()}
                            <div id="feedback_votable_{$aFeedBack.feedback_id}" style="margin-top: 2px;">
                              <span class="votable" >{_p var='vote_closed'}</span>
                            </div>
                        {/if}
                    </div>
                    <div class="feedbacks_lightbox_info_title">
                	    <input type="hidden" id="feedback_show_{$$aFeedBack.feedback_id}" value="{$aFeedBack.feedback_id}"/>
                        <a class="link ajax_link" title="{$aFeedBack.title}"  href="{$aFeedBack.bookmark_url}">{$aFeedBack.title|shorten:36:'...'}</a>
                        {if $aFeedBack.is_featured == 1}
               				<div class="item-flag-icon">
                              <div class="sticky-label-icon sticky-featured-icon">
                                  <span class="flag-style-arrow"></span>
                                  <i class="ico ico-diamond"></i>
                              </div>
                            </div>
               			 {/if}
                    </div>
                    <div class="feedbacks_lightbox_info">
                        <p class="extra_info">
                            {$aFeedBack.total_view} {if $aFeedBack.total_view == 1}{_p var='view'}{else}{_p var='views_n'}{/if},&nbsp;
                            {$aFeedBack.total_comment} {if $aFeedBack.total_comment == 1}{_p var='comment'}{else}{_p var='comments_n'}{/if},&nbsp;
                            {$aFeedBack.total_attachment} {if $aFeedBack.total_attachment == 1}{_p var='picture'}{else}{_p var='pictures_n'}{/if}<br/>
							{$aFeedBack.info}
                        </p>
                        <div class="servertity_status_feedback">
                        {if !empty($aFeedBack.status)}
                        	<span class="feedbacks_lightbox_info_date" style="float:left">
                            	<b class="extra_info">{_p var='status'}: </b><span class="feedback_status_{$aFeedBack.status} feedback_status" style="border-radius: 2px 2px 2px 2px; background-color:#{$aFeedBack.color}; color:#FFFFFF; font-weight:700; padding:2px 3px;">{$aFeedBack.status}</span>
                        	</span>
                        {/if}
                        {if !empty($aFeedBack.feedback_servertity_name) && Phpfox::isAdmin()}
    						<span class="feedbacks_lightbox_info_date" {if empty($aFeedBack.status)}style="float:left"{else}style="float:right"{/if}>
    							<b class="extra_info">{_p var='serverity'}: </b><span class="feedback_status_{$aFeedBack.status} feedback_status" style="border-radius: 2px 2px 2px 2px; background-color:#{$aFeedBack.feedback_serverity_color}; color:#FFFFFF; font-weight:700; padding:2px 3px;">{$aFeedBack.feedback_servertity_name}</span>
    						</span>
    					{/if}
    					</div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
               
                </div>
                {/foreach}        
            </div>    
	        {else}
	        	<div class="extra_info feedback_no_found">{_p var='no_feedback_found'}</div>
	        {/if} 
	        </div>
 		</div>
 		</div>

        <div class="feedback-forum">
            <button onclick=" window.location='{$feedback}';" class="btn btn-success" type="button">{_p var='go_to_feedback_forum'}</button>
        </div> 
    </div>
</div>


<script type="text/javascript">
    var ttifb = {$totalaCats};
    viewScrollBar();
    {literal}
    function requestform()
    {
        var post_ajax=$('#post_ajax_feedback').val();
        if(post_ajax==2)
            return true;
        return false;
    }
    function viewScrollBar()
    {
        var itw = 0;
         $('#dbTmContent382 .menu_cat').each(function(e){
            itw = itw + $('#dbTmContent382 .menu_cat').eq(e).width()+2;
         });
        var btw = $('#menu_bar_fb').width();
        if(itw >= btw)
        {
            $('.header_feedback .control').show();
            $('.dbTmLeft').hide();
            //$('#dbTmContent382').css('width',itw+2+'px');
        }
        else
        {
            $('.header_feedback .control').hide();
        }
    }
    {/literal}
    
</script>

{literal}
<style>
@media screen and (max-width:700px){
    #col-view{display: none}
    #col-add{
        width: 100%;
        float: none;
        border-left: none;
    }
    .ynf_box_header{display: none !important}
    .table_left_addfeedback{
        float: none;
        margin-bottom: 5px;
    }
    .body_feedback .table_right{
        margin-left: 0;
    }
    .mb-feedback-forum{
        display: block;
    }
    .mb-feedback-forum input{
        background: transparent !important;
    }

    .mb-feedback-forum input:hover{
        background: #595959 !important;
        color: #e5e5e5;
    }
}

</style>
{/literal}