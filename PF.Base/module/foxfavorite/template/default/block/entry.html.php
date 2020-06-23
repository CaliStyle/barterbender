<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: entry.html.php 1298 2009-12-05 16:19:23Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style>
	.link_title
	{
		font-weight:bold;
	}
	.view_all
	{
		font-weight:bold;
		text-align:right;
		margin-right:2px;
	}
</style>
{/literal}
{if isset($aGroups.items)}
{foreach from=$aGroups.items name=favorites item=aFavorite}
<div class="rowfavorite">
	<div class="rowfavotive_image">
		{if !empty($aFavorite.image)}
			<a href="{$aFavorite.link}">{$aFavorite.image}</a>
			{else}
			{img user=$aFavorite suffix='_50' max_width=50 max_height=50}
		{/if}
	</div>

	<div style="rowfavotive_content">
		<a title="{$aFavorite.title}" href="{$aFavorite.link}">{$aFavorite.title|clean|shorten:55:'...'|split:20}</a>
		<div class="description">
		{if isset($aFavorite.extra_info)}
			{$aFavorite.extra_info}
		{else}
			{$aFavorite.time_stamp_phrase}
		{/if}
		</div>
	</div>	
	
	<div class="clear"></div>
</div>
{/foreach}

{if $sView}
{pager}
{/if}



{/if}