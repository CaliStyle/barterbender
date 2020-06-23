<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
 ?>

{if count($aCoupon) > 0}
 <!-- Coupon detail listing space -->
<div class ="ync coupon_header">
	<!-- Coupon title -->
	<div class="ync coupon-title">
	{$aCoupon.title}
	</div>
	<div class="ync coupon-owner-category">
		<span class="ync coupon-owner"> 
			{phrase var="created_by"} {$aCoupon|user}
		</span>
		<span class="ync dot">.</span>
		<span class="ync coupon-category">
			{phrase var="category"}:
			{if $aCoupon.category}
				<a href="{permalink module='coupon.category' id=$aCoupon.category_id title=$aCoupon.category}">
				{$aCoupon.category|convert|clean}
				</a>
			{else}
				<span class="ync notice">{phrase var="none"}</span>
			{/if}
		</span> 
	</div>
</div>
<!-- Control action space -->

{if $bCanAction or $bCanEdit or $bCanDelete or $bCanPause or $bCanClose}

<div class="item_bar">
	<div class="item_bar_action_holder">
		<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{phrase var='actions'}</span>
            <i id="icon_edit" class="fa fa-edit" style="font-size:16px; margin:12px; color:#626262; position: absolute;top: 0"></i>
        </a>
		<ul class="dropdown-menu dropdown-menu-right">
			{if $bCanEdit}
			<li>
				<a href="{permalink module='coupon.add' id= 'id_'$aCoupon.coupon_id title='title_'$aCoupon.title}"> {phrase var='core.edit'} </a>
			</li>
			{/if}
			{if $bCanDelete }
			<li class="item_delete">
                <a href="{permalink module='coupon.delete' id=$aCoupon.coupon_id title=$aCoupon.title}" class="sJsConfirm" data-message="{phrase var='are_you_sure_you_want_to_delete_this_coupon' phpfox_squote=true}" > {phrase var='delete'} </a>
			</li>
			{/if}

			{if $bCanPause }
			<li class="item_pause">
                <a title="{phrase var='pause'}"
                   onclick="$Core.jsConfirm({l}message: '{phrase var='are_you_sure_you_want_to_pause_this_coupon' phpfox_squote=true}'{r},function(){l}$.ajaxCall('coupon.pauseOwnCoupon', 'item_id={$aCoupon.coupon_id}');{r});"
                   href="javascript:void(0)"> {phrase var='pause'} </a>
            </li>
			{/if}

			{if $bCanResume }
			<li class="item_resume">
                <a title="{phrase var='resume'}"
                   onclick="$Core.jsConfirm({l}message: '{phrase var='are_you_sure_you_want_to_resume_this_coupon' phpfox_squote=true}'{r},function(){l}$.ajaxCall('coupon.resumeOwnCoupon', 'item_id={$aCoupon.coupon_id}');{r});"
                   href="javascript:void(0)"> {phrase var='resume'} </a>
            </li>
			{/if}
			
			{if $bCanClose}
			<li>
                <a title="Close"
                   onclick="$Core.jsConfirm({l}message: '{phrase var='are_you_sure_you_want_to_close_this_coupon' phpfox_squote=true}'{r},function(){l}$.ajaxCall('coupon.closeOwnCoupon', 'item_id={$aCoupon.coupon_id}');{r});"
                   href="javascript:void(0)"> {phrase var='close'} </a>
            </li>
			{/if}

            {if $bCanPublish}
            <li id="js_coupon_publish_{$aCoupon.coupon_id}">
                {if $aCoupon.status == 7}
                <a title="{phrase var='publish_this_coupon'}"
                   onclick="$Core.jsConfirm({l}message: '{phrase var='confirm_publish_coupon' total_fee=$aCoupon.total_fee symbol_currency_fee=$aCoupon.symbol_currency_fee }'{r},function(){l}$.ajaxCall('coupon.publish', '&iCouponId={$aCoupon.coupon_id}&amp;iCouponStatus={$aCoupon.status}', 'GET');{r});"
                   href="javascript:void(0)"> {phrase var='publish'} </a>
                {elseif $aCoupon.status == 8}
                <a title="{phrase var='publish_this_coupon'}" href="javascript:void(0)"
                   onclick="$Core.jsConfirm({l}message: '{phrase var='confirm_publish_again'}'{r},function(){l}$.ajaxCall('coupon.publish', '&iCouponId={$aCoupon.coupon_id}&amp;iCouponStatus={$aCoupon.status}', 'GET');{r});">
                    {phrase var='publish'} </a>
                {/if}
            </li>
            {/if}

            {if Phpfox::getUserParam('coupon.can_feature_coupon')}
                <li id="js_coupon_feature_{$aCoupon.coupon_id}" {if $aCoupon.is_featured} style="display: none;" {/if}>
                    <a href="#" title="{phrase var='feature_this_coupon'}" onclick="$.ajaxCall('coupon.feature', '&iCouponId={$aCoupon.coupon_id}&amp;iFeatured=1', 'GET'); return false;">{phrase var='feature'}</a>
                </li>
                <li id="js_coupon_unfeature_{$aCoupon.coupon_id}" {if !$aCoupon.is_featured} style="display: none;" {/if}>
                    <a href="#" title="{phrase var='unfeature_this_coupon'}" onclick="$.ajaxCall('coupon.feature', '&iCouponId={$aCoupon.coupon_id}&amp;iFeatured=0', 'GET'); return false;">{phrase var='unfeature'}</a>
                </li>
            {elseif !$aCoupon.is_featured}
                <li id="js_coupon_feature_{$aCoupon.coupon_id}">
                    <a href="#" title="{phrase var='feature_this_coupon'}" onclick="$.ajaxCall('coupon.payFeature', '&iCouponId={$aCoupon.coupon_id}', 'GET'); return false;">{phrase var='feature'}</a>
                </li>
            {/if}

            {if Phpfox::getUserParam('coupon.can_approve_coupon') && $bNeedApproved}
            <li id="js_coupon_approved_{$aCoupon.coupon_id}">
                <a href="#" title="{phrase var='approve_this_coupon'}" onclick="$.ajaxCall('coupon.approveCoupon', 'iCouponId={$aCoupon.coupon_id}', 'GET');return false;">{phrase var='approve'}</a>
            </li>
            <li id="js_coupon_denied_{$aCoupon.coupon_id}">
                <a href="#" title="{phrase var='denied_this_coupon'}" onclick="$.ajaxCall('coupon.denyCoupon', 'iCouponId={$aCoupon.coupon_id}', 'GET'); return false;">{phrase var='deny'}</a>
            </li>
            {/if}

			<li class="item_statistic">
				<a href="{permalink module='coupon.list' id=$aCoupon.coupon_id title=$aCoupon.title}"> {phrase var='coupon_statistics'} </a>
			</li>
		</ul>
	</div>
