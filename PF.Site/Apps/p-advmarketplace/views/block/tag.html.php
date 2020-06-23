<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_AdvMarketplace
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($aTags.tag_list) && !empty($aTags.tag_list)}
     <span>{foreach from=$aTags.tag_list item=aTag name=tag}{if $phpfox.iteration.tag != 1}, {/if}<a href="{$aTag.tag_url}">{$aTag.tag_text|clean|shorten:55:'...'|split:20}</a>{/foreach}</span>
    {unset var=$sTags}
{else}
	<div class="extra_info">
		{phrase var='tag.no_tags_have_been_found'}
	</div>
{/if}