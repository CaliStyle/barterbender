<?php

defined('PHPFOX') or exit('NO DICE!');

?>
	{if is_array($aTopFavPages) && count($aTopFavPages)}
		<div class="global_apps_title_padding">
			<ul class="ynfavo_list_items">
			{foreach from=$aTopFavPages item=aTopFavPage}
				<li class="ynfavo_list_item_tfp clearfix"><a href="{$aTopFavPage.link}" title="{$aTopFavPage.title}">
					{img 
        					 
							title=$aTopFavPage.title 
							path='pages.url_image' 
							file=$aTopFavPage.image_path 
							suffix='_50' 
							max_width='50' 
							max_height='50' 
							is_page_image=true}
					
				 
				<span class="ynfavo_title_tfp">{$aTopFavPage.title|clean|shorten:22:'...'} ({$aTopFavPage.total})</span> 
				</a></li>
			{/foreach}
			</ul>
		</div>
		<div class="clear"></div>
	{/if}
	