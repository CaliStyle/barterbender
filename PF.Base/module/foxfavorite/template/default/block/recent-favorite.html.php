<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		YouNetCo company
 * @author  		MinhNTK
 * @package  		Module FoxFavorite
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<ul class="ynfavo_list_items">
{foreach from=$aFavorites item=aFavorite}
	<li class="clearfix">
		<span class="ynfavo_image clearfix">
		{if !empty($aFavorite.image)}
			<a href="{$aFavorite.link}">{$aFavorite.image}</a>
			{else}
			{img user=$aFavorite suffix='_50_square' max_width=50 max_height=50}
		{/if}
		</span>

		<span class="ynfavo_title">
			<a title="{$aFavorite.title}" href="{$aFavorite.link}">{$aFavorite.title|clean|shorten:55:'...'|split:20}</a>
			
			<span class="description">
				{if isset($aFavorite.extra_info)}
					{$aFavorite.extra_info}
				{/if}
			</span>
		</span>
		<span class="ynfavo_name_type">{$aFavorite.type}</span>
	</li>
    
{/foreach}
</ul>
