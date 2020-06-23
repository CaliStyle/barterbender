
<div id="js_ynsa_preview_holder">
	{if $sAdTypeName == 'html'}
	<div id="js_ynsa_preview_html">
		{module name='socialad.ad.display'}
	</div>
	{/if}

	{if $sAdTypeName == 'banner'}
	<div id="js_ynsa_preview_banner">
			{template file='socialad.block.ad.preview.banner'}
		<div id="js_ynsa_preview_banner_image_holder" >
			<img class="ynsaPreviewBannerImage" id="js_ynsa_display_ad_image"/>
		</div>
	</div>
	{/if}

	{if $sAdTypeName == 'feed'}
	<div id="js_ynsa_preview_feed">
		{template file='socialad.block.ad.preview.feed'}
	</div>
	{/if}
</div>

