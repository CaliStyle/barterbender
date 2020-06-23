{if count($aTags) > 0}
	<div class="yndirectory-block-taglist">
	{foreach from=$aTags item=aTag}
			<span class="yndirectory-tag-item" style="font-size:{$aTag.font}px;">
	        	<a href="{$aTag.link}">{$aTag.key}</a>
	        </span>
	{/foreach}
	</div>
{/if}