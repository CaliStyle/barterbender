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
 
<!-- Coupon block items -->
<div class="ync-item-content">
	<!-- Image content -->
	<div class="ync-image-block-coupon">
        <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" title="{$aCoupon.title|clean}">
			<span style="background-image:url({if $aCoupon.image_path}{img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_400'}{else}{$sDefaultLink}{/if})">
			</span>
        </a>
    </div>
	<!-- Information content -->
	<div class="ync_title_info">
	    <div id="js_coupon_edit_title{$aCoupon.coupon_id}" class="ync-title">
	        <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="link ajax_link">{$aCoupon.title|clean|shorten:25:'...'|split:10}</a>
	    </div>
	    <div class="extra_info">
	        <p>{phrase var='created_by'} <a href="javascript:void(0)">{$aCoupon|user}</a></p>
	        <p>{phrase var='start'}: {$aCoupon.start_time|date:'coupon.coupon_view_time_stamp'}</p>
	        <p>{phrase var='end'}: {$aCoupon.end_time|date:'coupon.coupon_view_time_stamp'}</p>
	        <p>{$aCoupon.total_claim} {phrase var='claims'}</p>
	    </div>
	</div>
<div class="clear"></div>
</div>