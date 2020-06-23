<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 9:28 AM
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<ul class="action" id="ynsocialstore_category_menus">
    {if !empty($aCategories)}
        {foreach from=$aCategories item=aCategory}
            <li class="ynstore-main-category-item {if $aCategory.category_id == $iCurrentCategoryId}selected{/if}">
                {if (!$bIsStoreDetail)}
                <a href="{permalink module=$sUrl id=$aCategory.category_id title=$aCategory.title}">
                {else}
                <a href="{permalink module='ynsocialstore.store' id=$iStoreId title=$sStoreName}products/category_{$aCategory.category_id}">
                {/if}

                    {if isset($aCategory.url_photo) && $aCategory.url_photo}
                        <img src="{$aCategory.url_photo}" height="16">
                    {elseif isset($aCategory.class_category_item)}
                        <span class="category_item_{$aCategory.class_category_item}"></span>
                    {/if}
                    {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                    {else}
                        {assign var='value_name' value=$aCategory.title|convert}
                    {/if}
                    <span title="{$value_name}" class="">{$value_name}</span>
                    <span class="toggle fa fa-chevron-right"></span>
                </a>
                {if isset($aCategory.sub_category) && !empty($aCategory.sub_category)}
                <div style="display: none;" class="ynsocialstore_sub_category_items">
                    <ul>
                        <?php
                        $sub1Limit = 4;
                        $sub1Count = 0;
                        ?>
                        {foreach from=$aCategory.sub_category item=aSubCategory}
                        <?php $sub1Count++;
                        if($sub1Count <= $sub1Limit) :?>
                            <li class="main_sub_category_item {if $aSubCategory.category_id == $iCurrentCategoryId} active {/if}">
                                {if (!$bIsStoreDetail)}
                                <a href="{permalink module=$sUrl id=$aSubCategory.category_id title=$aSubCategory.title}">
                                {else}
                                <a href="{permalink module='ynsocialstore.store' id=$iStoreId title=$sStoreName}products/category_{$aSubCategory.category_id}">
                                {/if}
                                    {if Phpfox::isPhrase($this->_aVars['aSubCategory']['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aSubCategory.title|convert}
                                    {/if}
                                    <span title="{$value_name}" class="ynmenu-text have-child">
                                        {if $aSubCategory.url_photo}
                                        <img src="{$aSubCategory.url_photo}" width="16" height="16">
                                        {/if}
                                        {$value_name}
                                    </span>
                                </a>
                                {if isset($aSubCategory.sub_category) && $aSubCategory.sub_category}
                                <ul class="ynsocialstore_sub_sub_category_items">
                                    {foreach from=$aSubCategory.sub_category item=aSubSubCategory}
                                        <li {if $aSubSubCategory.category_id == $iCurrentCategoryId} class="active" {/if}>
                                    {if (!$bIsStoreDetail)}
                                    <a href="{permalink module=$sUrl id=$aSubSubCategory.category_id title=$aSubSubCategory.title}">
                                        {else}
                                    <a href="{permalink module='ynsocialstore.store' id=$iStoreId title=$sStoreName}products/category_{$aSubSubCategory.category_id}">
                                    {/if}
                                        {if Phpfox::isPhrase($this->_aVars['aSubSubCategory']['title'])}
                                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubSubCategory']['title']) ?>
                                        {else}
                                            {assign var='value_name' value=$aSubSubCategory.title|convert}
                                        {/if}
                                        <span title="{$value_name}" class="ynmenu-text have-child">
                                            {if $aSubSubCategory.url_photo}
                                            <img src="{$aSubSubCategory.url_photo}" width="16" height="16">
                                            {/if}
                                            {$value_name}
                                        </span>
                                    </a>
                                    </li>
                                    {/foreach}
                                </ul>
                                {/if}
                            </li>
                        <?php endif;?>
                        {/foreach}
                    </ul>
                    <div class="view_all_categories">
                        <a href="{url link='ynsocialstore.categories'}type_{$sType}{if $iStoreId}/storeid_{$iStoreId}{/if}">
                            {_p var='ynsocialstore.view_all_categories'}
                        </a>
                    </div>
                </div>

                {/if}
            </li>
        {/foreach}
        {literal}
        <script>
            $Behavior.initStoreCategoriesMenu = function(){
                $('#ynsocialstore_category_menus > li.ynstore-main-category-item').hover(
                    function(){
                        $(this).addClass('active');
                        $(this).children('.ynsocialstore_sub_category_items').stop(true,true).fadeIn();
                        $(this).closest('.layout-left').css("z-index","2");
                    },
                    function () {
                        $(this).removeClass('active');
                        $(this).children('.ynsocialstore_sub_category_items').stop(true,true).fadeOut();
                        $(this).closest('.layout-left').css("z-index","");
                    });
            }
        </script>
        {/literal}
    {/if}
    <li class="ynstore-main-category-item all_category_item">
        <a href="{url link='ynsocialstore.categories'}type_{$sType}{if $iStoreId}/storeid_{$iStoreId}{/if}">
            <i class="ico ico-dottedmore"></i>
            <span class="">{_p var='ynsocialstore.all_categories'}</span>
        </a>
    </li>
</ul>