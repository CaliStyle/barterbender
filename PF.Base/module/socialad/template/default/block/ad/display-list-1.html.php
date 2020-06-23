
{if $aBannerAds}
<div id="js_ynsa_display_ad">
    {foreach from=$aBannerAds item=aSaAd}
        <input type="hidden" class="ynsaAdId" data-adstype="{$aSaAd.ad_type}" value="{$aSaAd.ad_id}"/>
        {module name='socialad.ad.display' aSaAd=$aSaAd bIsDisplayForUser=true}
    {/foreach}
</div>
{/if}
