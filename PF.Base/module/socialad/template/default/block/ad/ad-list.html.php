{literal}
	<style type="text/css">
		th a {
		    color: #3B5998;
		    outline: 0 none;
		    text-decoration: none;
		}

		th:hover a {
			color: #3B5998;
		}

		.link_menu li a, .sub_menu_bar .dropContent li a, .link_menu .div_menu{
			width: 96%;
		}
	</style>
{/literal}
{if $aSaAds}
<div id="js_ynsa_ad_list" style="overflow-x: auto" {if $isAdmin}class="panel"{/if}>

<table class="ynsaTable ynsaLFloat {if $isAdmin}table table-bordered{/if}" cellpadding="0" cellspacing="0">
	<tr>
		<th class="first"></th>
		<th class="second">{phrase var='name'}</th>

		<th class="{if !$isAdmin}tipright{else}tipright2{/if}">{phrase var='status'}
			{module name='socialad.tooltip' sTooltipName='ad_status'}
		</th>

		<th>{phrase var='campaign'}</th>
		<th class="ynsaClickable jsYnsaSortingClick {if !$isAdmin}tipright{else}tipright2{/if}" data-field-name="ad_start_time" data-action-type="{$aSaOrders.ad_start_time}">{phrase var='start_date'}
			{module name='socialad.tooltip' sTooltipName='ad_start_date'}
		{if $sOrderingField == 'ad_start_time'}
			<span class="ynsaArrow{$sOrderingType}"> </span>
		{/if}	
		</th>

		<th class="{if !$isAdmin}tipright{else}tipright2{/if}">{phrase var='end_date'}
			{module name='socialad.tooltip' sTooltipName='ad_end_date'}
		</th>

		{if !$isAdmin}
			<th class="ynsaClickable jsYnsaSortingClick tipright" data-field-name="ad_total_click" data-action-type="{$aSaOrders.ad_total_click}">{phrase var='clicks'}
			{module name='socialad.tooltip' sTooltipName='click'}
			{if $sOrderingField == 'ad_total_click'}
				<span class="ynsaArrow{$sOrderingType}"> </span>
			{/if}	
			</th>

			<th class="ynsaClickable jsYnsaSortingClick tipmiddle" data-field-name="ad_total_impression" data-action-type="{$aSaOrders.ad_total_impression}">{phrase var='impressions'}
				{module name='socialad.tooltip' sTooltipName='impression'}
			{if $sOrderingField == 'ad_total_impression'}
				<span class="ynsaArrow{$sOrderingType}"> </span>
			{/if}	
			</th>

			<th class="ynsaClickable jsYnsaSortingClick tipleft" data-field-name="ad_total_unique_click" data-action-type="{$aSaOrders.ad_total_unique_click}">{phrase var='unique_clicks'}
				{module name='socialad.tooltip' sTooltipName='unique_click'}
			{if $sOrderingField == 'ad_total_unique_click'}
				<span class="ynsaArrow{$sOrderingType}"> </span>
			{/if}	
			</th>

			<th class="ynsaClickable jsYnsaSortingClick tipleft" data-field-name="ad_total_reach" data-action-type="{$aSaOrders.ad_total_reach}">{phrase var='reaches'}
				{module name='socialad.tooltip' sTooltipName='reach'}
			{if $sOrderingField == 'ad_total_reach'}
				<span class="ynsaArrow{$sOrderingType}"> </span>
			{/if}	
			</th>

			{*
			<th class="ynsaClickable jsYnsaSortingClick tipleft" data-field-name="ad_total_running_day" data-action-type="{$aSaOrders.ad_total_running_day}">{phrase var='days'}
				{module name='socialad.tooltip' sTooltipName='running_day'}
			{if $sOrderingField == 'ad_total_running_day'}
				<span class="ynsaArrow{$sOrderingType}"> </span>
			{/if}	
			</th>
			*}

			<th class="tipleft">{phrase var='remaining'}
				{module name='socialad.tooltip' sTooltipName='remaining'}
			</th>
		{/if}

		<th class="{if !$isAdmin}tipleft{else}tipleft2{/if}">{phrase var='type'}
			{module name='socialad.tooltip' sTooltipName='ad_type'}
		</th>
	</tr>
{foreach from=$aSaAds name=ads item=aSaAd}
	<tr{if is_int($phpfox.iteration.ads/2)} class="on"{/if}>
		<td class="t_center ynsaFirstColumn">
			{if $aActionAd = $aSaAd} {/if}
			{template file='socialad.block.ad.action'}	
		</td>	
		<td title="{phrase var='name'}" class="t_center ">
			<a href="{url link='socialad.ad.detail' id=$aSaAd.ad_id}" >{$aSaAd.ad_title|clean|shorten:100:'...'}</a>
		</td>

		<td title="{phrase var='status'}" class="t_center">{$aSaAd.ad_status_phrase}</td>

		<td title="{phrase var='campaign'}" class="t_center">
			{$aSaAd.campaign_name|clean|shorten:20:'...'}
		</td>
		<td title="{phrase var='start_date'}" class="t_center">{$aSaAd.ad_start_time_phrase}</td>
		<td title="{phrase var='end_date'}" class="t_center">{$aSaAd.ad_end_time_phrase}</td>

		{if !$isAdmin}
			<td title="{phrase var='clicks'}" class="t_center">{$aSaAd.ad_total_click}</td>
			<td title="{phrase var='impressions'}" class="t_center">{$aSaAd.ad_total_impression}</td>
			<td title="{phrase var='unique_clicks'}" class="t_center">{$aSaAd.ad_total_unique_click}</td>
			<td title="{phrase var='reaches'}" class="t_center">{$aSaAd.ad_total_reach}</td>
			{*
			<td title="{phrase var='days'}" class="t_center">{$aSaAd.ad_total_running_day}</td>
			*}
			<td title="{phrase var='remaining'}" class="t_center">{$aSaAd.ad_remaining_phrase}</td>
		{/if}
		
		<td title="{phrase var='type'}" class="t_center ynsaLastColumn">{$aSaAd.ad_type_phrase}</td>
	</tr>
{/foreach}
</table>
</div>
{else}
<div class="extra_info" style="text-algin:center">
	{phrase var='no_ads_found'}
</div>
{/if}
<div class="clear"></div>
	{module name='socialad.paging'}

