{if count($aTags) > 0}
<div class="block">
	<div class="title">{phrase var='foxfeedspro.cloud_tags'}</div>
	<div style="padding:10px;text-align:center;">
	{foreach from=$aTags item=aTag}
			<span style="font-size:{$aTag.font}pt;">
	        	<a href="{$aTag.link}">{$aTag.key}</a>
	        </span>&nbsp;  
	{/foreach}
	</div>
</div>
{/if}