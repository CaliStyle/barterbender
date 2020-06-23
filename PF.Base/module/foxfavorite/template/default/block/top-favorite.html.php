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
			<a class="link_title" href="{$aFavorite.link}">{$aFavorite.title|clean}</a>
			<span class="ynfavo_name_type">
				{$aFavorite.type}
				<strong style="position:relative; top:3px">&middot;</strong>
				<span class="description">
				{if isset($aFavorite.extra_info)}
					{$aFavorite.extra_info}
				{/if}
				<strong>{$aFavorite.total} <i class="fa fa-star"></i></strong>
				</span>
			</span>

		</span>	
	</li>
{/foreach}
</ul>