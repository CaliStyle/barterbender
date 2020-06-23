		<div class="ync-style style-3 {if isset($aCoupon.print_option) && $aCoupon.print_option.photo=='0'} no-photo{/if}">
		<h3>{$aCoupon.title}</h3>
		<div class="ync-3-left">
			{if isset($aCoupon.site_url)}
			<p{if isset($aCoupon.print_option) && $aCoupon.print_option.site_url=='0'} style="display: none;"{/if}>
				{$aCoupon.site_url}
			</p>
			{/if}
            <div class="ync-expire">
				{phrase var="expired_date"}: {if isset($aCoupon.expire_time)}{$aCoupon.expire_time|date:'coupon.coupon_view_time_stamp'}{else}{phrase var='never_expire'}{/if}
			</div>
            <p{if isset($aCoupon.print_option) && $aCoupon.print_option.location=='0'} style="display: none;"{/if}>
				{phrase var="location"}:&nbsp;{$aCoupon.location_venue}{if $aCoupon.city}, {$aCoupon.city}{/if}{if $aCoupon.country_iso}, {$aCoupon.country_iso|location}{/if}
			</p>
			<p{if isset($aCoupon.print_option) && $aCoupon.print_option.category=='0'} style="display: none;"{/if}>
				{phrase var="category"}: {if $aCoupon.category}{$aCoupon.category|convert|clean}{else}{phrase var="none"}{/if}
			</p>
			{if $aCoupon.discount_type=='percentage' || $aCoupon.discount_type=='price'}
			<div class="ync-price-off">
				{if isset($aCoupon.discount) }	
				<span class="number">{$aCoupon.discount}</span>
				{/if}
		        {if isset($aCoupon.special_price) }
				<span class = "number"> {$aCoupon.special_price}</span>
		        {/if}
				<div class="ync-text-align">
					<span class="text-off">{phrase var='off'}</span>
				</div>
			</div>
			{/if}
			<div class="ync_code">
				<span class="ync-code-cus" id ="coupon_code_display">{$aCoupon.code}</span>
			</div>
		</div>
		<div class="ync-3-right">
			<div class="ync-image">
				{img server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_400_square' width=140 height=140}
			</div>
		</div>
		<div class="clear"></div>
	</div>
