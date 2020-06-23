<?php

defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ync-listing-container-mini ync-list-layout item-container yndirectory-block">
{if count($aBusinessYouMayLike)}
	{foreach from=$aBusinessYouMayLike item=aBusiness name=business}
		{template file='directory.block.business-items'}
	{/foreach}
{/if}
</div>
