{if isset($aAnn.announcement_id)}
	<div id="more_ann_title">{$aAnn.announcement_title|parse}</div>
	<div id="more_ann_content">
		{if Phpfox::getParam('core.allow_html')}
			{$aAnn.announcement_content_parse|parse}
		{else}
			{$aAnn.announcement_content|parse}
		{/if}
	</div>
{else}
	{phrase var='no_found_item'}
{/if}