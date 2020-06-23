
{if isset($aSaDisplayAd.ad_type_name) && $aSaDisplayAd.ad_type_name == 'banner'}
<div    {if isset($aSaDisplayAd.ad_id)}
        id="ynsaAdDisplay_{$aSaDisplayAd.ad_id}"
    {/if}>	
    <div class="ynsaDisplayAdBlock" >
    <a target="_blank" href="{$aSaDisplayAd.ad_click_url}" class="ynsaAdBannerClickZone">
	<img src="{$aSaDisplayAd.image_full_url}" style="width: 100%"/>

				{if isset($aSaDisplayAd.ad_id)}
					<div class="ynsaDisplayBannerAdHideButton" data-ad-id="{$aSaDisplayAd.ad_id}" ><span></span></div>
				{/if}

    </a>	
    </div>      
    {if isset($aSaDisplayAd.ad_id)}
            <div class="ynsaHiddenPermanently " style="display:none">
                <div class="ynsaTitle">
                    {phrase var='do_you_want_to_hide_the_ad_title_permanently' title=$aSaDisplayAd.ad_title_encode}
                </div>
                <div class="ynsaButton">
                    <button type="button" class="button btn btn-primary btn-sm" data-action="yes" data-ad-id="{$aSaDisplayAd.ad_id}">{phrase var='yes'}</button>
                    <button type="button" onclick="ynsaClickNoButtonBox({$aSaDisplayAd.ad_id}); return false;" class="button btn btn-warning btn-sm button_off" data-action="no" data-ad-id="{$aSaDisplayAd.ad_id}">{phrase var='no'}</button>
                </div>
            </div>
    {/if}
</div>
{else}
<!-- ad block display here -->
<div class="ynsaDisplayAdBlockWrapper ynsaDisplayUserBorder {if !$bIsDisplayForUser } ynsaBorder {/if}"
	{if isset($aSaDisplayAd.ad_id)}
		id="ynsaAdDisplay_{$aSaDisplayAd.ad_id}"
	{/if}
>
	<div class="ynsaDisplayAdBlock" >
		<div class="ynsaAdClickZone"
			{if isset($aSaDisplayAd.ad_click_url)} 
				onclick="window.open('{$aSaDisplayAd.ad_click_url}');"
			{/if}
		 >

			<div class="ynsaAdTitle" id="js_ynsa_display_ad_title"> 
				<span> {$aSaDisplayAd.ad_title|clean} </span>
				{if isset($aSaDisplayAd.ad_id)}
					{if Phpfox::getLib('module')->getFullControllerName() != 'socialad.payment/choosemethod'
						&& Phpfox::getLib('module')->getFullControllerName() != 'socialad.ad/detail'
					}
						<div class="ynsaDisplayAdHideButton" data-ad-id="{$aSaDisplayAd.ad_id}" ><span>x</span></div>
					{/if}
				{/if}
			</div>


			<div class="clear" > </div>
			{if isset($aSaDisplayAd.ad_item_type_name) && $aSaDisplayAd.ad_item_type_name == 'external_url'}
				<div class="ynsaAdExternalUrl" >{$aSaDisplayAd.ad_external_url|clean|shorten:60:'...'}</div>
				<div class="clear" > </div>
			{/if}
			<div class="ynsaAdImage" id="js_ynsa_display_ad_image_div" > <img id="js_ynsa_display_ad_image" src='{$aSaDisplayAd.image_full_url}'/> </div>
			<div class="ynsaAdText" id="js_ynsa_display_ad_text">{$aSaDisplayAd.ad_text|clean}</div>
			<div class="clear" > </div>
		</div>


		{if isset($aSaDisplayAd.ad_item_type)}
		<div class="ynsaLikeHolder" id="js_ynsa_action_holder_{$aSaDisplayAd.ad_item_type}_{$aSaDisplayAd.ad_item_id}" > 
			{module name='socialad.ad.action.action' iSaItemId=$aSaDisplayAd.ad_item_id iSaItemTypeId=$aSaDisplayAd.ad_item_type}
		</div>
		{else}
			<div class="ynsaLikeHolder" id="js_ynsa_action_holder" > </div> 
		{/if}
	</div>
		{if isset($aSaDisplayAd.ad_id)}
			<div class="ynsaHiddenPermanently " style="display:none">
				<div class="ynsaTitle">
					{phrase var='do_you_want_to_hide_the_ad_title_permanently' title=$aSaDisplayAd.ad_title_encode}
				</div>
				<div class="ynsaButton">
					<button type="button" class="button btn btn-primary btn-sm" data-action="yes" data-ad-id="{$aSaDisplayAd.ad_id}">{phrase var='yes'}</button>
					<button type="button" onclick="ynsaClickNoButtonBox({$aSaDisplayAd.ad_id}); return false;" class="button btn btn-warning btn-sm button_off" data-action="no" data-ad-id="{$aSaDisplayAd.ad_id}">{phrase var='no'}</button>
				</div>
			</div>
		{/if}

</div>

{/if}