</div>
{/if}

<div class="ync coupon-menu">
	{if $aCoupon.is_approved}
		{if Phpfox::isUser()}
		<!-- Information content -->
		<div>
			<ul>
				<li id="coupon_detail_favorite_link">
                    <a href="javascript:void(0)" onclick="FavoriteAction('favorite',{$aCoupon.coupon_id});return false;"
                       id="js_favorite_link_{$aCoupon.coupon_id}" class="ync_btn_favorite"
                       title="{phrase var='add_to_your_favorite'}" {if ($bIsFavorited)}style="display:none;" {/if}>
                    <i class="fa fa-heart"></i>&nbsp; {phrase var='favorite'}
					</a>
                    <a href="javascript:void(0);" title="{phrase var='remove_from_your_favorite'}" href="#"
                       onclick="FavoriteAction('unfavorite',{$aCoupon.coupon_id});return false;"
                       class="ync_btn_notfavorite" id="js_unfavorite_link_{$aCoupon.coupon_id}" {if
                       (!$bIsFavorited)}style="display:none;" {/if}>
                    <i class="fa fa-heart-o"></i>&nbsp; {phrase var='unfavorite'}
					</a>
				</li>
			</ul>
		</div>
		<div class="sub_section_menu ync_sub_section_menu">
			<ul>
				{if $bCanFollow}
				<li id="coupon_detail_follow_link">
                    <a href="javascript:void(0);" onclick="FollowAction('follow',{$aCoupon.coupon_id});return false;"
                       id="js_follow_link_{$aCoupon.coupon_id}" title="{phrase var='follow_this_coupon'}" {if
                       ($aCoupon.has_followed)}style="display:none;" {/if}>
                    <i class="fa fa-plus-circle"></i>&nbsp; {phrase var='follow'}
					</a>
                    <a href="javascript:voi(0);" title="{phrase var='unfollow_this_coupon'}"
                       onclick="FollowAction('unfollow',{$aCoupon.coupon_id});return false;"
                       id="js_unfollow_link_{$aCoupon.coupon_id}" {if (!$aCoupon.has_followed)}style="display:none;" {/if}>
                    <i class="fa fa-plus-circle"></i>&nbsp; {phrase var='unfollow'}
					</a>
				</li>
				{/if}
				<li>
                    <a href="javascript:void(0);"
                       onclick="$Core.box('coupon.inviteBlock',800,'id={$aCoupon.coupon_id}&url={permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}'); return false;">
                        <i class="fa fa-user-plus"></i>&nbsp; {phrase var='invite_friends'}
					</a>
				</li>
			</ul>
		</div>
		{/if}
	{/if}
