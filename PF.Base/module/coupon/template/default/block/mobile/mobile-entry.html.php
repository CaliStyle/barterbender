<div id="js_coupon_entry{$aCoupon.coupon_id}"{if !isset($bCouponView)} class="{if $aCoupon.is_approved != 1} {/if} ync-mobile-entry"{/if}> 
    <div class="ync-image-mobile-coupon">
        {if $aCoupon.is_draft != 1}
            {if $aCoupon.is_approved != 1}
            <div class="ync_pending_link">
                {phrase var='pending'}
            </div>
            {elseif $aCoupon.status == $aCouponStatus.closed}
            <div class="ync_close_link">
                {phrase var='closed'}
            </div>
            {elseif $aCoupon.status == $aCouponStatus.pause}
            <div class="ync_pause_link">
                {phrase var='pause'}
            </div>
            {elseif $aCoupon.status == $aCouponStatus.upcoming}
            <div class="ync_upcoming_link">
                {phrase var='upcoming'}
            </div>
            {elseif $aCoupon.status == $aCouponStatus.endingsoon}
            <div class="ync_ending_soon_link">
                {phrase var='ending_soon'}
            </div>

            {else}
            <div class="js_featured_coupon ync_featured_link"{if !$aCoupon.is_featured} style="display:none;"{/if}>
            {phrase var='featured'}
            </div>
            {/if}
        {/if}
        <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" title="{$aCoupon.title|clean}">
            <span style="background:url({img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_100'}) no-repeat top center;display:block;text-indent:-99999px;height:70px;width:95px;">
            
            </span>
        </a>
    </div>
    {if isset($bInHomepage) && !$bInHomepage}
        {if $aCoupon.having_action_button}
            <a href="#" class="image_hover_menu_link">{phrase var='link'}</a>
            <div class="image_hover_menu">
                <ul>
                    
                    {template file='coupon.block.link'}
                </ul>           
            </div>
        {/if}
    {/if}

    <div class="ync_title_info">
        <p id="js_coupon_edit_title{$aCoupon.coupon_id}" class="ync-title">
            <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="link ajax_link">{$aCoupon.title|clean|shorten:45:'...'|split:20} {if $aCoupon.is_draft} <span class="ync campaign-entry draft-text"> &lt;{phrase var='draft'}&gt;</span> {/if}</a>
        </p>
        <div class="extra_info">
            <p>{phrase var='created_by'} <a href="javascript:void(0)">{$aCoupon|user}</a></p>
            <p>{phrase var='end'}: {$aCoupon.end_time|date:'coupon.coupon_view_time_stamp'}</p>
            {if isset($aCoupon.discount) }<p>{phrase var='discount'} {$aCoupon.discount}</p> {/if}
            {if isset($aCoupon.special_price) }<p>{phrase var='special_price'} : {$aCoupon.special_price}</p> {/if}
            {plugin call='coupon.template_block_entry_date_end'}          
        </div>
    </div>
</div>
