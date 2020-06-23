<div class="yndirectory-video-list">
	{if count($aVideos) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}

	<div class="item-container with-video video-listing">
	{foreach from=$aVideos item=aItem name=videos}
		{template file='v.block.entry'}
	{/foreach}
	</div>

	<div class="clear"></div>
	{module name='directory.paging'}	
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}