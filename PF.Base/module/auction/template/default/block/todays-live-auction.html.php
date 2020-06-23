<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aCategories)}
	<ul class="action" id="auction_category_menus">
        {foreach from=$aCategories item=aCategory}
        <li class="main_category_item">
            <span class="toggle"><i class="fa fa-chevron-right"></i></span>
            <a href="{permalink module='auction.category' id=$aCategory.category_id title=$aCategory.title}">
                <span class="" style="background-image: url('{img server_id=$aCategory.server_id path='core.url_pic' file=$aCategory.image_path suffix='_16' return_url=true}');"></span>
                <span class="">{$aCategory.title|clean}</span>
            </a>
            {if $aCategory.sub_category}
                <ul style="display: none;" class="auction_sub_category_items">
                    {foreach from=$aCategory.sub_category item=aSubCategory}
                        <li class="main_sub_category_item">
                            <span class="toggle"><i class="fa fa-chevron-right"></i></span>
                            <a href="{permalink module='auction.category' id=$aSubCategory.category_id title=$aSubCategory.title}">
                                <span class="ynmenu-icon" style="background-image: url('{img server_id=$aSubCategory.server_id path='core.url_pic' file=$aSubCategory.image_path suffix='_16' return_url=true}');"></span>
                                <span class="ynmenu-text have-child">{$aSubCategory.title|clean}</span>
                            </a>
                            {if $aSubCategory.sub_category}
                                <ul style="display: none;" class="auction_sub_sub_category_items">
                                    {foreach from=$aSubCategory.sub_category item=aSubSubCategory}
                                        <li>
                                            <span class="toggle"><i class="fa fa-chevron-right"></i></span>
                                            <a href="{permalink module='auction.category' id=$aSubSubCategory.category_id title=$aSubSubCategory.title}">
                                                <span class="ynmenu-icon" style="background-image: url('{img server_id=$aSubSubCategory.server_id path='core.url_pic' file=$aSubSubCategory.image_path suffix='_16' return_url=true}');"></span>
                                                <span class="ynmenu-text have-child">{$aSubSubCategory.title|clean}</span>
                                            </a>
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </li>
        {/foreach}
        <li class="main_category_item">
            <span class="toggle"><i class="fa fa-chevron-right"></i></span>
            <a href="{url link='auction.categories'}">
                <span class="">{phrase var='all_categories'}</span>
            </a>
        </li>
	</ul>

	{literal}
	<script>
		$Behavior.initAuctionCategoriesMenu = function(){
			$('#auction_category_menus > li.main_category_item').hover(
			function(){
				$(this).children('.auction_sub_category_items').show('fast');
			},
			function () {
				$(this).children('.auction_sub_category_items').hide('fast');
			});
			
			$('.auction_sub_category_items > li.main_sub_category_item').hover(
			function(){
				$(this).children('.auction_sub_sub_category_items').show('fast');
			},
			function () {
				$(this).children('.auction_sub_sub_category_items').hide('fast');
			});
		}
	</script>
	{/literal}

{/if}