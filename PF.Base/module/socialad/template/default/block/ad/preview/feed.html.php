
{template file='feed.block.entry'}

{if isset($aPreviewAd)}
<script type="text/javascript"> 
$Behavior.ynsaDisablePreviewFeed = function() {l}
		ynsocialad.addForm.preview.disablePreviewSide();
	{r}
</script>
{/if}
