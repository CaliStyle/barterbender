<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="extra_info">
	{phrase var='keyword_substitutions'}:
	<ul>
{foreach from=$aKeywordPlaceholder key=sKeyword item=sSubtitution}
	<li>{$sKeyword} => {$sSubtitution}</li>

{/foreach}
	</ul>
</div>