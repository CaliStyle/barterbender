<?php 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if isset($aCategories)}
	<ul class="action" id="auction_category_menus">
        {foreach from=$aCategories item=aCategory}
        <li class="main_category_item {if $aCategory.category_id == $iCurrentCategoryId} active {/if}">
            {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
            {else}
                {assign var='value_name' value=$aCategory.title|convert}
            {/if}
            <a href="{permalink module='auction.category' id=$aCategory.category_id title=$value_name}">
                {if isset($aCategory.url_photo) && $aCategory.url_photo}
                    <img src="{$aCategory.url_photo}" height="16">
                {elseif isset($aCategory.class_category_item)}
                    <span class="category_item_{$aCategory.class_category_item}"></span>
                {/if}
                <span class="">{$value_name}</span>
                <span class="toggle fa fa-chevron-right"></span>
            </a>
            {if $aCategory.sub_category}
                <div style="display: none;" class="auction_sub_category_items">
                    <ul>
                    	<?php
                    		$sub1Limit = 4;
							$sub1Count = 0;
						?>
                        {foreach from=$aCategory.sub_category item=aSubCategory}
                        	<?php $sub1Count++;
                        	if($sub1Count <= $sub1Limit) :?>
	                            <li class="main_sub_category_item {if $aSubCategory.category_id == $iCurrentCategoryId} active {/if}">
                                    {if Phpfox::isPhrase($this->_aVars['aSubCategory']['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aSubCategory.title|convert}
                                    {/if}
	                                <a href="{permalink module='auction.category' id=$aSubCategory.category_id title=$value_name}">
	                                    <span class="ynmenu-icon" style="background-image: url('{$aSubCategory.url_photo}');"></span>
	                                    <span class="ynmenu-text have-child">{$value_name}</span>
	                                </a>
	                                {if $aSubCategory.sub_category}
	                                    <ul class="auction_sub_sub_category_items">
	                                        {foreach from=$aSubCategory.sub_category item=aSubSubCategory}
                                                {if Phpfox::isPhrase($this->_aVars['aSubSubCategory']['title'])}
                                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubSubCategory']['title']) ?>
                                                {else}
                                                    {assign var='value_name' value=$aSubSubCategory.title|convert}
                                                {/if}
	                                            <li {if $aSubSubCategory.category_id == $iCurrentCategoryId} class="active" {/if}>
	                                                <a href="{permalink module='auction.category' id=$aSubSubCategory.category_id title=$value_name}">
	                                                    <span class="ynmenu-icon" style="background-image: url('{$aSubSubCategory.url_photo}');"></span>
	                                                    <span class="ynmenu-text have-child">{$value_name}</span>
	                                                </a>
	                                            </li>
	                                        {/foreach}
	                                    </ul>
	                                {/if}
	                            </li>
                            <?php endif;?>
                        {/foreach}
                    </ul>
                    <div class="view_all_categories"><a href="{url link='auction.categories'}">{phrase var='view_all_categories'}</a></div>
                </div>
                
            {/if}
        </li>
        {/foreach}
        <li class="main_category_item all_category_item">
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
		}
	</script>
	{/literal}

{/if}