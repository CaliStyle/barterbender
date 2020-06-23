<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="ynfr-user">
	<a href="#">{img user=$aUser suffix='_50_square' max_width=34 max_height=34}</a>
	<div>
		<p><a href="#">{$aUser.full_name}</a></p>
		{if isset($aUser.amount) && $aUser.amount > 0}
			<p>{$aUser.amount_text}</p>
		{elseif isset($aUser.total_donate)}
			<p>{phrase var='donated_in_total_donate_campaigns' total_donate=$aUser.total_donate}</p>
		{elseif  isset($aUser.total_share)}
			<p>{$aUser.total_share} {if $aUser.total_share==1}{_p var='share_lower'}{else}{_p var = 'share_s_lower'}{/if}</p>
			
		{/if}
	</div>
</div>
