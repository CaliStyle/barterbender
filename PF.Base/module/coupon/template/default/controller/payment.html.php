<div class="ynCoupon-order-detail">
	<div class="title">{$aCheckoutParams.item_name}</div>
	<div class="description coupon-detail">
		<div class="label">{phrase var='Coupon'}</div>
		<div class="value">
			<div id="js_coupon_entry{$aCoupon.coupon_id}"{if !isset($bCouponView)} class="js_coupon_parent image_hover_holder"{/if}>
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
			
					<a class="ync_coupon_img" href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" title="{$aCoupon.title|clean}">
						<span style="background-image:url({if $aCoupon.image_path}{img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix=''}{else}{$sDefaultLink}{/if})">
						</span>
			        </a>
			
			        <div class="ync_author">{phrase var='created_by'} <a href="javascript:void(0)">{$aCoupon|user}</a></div>
			    </div>
			
			    {if isset($bIsHomepage) && !$bIsHomepage}
			        {if $aCoupon.have_menu}
			            <a href="#" class="image_hover_menu_link">{phrase var='link'}</a>
			            <div class="image_hover_menu">
			                <ul>
			                    {template file='coupon.block.link'}
			                </ul>
			            </div>
			        {/if}
			    {/if}
			
			    <div class="ync_title_info clearfix">
			        <div id="js_coupon_edit_title{$aCoupon.coupon_id}" class="ync-title">
			            <a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="link ajax_link">{$aCoupon.title|clean|shorten:25:'...'|split:10} {if $aCoupon.is_draft} <span class="ync coupon-entry draft-text"> &lt;{phrase var='draft'}&gt;</span> {/if}</a>
			        </div>
			    
			        <p>{phrase var='end'}: {$aCoupon.end_time|date:'coupon.coupon_view_time_stamp'}</p>
			        
			
			        {if isset($aCoupon.discount) }
			            <span class="ync-discount-small">- {$aCoupon.discount}</span> 
			        {/if}
			
			        {if isset($aCoupon.special_price) }
			            <span class="ync-discount-small">{$aCoupon.special_price}</span> 
			        {/if}
			
			        {plugin call='coupon.template_block_entry_date_end'}
			    </div>
			</div>
		</div>
	</div>
	<div class="description price">
		<div style="display: inline;">{phrase var='price'}: </div>
		<div class="value">
			{$aCheckoutParams.amount} {$aCheckoutParams.currency_code}
		</div>
	</div>
</div>

{if $bNoPaymentMethodActive}
	<div>
        {phrase var='no_available_payment_methods'}
	</div>
{else}
	{module name='api.gateway.form'}
{/if}
