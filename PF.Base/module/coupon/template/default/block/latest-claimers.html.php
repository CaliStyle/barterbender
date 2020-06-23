<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aClaimers item=aUser name=aUser}
	<div class="ync-user {if ($phpfox.iteration.aUser%3)==2}item-middle{/if}" style="float:left;">
		{img user=$aUser suffix='_50_square' max_width=32 max_height=32 class='js_hover_title' title='aaa'}	
	</div>
{/foreach}
<div class="clear"></div>