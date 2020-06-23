{if $aSelectAds}
<div class="table form-group">
	<div class="table_left">
		{phrase var='ad'}:
	</div>
	<div class="table_right">			
		<select class="form-control ynsaMultipleChosen" name="val[ad_id]" id="js_ynsa_report_select_ad">
				<option value="0" >{phrase var='all_ads'}</option>
			{foreach from=$aSelectAds item=aAd} 
				<option {if $aAd.ad_id == $iDefaultAdId} selected="selected" {/if} value="{$aAd.ad_id}" >{$aAd.ad_title|clean}</option>
			{/foreach}
		</select>	
	</div>
</div>

{else}
	<div class="extra_info">
	{phrase var='no_ad_found'}
	</div>
{/if}
