{if $aDisplayAds}
	{if $bIsContentOnly}
		{foreach from=$aDisplayAds item=aSaAd}
			<input type="hidden" class="ynsaAdId" data-adstype="{$aSaAd.ad_type}" value="{$aSaAd.ad_id}"/>
			{module name='socialad.ad.display' aSaAd=$aSaAd bIsDisplayForUser=true}
		{/foreach}

	{else}
		<div id="{$sDisplayDivId}">
			{foreach from=$aDisplayAds item=aSaAd}
				<input type="hidden" class="ynsaAdId" data-adstype="{$aSaAd.ad_type}" value="{$aSaAd.ad_id}"/>
				{module name='socialad.ad.display' aSaAd=$aSaAd bIsDisplayForUser=true}
			{/foreach}
		</div>
	{/if}
{/if}
