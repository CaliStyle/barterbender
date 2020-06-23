<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{if empty($sCategoryUrl)}{url link=$sParentLink}{else}{url link=''$sParentLink'.category'$sCategoryUrl''}{/if}">
	{phrase var='videochannel.keywords'}:
	<div class="p_4">
		{filter key='keyword'}
	</div>	
	
	<div class="p_top_4">
		{phrase var='videochannel.sort'}:
		<div class="p_4">
			{filter key='sort'} {phrase var='videochannel.in_sorting_order'} {filter key='sort_by'}
		</div>	
	</div>	
	
	<div class="p_top_8">
		<input name="search[submit]" value="{phrase var='videochannel.submit'}" class="button btn-primary" type="submit" />
		<input name="search[reset]" value="{phrase var='videochannel.reset'}" class="button btn-primary" type="submit" />
	</div>	
</form>