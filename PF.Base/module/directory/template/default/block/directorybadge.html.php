<div style="text-align: center;">
	<div>
		<a target="_blank" title="{$aBusiness.name|clean}" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}">
			{if $aBusiness.logo_path}
				{img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_200'}
			{else}
				<img src="{$aBusiness.no_image}">
			{/if}
		</a>
	</div>
	<div>
		<a target="_blank" title="{$aBusiness.name|clean}" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}">{$aBusiness.name|clean|shorten:50:'...'}</a>
		<div>{$aBusiness.short_description|clean|shorten:100:'...'}</div>
	</div>
</div>
{literal}
	<script type="text/javascript">
		$Behavior.globalInit();
	</script>
{/literal}
