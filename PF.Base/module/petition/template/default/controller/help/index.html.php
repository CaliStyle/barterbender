<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !count($aHelps) && $iPage <= 1}
    {phrase var='petition.no_helps_found'}
{elseif count($aHelps)}
<div class="help_list">
	
	<table>
	{foreach from=$aHelps item=aHelp name=help}
		<tr>
			<td class="help_img"><a href="{permalink module='petition.help' id=$aHelp.help_id title=$aHelp.title}">{img server_id=$aHelp.server_id path='core.url_pic' file=$aHelp.image_path suffix='_200' max_width='120' max_height='120' class='js_mp_fix_width'}</a></td>
			<td class="help_cont">
				<div class="pet_help_tit"><a href="{permalink module='petition.help' id=$aHelp.help_id title=$aHelp.title}" class="link">{$aHelp.title|clean|shorten:55:'...'|split:20}</a></div>
				<div class="pet_help_cont">{$aHelp.content_parsed}</div>
			</td>
		</tr>
	{/foreach}
	</table>
	<div class="clear"></div>
	{pager}
</div>	
{/if}
