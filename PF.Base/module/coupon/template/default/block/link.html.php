<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if $sView == "my" || $bIsProfile || $bIsInPages}

    {if $aCoupon.can_edit}
        {if !$aCoupon.is_closed || !$aCoupon.is_removed}
            {if $aCoupon.module_id == 'pages'}
                <li><a href="{url link="coupon.add" id=""$aCoupon.coupon_id"" module="pages" item=""$aCoupon.item_id""}">{phrase var='edit'}</a></li>
            {else}
                <li><a href="{url link="coupon.add" id=""$aCoupon.coupon_id""}">{phrase var='edit'}</a></li>
            {/if}
        {/if}
    {/if}

    {if $aCoupon.can_delete}
        {if isset($bcouponView) && $bcouponView == true}
            <li class="item_delete"><a href="{url link='coupon' delete=$aCoupon.coupon_id}" class="sJsConfirm">{phrase var='delete'}</a></li>
        {else}
            <li class="item_delete">
                <a title="{phrase var='delete'}"
                   onclick="$Core.jsConfirm({l}message: '{phrase var='are_you_sure_you_want_to_delete_this_coupon' phpfox_squote=true}'{r},function(){l}$.ajaxCall('coupon.inlineDelete', 'item_id={$aCoupon.coupon_id}');{r});"
                   href="javascript:void(0)"> {phrase var='delete'} </a>
            </li>
        {/if}
    {/if}

    {if $aCoupon.can_pause}
        <li class="item_pause">
            <a title="{phrase var='pause'}"
               onclick="$Core.jsConfirm({l}message: '{phrase var='are_you_sure_you_want_to_pause_this_coupon' phpfox_squote=true}'{r},function(){l}$.ajaxCall('coupon.pauseOwnCoupon', 'item_id={$aCoupon.coupon_id}');{r});"
               href="javascript:void(0)"> {phrase var='pause'}
            </a>
        </li>
    {/if}

    {if $aCoupon.can_resume}
        <li class="item_resume">
            <a title="{phrase var='resume'}"
               onclick="$Core.jsConfirm({l}message: '{phrase var='are_you_sure_you_want_to_resume_this_coupon' phpfox_squote=true}'{r},function(){l}$.ajaxCall('coupon.resumeOwnCoupon', 'item_id={$aCoupon.coupon_id}');{r});"
               href="javascript:void(0)"> {phrase var='resume'} </a>
        </li>
    {/if}
    
    {if $aCoupon.can_close}
        <li class="item_close">
            <a title="Close"
               onclick="$Core.jsConfirm({l}message: '{phrase var='are_you_sure_you_want_to_close_this_coupon' phpfox_squote=true}'{r},function(){l}$.ajaxCall('coupon.closeOwnCoupon', 'item_id={$aCoupon.coupon_id}');{r});"
               href="javascript:void(0)"> {phrase var='close'} </a>
        </li>
    {/if}

    {if $aCoupon.status != 3}
        <li id="js_coupon_publish_{$aCoupon.coupon_id}">
            {if $aCoupon.status == 7}
            <a title="{phrase var='publish_this_coupon'}"
               onclick="$Core.jsConfirm({l}message: '{phrase var='confirm_publish_coupon' total_fee=$aCoupon.total_fee symbol_currency_fee=$aCoupon.symbol_currency_fee }'{r},function(){l}$.ajaxCall('coupon.publish', '&iCouponId={$aCoupon.coupon_id}&amp;iCouponStatus={$aCoupon.status}', 'GET');{r});"
               href="javascript:void(0)"> {phrase var='publish'} </a>
            {elseif $aCoupon.status == 8}
            <a title="{phrase var='publish_this_coupon'}" href="javascript:void(0)"
               onclick="$Core.jsConfirm({l}message: '{phrase var='confirm_publish_again'}'{r},function(){l}$.ajaxCall('coupon.publish', '&iCouponId={$aCoupon.coupon_id}&amp;iCouponStatus={$aCoupon.status}', 'GET');{r})">
                {phrase var='publish'} </a>
            {/if}
        </li>
    {/if}

{/if}

{if Phpfox::getUserParam('coupon.can_feature_coupon') && !$aCoupon.is_closed}
    <li id="js_coupon_feature_{$aCoupon.coupon_id}" {if $aCoupon.is_featured} style="display: none;" {/if}>
        <a href="#" title="{phrase var='feature_this_coupon'}" onclick="$.ajaxCall('coupon.feature', '&iCouponId={$aCoupon.coupon_id}&amp;iFeatured=1', 'GET'); return false;">{phrase var='feature'}</a>
    </li>
    <li id="js_coupon_unfeature_{$aCoupon.coupon_id}" {if !$aCoupon.is_featured} style="display: none;" {/if}>
        <a href="#" title="{phrase var='unfeature_this_coupon'}" onclick="$.ajaxCall('coupon.feature', '&iCouponId={$aCoupon.coupon_id}&amp;iFeatured=0', 'GET'); return false;">{phrase var='unfeature'}</a>
    </li>
{elseif !$aCoupon.is_featured && !$aCoupon.is_closed}
    <li id="js_coupon_feature_{$aCoupon.coupon_id}">
        <a href="#" title="{phrase var='feature_this_coupon'}" onclick="$.ajaxCall('coupon.payFeature', '&iCouponId={$aCoupon.coupon_id}', 'GET'); return false;">{phrase var='feature'}</a>
    </li>
{/if}



{if $sView == "pending" && Phpfox::getUserParam('coupon.can_approve_coupon')}
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

