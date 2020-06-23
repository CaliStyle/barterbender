<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !isset($bIsLoadingMore)}
<form id="js_video_related_page_form" method="post" action="#">
	<div><input type="hidden" name="page_number" value="1" id="js_video_related_page_number" /></div>
	<div><input type="hidden" name="video_id" value="{$aVideo.video_id}" /></div>
	<div><input type="hidden" name="video_title" value="{$aVideo.title|clean}" /></div>
</form>
{/if}
{foreach from=$aRelatedVideos name=minivideos item=aMiniVideo}
	{template file='videochannel.block.mini'}
{/foreach}
{if !isset($bIsLoadingMore)}
{/if}