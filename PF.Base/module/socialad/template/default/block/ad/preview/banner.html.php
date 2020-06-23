

<div class="ynsaPreviewBannerSiteLayout"> 
	<div data-block-list="6,11"  class="ynsaPreviewBlock ynsaTop">  </div>
	<div data-block-list="1,9"  class="ynsaPreviewBlock ynsaLeft"></div>
	<div class="ynsaPreviewBlock ynsaMiddle ynsaNoBorder"> 
		<div data-block-list="7"  class="ynsaPreviewBlock ynsaTopMiddle"></div>
		<div data-block-list="2"class="ynsaPreviewBlock ynsaMiddleMiddle"></div>
		<div data-block-list="4"  class="ynsaPreviewBlock ynsaBottomMiddle"></div>
	</div>
	<div data-block-list="3,10"  class="ynsaPreviewBlock ynsaRight"></div>
	<div data-block-list="8,12,5"  class="ynsaPreviewBlock ynsaBottom"></div>

</div>

{if isset($aPreviewAd)}
<script type="text/javascript"> 
$Behavior.ynsaSetPreviewBannerBlock = function() {l}
		ynsocialad.review.setPreviewBannerBlock({$aPreviewAd.placement_block_id},'{$aPreviewAd.image_full_url}');
	{r}
</script>
{/if}
