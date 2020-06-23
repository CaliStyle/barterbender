<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_coupon_entry{$aCoupon.coupon_id}"{if !isset($bCouponView)} class="yncoupon-listing-item {if $bShowModerator}has-moderation{/if} js_coupon_parent image_hover_holder {if ($phpfox.iteration.coupon%3)==2}{/if}{if $aCoupon.is_approved != 1} {/if}"{/if}>
    
	<div class="ync-image-coupon">
        {if $aCoupon.is_draft != 1}
            {if $aCoupon.status == $aCouponStatus.closed}
            <div class="ync_tags_link ync_close_link">
                {phrase var='closed'}
            </div>
            {elseif $aCoupon.status == $aCouponStatus.pause}
            <div class="ync_tags_link ync_pause_link">
                {phrase var='pause'}
            </div>
            {elseif $aCoupon.status == $aCouponStatus.upcoming}
            <div class="ync_tags_link ync_upcoming_link">
                {phrase var='upcoming'}
            </div>
            {elseif $aCoupon.status == $aCouponStatus.endingsoon}
            <div class="ync_tags_link ync_ending_soon_link">
                {phrase var='ending_soon'}
            </div>
            {elseif $aCoupon.is_approved != 1}
            <div class="ync_tags_link ync_pending_link">
                {phrase var='pending'}
            </div>

            {else}
                <div class="ync_tags_link ync_featured_link"{if !$aCoupon.is_featured} style="display:none;"{/if}>
                    {phrase var='featured'}
                </div>	
            {/if}
        {/if}
        {if $bShowModerator && (int)$aCoupon.is_draft == 0}
            <div class="moderation_row">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aCoupon.coupon_id}">
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
        {/if}
		<a class="ync_coupon_img" href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" title="{$aCoupon.title|clean}">
			<span style="background-image:url({if $aCoupon.image_path}{img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_400'}{else}{$sDefaultLink}{/if})">
			</span>
            {if Phpfox::isAdmin()}
                {if $sView == 'pending'}
    			    {if $aCoupon.have_menu}
                        <a href="#{$aCoupon.coupon_id}" class="moderate_link" rel="coupon">{phrase var='moderate'}</a>
                    {/if}
                {/if}
            {/if}
        </a>

        <div class="ync_author">{phrase var='created_by'} <a href="javascript:void(0)">{$aCoupon|user}</a></div>
    </div>



    <div class="ync_title_info clearfix">
        <div id="js_coupon_edit_title{$aCoupon.coupon_id}" class="ync-title">
            <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="link ajax_link">{$aCoupon.title|clean}</a>
        </div>

        {if isset($aCoupon.discount) }
            <span class="ync-discount-small">- {$aCoupon.discount}</span> 
        {/if}
    
        {if isset($aCoupon.special_price) }
            <span class="ync-discount-small">{$aCoupon.special_price}</span> 
        {/if}

        {if $aCoupon.is_draft} <span class="ync coupon-entry draft-text"> &lt;{phrase var='draft'}&gt;</span> {/if}

        <p>{phrase var='end'}: {$aCoupon.end_time|date:'coupon.coupon_view_time_stamp'}</p>

        {plugin call='coupon.template_block_entry_date_end'}
    </div>

    {if isset($bIsHomepage) && !$bIsHomepage}
        {if $aCoupon.have_menu}
        <div class="yncoupon-cms">
            <div class="dropdown">
                <a role="button" data-toggle="dropdown" class="btn">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='coupon.block.link'}
                </ul>
            </div>
        </div>
        {/if}
    {/if}
</div>