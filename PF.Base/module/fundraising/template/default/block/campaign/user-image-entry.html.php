<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

?>

{if (isset($aUser.is_guest) && $aUser.is_guest) || (isset($aUser.is_anonymous) && $aUser.is_anonymous)}
	<a onclick="return false;" href="javascript:;" {if isset($aUser.is_anonymous) && $aUser.is_anonymous} title="{phrase var='anonymous_upper'}" {else} title="{$aUser.donor_name}" {/if} class="no_image_user _size__32 _gender_ _first_st"><span>{$userName}</span></a>
{else}
	{img user=$aUser suffix='_50_square' max_width=32 max_height=32 title='$aUser.donor_name'}
{/if}