</div>

<div class="ync coupon_detail_information item_view_content">
	<div class="ync coupon_detail_top clearfix">
		<div class="ync coupon-photo ync-detail-image">
			<span style="background-image: url({if $aCoupon.image_path}{img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_400_square' width=140 height=140}{else}{$sDefaultLink}{/if})">
			</span>
		</div>
		
		<div class="ync coupon-basic-info">
			<div class = "ync coupon-discount">
				{if isset($aCoupon.discount) }	
				<span class = "ync_label">{phrase var='discount'}</span>
				<span class = "ync_value">{$aCoupon.discount}</span> 
				{/if}
		        {if isset($aCoupon.special_price) }
				<span class = "ync_label">{phrase var='special_price'}</span>
				<span class = "ync_value">{$aCoupon.special_price}</span>  
		        {/if}
			</div>
			<div class="ync coupon-date">
				<span class="ync label">{phrase var="expired_date"}:</span>
				<span class="ync value">{if $aCoupon.expire_time}{$aCoupon.expire_time|date:'coupon.coupon_view_time_stamp'}{else}{phrase var="unlimited"}{/if}</span>
			</div>
			<div class="ync coupon-date">
				<span class="ync label">{phrase var="start_date"}:</span>
				<span class="ync value">{$aCoupon.start_time|date:'coupon.coupon_view_time_stamp'}</span>
			</div>	
			<div class="ync coupon-date">
				<span class="ync label">{phrase var="end_date"}:</span>
				<span class="ync value">{$aCoupon.end_time|date:'coupon.coupon_view_time_stamp'}</span>
			</div>
			
			<div class = "ync coupon-claim ync_claim">
				<div class = "ync_claim_sum clearfix">
					<!-- Claimed -->
					<span class = "ync_label">{phrase var='claimed'}</span>
					<span class = "ync_value">{$aCoupon.total_claim}</span>
					<!-- Claimed Bar -->
					<div class = "ync_claim_total">
						<div class = "ync_claim_active" style = "width:{$iPercent}%;">
						</div>
					</div>
					<!-- Claims Remain -->
					<p class = "ync_claim_remain">
						{$sRemain}
					</p>
					<!-- Remain Time -->
					<div class = "ync_claim_remain_time">
						{$sRemainTime}
					</div>
				</div>
			</div>	
			
			<!-- Code details -->
			{if Phpfox::isUser()}
				{if $aCoupon.has_claimed or $bCanClaim }
				<div class = "ync coupon-code ync_code_info">
					<!-- <p>{phrase var="coupon_code"}</p> -->
					<span class = "code_value" id ="coupon_code_display" {if !$aCoupon.code} style="display:none;"{/if}>{$aCoupon.code}</span>
                    <a href="javascript:void(0)"
                       onclick="tb_show('{_p var='term_conditions'}', $.ajaxBox('coupon.getTermAndCondition', 'width=600&iCouponId={$aCoupon.coupon_id}')); return false;"
                       id="coupon_code_button" class="ync_btn ync_getcode_btn ajax_link" {if $aCoupon.has_claimed}
                       style="display:none;" {/if}>
                    {phrase var = 'coupon.get_code'} &nbsp;<i class="fa fa-long-arrow-right"></i>
					</a>
					<a href="{url link='coupon.print'}{$aCoupon.coupon_id}" target="_blank" id="coupon_print_button" class="ync_btn ync_print_btn ajax_link" {if !$aCoupon.has_claimed} style="display:none;"{/if}>
						<i class="fa fa-print"></i>&nbsp; {phrase var = 'coupon.print_code'}
					</a>
				</div>
				{/if}
			{/if}
		</div>
	</div>


	
	{if count($aFields)}
	<div class="ync coupon-custom_fields item_content item_view_content">
        {foreach from=$aFields item=aField}
            {if !empty($aField.value)}
                {template file='coupon.block.custom.view'}
            {/if}
        {/foreach}
	</div>
	{/if}
	
	{if isset($aCoupon.site_url)}
		<div class="ync coupon-site_url">
   	 	<div class="ync_label">{phrase var='site_url'}</div>
   	 	<a href="{$aCoupon.site_url}" target="_blank">{$aCoupon.site_url}</a>
   		</div>
    {/if}
	
	
	<div class = " ync coupon-information ync_code_txt">
		<div class="ync_label">{phrase var="information"}</div>
		<div clas="ync coupon-description">
			<div class="ync_label">{phrase var="short_description"}</div>
			<div class="ync value">
				{if $aCoupon.description}
					{$aCoupon.description|parse}
				{else}
					{phrase var="none"}
				{/if}
			</div>
		</div>
		
		<div clas="ync coupon-location">
			<span class="ync_label">{phrase var="location"}:</span>
			<span class="ync value">
				{$aCoupon.location_venue}{if $aCoupon.city}, {$aCoupon.city} {/if}{if $aCoupon.country_iso}, {$aCoupon.country_iso|location}{/if}
			</span>
			{ if $aCoupon.is_show_map}
			<iframe width="510" height="430" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;geocode=&amp;q={$aCoupon.location_venue}+{$aCoupon.country_iso|location}+{$aCoupon.city}&amp;aq=&amp;sll={$aCoupon.latitude},{$aCoupon.longitude}&amp;sspn=0,0&amp;vpsrc=6&amp;doflg=ptk&amp;ie=UTF8&amp;hq={$aCoupon.location_venue}+{$aCoupon.country_iso|location}+{$aCoupon.city}&amp;ll={$aCoupon.latitude},{$aCoupon.longitude}&amp;spn=0,0&amp;t=m&amp;z=12&amp;output=embed"></iframe>
			{ /if }
		</div>
	</div>
