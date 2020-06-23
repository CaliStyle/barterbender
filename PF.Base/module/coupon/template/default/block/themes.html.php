<div class="list_carousel">
<ul class="yns-print-slider">
	<li print_style="1">
		<div class="ync-style style-1">
			<div class="ync-1-left">
				<div class="ync-price-off">
					<span class="number">20%</span>
					<span class="text-off">{phrase var='off'}</span>
				</div>
				<div class="ync-image print_option_photo">
					<img src="{$aCoupon.image_url}" width="100" height="100" />
				</div>
			</div>
			<div class="ync-1-right">
				<h3>{$aCoupon.title}</h3>
				{if isset($aCoupon.site_url)}
				<p class="print_option_site_url">
					{$aCoupon.site_url}
				</p>
				{/if}
				<p class="print_option_location">
					{phrase var="location"}:&nbsp;{$aCoupon.location_venue}{if $aCoupon.city}, {$aCoupon.city}{/if}{if $aCoupon.country_iso}, {$aCoupon.country_iso|location}{/if}
				</p>
				<div class="ync-expire">
					{phrase var="expired_date"}: {$aCoupon.expire_time|date:'coupon.coupon_view_time_stamp'}
				</div>
				<p class="print_option_category">
					{phrase var="category"}: {if $aCoupon.category}{$aCoupon.category|convert|clean}{else}{phrase var="none"}{/if}
				</p>
				<div class="ync_code_info">
					<span id ="coupon_code_display">{$aCoupon.code}</span>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</li>
	<li print_style="2">
		<div class="ync-style style-2">
		<div class="ync-2-left">
			<div class="ync-image print_option_photo">
				<img src="{$aCoupon.image_url}" width="100" height="100" />
			</div>
			<div class="ync_code">
				<span class="ync-code-cus" id ="coupon_code_display">{$aCoupon.code}</span>
			</div>
		</div>
		<div class="ync-2-right">
			<h3>{$aCoupon.title}</h3>
			{if isset($aCoupon.site_url)}
			<p class="print_option_site_url">
				{$aCoupon.site_url}
			</p>
			{/if}
			<div class="ync-price-off">
				<span class="number">20%</span>
				<div class="ync-text-align">
					<span class="text-off">{phrase var='off'}</span>
				</div>

			</div>
			<div class="ync-expire">
				{phrase var="expired_date"}: {$aCoupon.expire_time|date:'coupon.coupon_view_time_stamp'}
			</div>
			<p class="print_option_location">
				{phrase var="location"}:&nbsp;{$aCoupon.location_venue}{if $aCoupon.city}, {$aCoupon.city}{/if}{if $aCoupon.country_iso}, {$aCoupon.country_iso|location}{/if}
			</p>
            <p class="print_option_category">
				{phrase var="category"}: {if $aCoupon.category}{$aCoupon.category|convert|clean}{else}{phrase var="none"}{/if}
			</p>
		</div>
		<div class="clear"></div>
	</div>
	</li>
	<li print_style="3">
		<div class="ync-style style-3">
		<h3>{$aCoupon.title}</h3>
		<div class="ync-3-left">
			{if isset($aCoupon.site_url)}
			<p class="print_option_site_url">
				{$aCoupon.site_url}
			</p>
			{/if}
            <div class="ync-expire">
				{phrase var="expired_date"}: {$aCoupon.expire_time|date:'coupon.coupon_view_time_stamp'}
			</div>
            <p class="print_option_location">
				{phrase var="location"}:&nbsp;{$aCoupon.location_venue}{if $aCoupon.city}, {$aCoupon.city}{/if}{if $aCoupon.country_iso}, {$aCoupon.country_iso|location}{/if}
			</p>
			<p class="print_option_category">
				{phrase var="category"}: {if $aCoupon.category}{$aCoupon.category|convert|clean}{else}{phrase var="none"}{/if}
			</p>
			<div class="ync-price-off">
				<span class="number">20%</span>
				<div class="ync-text-align">
					<span class="text-off">{phrase var='off'}</span>
				</div>

			</div>
			<div class="ync_code">
				<span class="ync-code-cus" id ="coupon_code_display">{$aCoupon.code}</span>
			</div>
		</div>
		<div class="ync-3-right">
			<div class="ync-image print_option_photo">
				<img src="{$aCoupon.image_url}" width="100" height="100" />
			</div>
		</div>
		<div class="clear"></div>
	</div>
	</li>
	<li print_style="4">
		<div class="ync-style style-4">
		<div class="style-4-border">
			<div class="ync-4-left">
				<div class="ync-image print_option_photo">
					<img src="{$aCoupon.image_url}" width="100" height="100" />
				</div>
			</div>
			<div class="ync-4-right">
				<h3>{$aCoupon.title}</h3>
				{if isset($aCoupon.site_url)}
				<p class="print_option_site_url">
					{$aCoupon.site_url}
				</p>
				{/if}
                <div class="ync-expire">
					{phrase var="expired_date"}: {$aCoupon.expire_time|date:'coupon.coupon_view_time_stamp'}
				</div>
				<p class="print_option_location">
					{phrase var="location"}:&nbsp;{$aCoupon.location_venue}{if $aCoupon.city}, {$aCoupon.city}{/if}{if $aCoupon.country_iso}, {$aCoupon.country_iso|location}{/if}
				</p>
				<p class="print_option_category">
					{phrase var="category"}: {if $aCoupon.category}{$aCoupon.category|convert|clean}{else}{phrase var="none"}{/if}
				</p>
				<div class="ync-price-off">
					<span class="number">20%</span>
					<div class="ync-text-align">
						<span class="text-off">{phrase var='off'}</span>
					</div>

				</div>
				<div class="ync_code">
					<span class="ync-code-cus" id ="coupon_code_display">{$aCoupon.code}</span>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	</li>
	<li print_style="5">
		<div class="ync-style style-5">
		
		<div class="ync-5-left">
			<h3>{$aCoupon.title}</h3>
			{if isset($aCoupon.site_url)}
			<p class="print_option_site_url">
				{$aCoupon.site_url}
			</p>
			{/if}
            <div class="ync-expire">
				{phrase var="expired_date"}: {$aCoupon.expire_time|date:'coupon.coupon_view_time_stamp'}
			</div>
			<p class="print_option_location">
				{phrase var="location"}:&nbsp;{$aCoupon.location_venue}{if $aCoupon.city}, {$aCoupon.city}{/if}{if $aCoupon.country_iso}, {$aCoupon.country_iso|location}{/if}
			</p>
			<p class="print_option_category">
				{phrase var="category"}: {if $aCoupon.category}{$aCoupon.category|convert|clean}{else}{phrase var="none"}{/if}
			</p>
			<div class="ync-price-off">
			<span class="number">20%</span>
			<div class="ync-text-align">
				<span class="text-off">{phrase var='off'}</span>
			</div>
		</div>
			<div class="ync_code">
				<span class="ync-code-cus" id ="coupon_code_display">{$aCoupon.code}</span>
			</div>
		</div>
		<div class="ync-5-right">
			<div class="ync-image print_option_photo">
				<img src="{$aCoupon.image_url}" width="80" height="80" />
			</div>
		</div>
		<div class="clear"></div>
	</div>
	</li>
	
    {if count($aTemplates)}
    {foreach from=$aTemplates item=aTemplate}
    <li print_style="custom_{$aTemplate.template_id}">
        {$aTemplate.html}
    </li>
    {/foreach}
    {/if}
</ul>
<a id="ync-prev3" class="prev" href="#">&nbsp;</a>
<a id="ync-next3" class="next" href="#">&nbsp;</a>
<div class="clear"></div>
</div>

<div>
    <ul>
        <li><label><input type="checkbox" id="checkbox_option_photo" /> {phrase var='show_photo'}</label></li>
        <li><label><input type="checkbox" id="checkbox_option_site_url" /> {phrase var='show_site_url'}</label></li>
        <li><label><input type="checkbox" id="checkbox_option_location" /> {phrase var='show_location'}</label></li>
        <li><label><input type="checkbox" id="checkbox_option_category" /> {phrase var='show_category'}</label></li>
    </ul>
</div>
{literal}
<script type="text/javascript">
    $Behavior.themes = function() {
        yncInitPrintSlide();
    }
</script>
{/literal}
