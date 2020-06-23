<div class="ynsaSection" >
	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='ad_preview'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			{module name='socialad.ad.preview.preview' aPreviewAd=$aSaAd }
		</div>
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='campaign'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			{$aSaAd.campaign_name}
		</div>
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='package'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			{if $bNoCreateBtn = true} {/if}
			{template file='socialad.block.package.entry'}
		</div>
	</div>
</div>
<button  class="btn btn-info btn-sm" onclick="location.href='{url link='socialad.ad.add' id=$aSaAd.ad_id}'">{phrase var='edit_ad'}</button>
<button  class="btn btn-info btn-sm" onclick="location.href='{url link='socialad.ad.placeorder' id=$aSaAd.ad_id}'">{phrase var='place_order'}</button>
