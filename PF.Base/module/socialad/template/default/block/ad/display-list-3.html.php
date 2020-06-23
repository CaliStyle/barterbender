
{if $aHtmlAds}
    <div id="js_ynsa_display_html_ad" style="margin-bottom: 10px;">
        <div class="block">
            <div class="title"> {phrase var='sponsored'}
                <span style="float: right; margin-right: 10px; font-weight: lighter; font-size: 10px;">
                    <a href="{url link='socialad.ad.add'}" > {phrase var='create_an_ad'}</a>
                 </span>
            </div>
        </div>
        {foreach from=$aHtmlAds item=aSaAd}
            <input type="hidden" class="ynsaAdId" data-adstype="{$aSaAd.ad_type}" value="{$aSaAd.ad_id}"/>
            {module name='socialad.ad.display' aSaAd=$aSaAd bIsDisplayForUser=true}
        {/foreach}
    </div>
    {if $iAjaxRefreshTime > 0}
        <script>
        $Behavior.ynsaRefreshAds = function() {l}
            setTimeout(function() {l}
                $.ajaxCall('socialad.refreshAds', 'div_id=js_ynsa_display_html_ad&block_id=3&module_id={$sYnsaModuleId}');
            {r}, {$iAjaxRefreshTime});
        {r}
        </script>
    {/if}
{/if}


{if $aBannerAds}
    <div id="js_ynsa_display_ad">
        {foreach from=$aBannerAds item=aSaAd}
            <input type="hidden" class="ynsaAdId" data-adstype="{$aSaAd.ad_type}" value="{$aSaAd.ad_id}"/>
            {module name='socialad.ad.display' aSaAd=$aSaAd bIsDisplayForUser=true}
        {/foreach}
    </div>
{/if}

