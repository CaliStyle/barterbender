<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ync-listing-container-mini ync-list-layout item-container yndirectory-block">
	{foreach from=$aMostRatedBusinesses item=aBusiness name=business}
		{template file='directory.block.business-items'}
	{/foreach}
</div>
