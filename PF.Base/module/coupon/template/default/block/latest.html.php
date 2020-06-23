<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


?>

{foreach from=$aLatestCoupons item=aCoupon name=coupon}
{template file='coupon.block.coupon-items'}
{/foreach}

<div class="clear"></div>
    {*

<div class="ync-image-coupon">
    <a href="{permalink module='coupon' id=$aCoupon.coupon_id title=$aCoupon.title}" title="{$aCoupon.title|clean}">
            <span style="background:url({img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_100'}) no-repeat top center;display:block;text-indent:-99999px;height:80px;width:125px;">

            </span>
    </a>
</div>

<div class="ync_title_info">
    <p id="js_coupon_edit_title{$aCoupon.coupon_id}" class="ync-title">
        <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="link ajax_link">{$aCoupon.title|clean|shorten:45:'...'|split:20} {if $aCoupon.is_draft} <span class="ync coupon-entry draft-text"> &lt;{phrase var='draft'}&gt;</span> {/if}</a>
    </p>
    <div class="extra_info">
        <p>{phrase var='created_by'} <a href="javascript:void(0)">{$aCoupon|user}</a></p>
        <p>{phrase var='posted_date'} {$aCoupon.start_time|date:'coupon.coupon_view_time_stamp'}</p>
        <p>{phrase var='expired_date'} {$aCoupon.expire}</p>
        {plugin call='coupon.template_block_entry_date_end'}
    </div>
</div>

    *}