</div>
<!-- Comment space -->
{module name='feed.comment'}

{literal}
<script type="text/javascript">

function FavoriteAction(sActionType, iItemId){
	if(sActionType=='favorite')
	{
		$('#js_unfavorite_link_'+iItemId).show(); 
		$('#js_favorite_link_'+iItemId).hide();
		tb_show("{/literal}{phrase var='core.notice'}{literal}", $.ajaxBox('coupon.addFavorite', 'width=' + 400 + '&id=' + iItemId));	
		return false;
	}
	else
	{
		$('#js_favorite_link_' + iItemId).show(); 
		$('#js_unfavorite_link_' + iItemId).hide();
		$.ajaxCall('coupon.deleteFavorite','id='+iItemId,'GET');
	}
}

function FollowAction(sActionType, iItemId){
    if(sActionType=='follow')
    {
        $('#js_unfollow_link_'+iItemId).show(); 
        $('#js_follow_link_'+iItemId).hide();
        tb_show("{/literal}{phrase var='core.notice'}{literal}",$.ajaxBox('coupon.addFollow','width=' + 400 + '&id='+iItemId));
        return false;
    }
	else
	{
		$('#js_follow_link_' + iItemId).show(); 
		$('#js_unfollow_link_' + iItemId).hide();
		$.ajaxCall('coupon.deleteFollow','id='+iItemId,'GET');
	}
}
</script>
{/literal}
{/if}
