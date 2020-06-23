<div class="yndirectory-coupon-list">
	{if count($aCoupons) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}
	<div class="yndirectory-content-column3">
	{foreach from=$aCoupons  name=coupon item=aCoupon}
		<div class="yndirectory-coupon-item">
			<div class="yndirectory-item-image">
				<a  href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" title="{$aCoupon.title|clean}">
					{if $aCoupon.image_path}
					<span class="yndirectory-photo-span" style="background-image:url({img return_url=true server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_200'});">
					</span>
					{else}
					<span class="yndirectory-photo-span" style="background-image:url({$sDefaultLink});">
					</span>
					{/if}
		        </a>			

		        <div class="yndirectory-coupon-status">
					{if $aCoupon.is_draft != 1}
			            {if $aCoupon.status == $aCouponStatus.closed}
			            <div class="yndirectory-coupon-status-close">
			                {phrase var='coupon.closed'}
			            </div>
			            {elseif $aCoupon.status == $aCouponStatus.pause}
			            <div class="yndirectory-coupon-status-pause">
			                {phrase var='coupon.pause'}
			            </div>
			            {elseif $aCoupon.status == $aCouponStatus.upcoming}
			            <div class="yndirectory-coupon-status-upcomming">
			                {phrase var='coupon.upcoming'}
			            </div>
			            {elseif $aCoupon.status == $aCouponStatus.endingsoon}
			            <div class="yndirectory-coupon-status-endingsoon">
			                {phrase var='coupon.ending_soon'}
			            </div>
			            {elseif $aCoupon.is_approved != 1}
			            <div class="yndirectory-coupon-status-pending">
			                {phrase var='coupon.pending'}
			            </div>

			            {elseif $aCoupon.is_featured}
			            <div class="yndirectory-coupon-status-featured">
			            {phrase var='coupon.featured'}
			            </div>
			            {/if}

			        {/if}
	        	</div>
			</div>			

			<div >
				<div class="yndirectory-item-title"><a href="{permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}" id="js_coupon_edit_inner_title{$aCoupon.coupon_id}" class="link ajax_link">{$aCoupon.title|clean|shorten:25:'...'|split:10} {if $aCoupon.is_draft} <span class="ync coupon-entry draft-text"> &lt;{phrase var='coupon.draft'}&gt;</span> {/if}</a></div>
				<div class="yndirectory-item-info">
			        <div class="extra_info">
			            <p>{phrase var='coupon.created_by'} <a href="javascript:void(0)">{$aCoupon|user}</a></p>
			            <p>{phrase var='coupon.end'}: {$aCoupon.end_time|date:'coupon.coupon_view_time_stamp'}</p>
			            {if isset($aCoupon.discount) }<p>{phrase var='coupon.discount'} {$aCoupon.discount}</p> {/if}
			            {if isset($aCoupon.special_price) }<p>{phrase var='coupon.special_price'} : {$aCoupon.special_price}</p> {/if}
			        </div>					
				</div>
			</div>
		</div>
	{/foreach}
	</div>
	<div class="clear"></div>
	{module name='directory.paging'}	
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}