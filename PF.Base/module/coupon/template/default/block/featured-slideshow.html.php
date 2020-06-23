<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script type="text/javascript">
$Behavior.yncouponsSlideInit = function() {
    var ync_owl = $("#ync_featured_slides");
    if(ync_owl.prop('built')) return;
    ync_owl.prop('built',true);
    ync_owl.addClass('dont-unbind-children');
    ync_owl.owlCarousel({
        navigation : true, // Show next and prev buttons
        slideSpeed : 300,
        paginationSpeed : 400,
        singleItem:true,
        autoPlay: true 
    });
}


</script>
{/literal}
<div id="ync_featured_slides">
    {foreach from=$aFeaturedCoupons item=aCoupon name=coupon}
    <div class="item ync-feature-item">
		<div class = "ync-bg-img">
            <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" title="{$aCoupon.title|clean}">
				<span style="background-image:url({if $aCoupon.image_path}{img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_400' max_width='240' max_height='160'}{else}{$sDefaultLink}{/if})">
				</span>

                {if isset($aCoupon.discount) }  
                    <div class="ync-discount-small">- {$aCoupon.discount}</div>
                {/if}
            </a>
        </div>
    

        <div class = "ync-feature-content">
            <div id="js_coupon_edit_title{$aCoupon.coupon_id}" class="ync-title">
                <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="link ajax_link">{$aCoupon.title|clean|shorten:40:'...'|split:20}</a>
            </div>

            <p>{phrase var='created_by'} <a href="javascript:void(0)">{$aCoupon|user}</a></p>

            <p>{phrase var='total_claims' total_claims=$aCoupon.total_claim } - {phrase var='total_likes' total_likes=$aCoupon.total_like } - {phrase var='total_rates' total_rates=$aCoupon.total_rating }</p>
            
            {if isset($aCoupon.special_price) }
                <div class="ync-discount">{phrase var='special_price'} <span>{$aCoupon.special_price}</span></div>
            {/if}

            {if isset($aCoupon.discount) }  
                <div class="ync-discount">{phrase var='discount'} <span>{$aCoupon.discount}</span></div>
            {/if}
            
            <div class="ync-feature-content-bottom">
                <div class="ync_end_time">
                    <span>{phrase var='end'}:</span> {$aCoupon.end_time|date:'coupon.coupon_view_time_stamp'}
                </div>

                <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="ync_getcode_btn link ajax_link">
                    {phrase var = 'coupon.get_code'}&nbsp;  <i class="fa fa-long-arrow-right"></i>  
                </a>
            </div>

        </div>
    </div>
    {/foreach}
</div>

<div class="clear"></div>