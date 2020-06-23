<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/13/16
 * Time: 9:27 AM
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ynstore_categories_alfabet">
    <div class="alfabet_products">{_p var='ynsocialstore.categories_by'}:</div>
    <div class="alfabet_products_list">
        <a href="javascript:;" class="active" onclick="showAllCategories(this);">{_p var='ynsocialstore.all'}</a>
        {foreach from=$aAlfabet item=sChar}
        <a href="javascript:;" class="" onclick="filterSubSubCatgegories(this, '{$sChar}');">{$sChar}</a>
        {/foreach}
    </div>
</div>
<div class="extra_info" id="no_categories" style="display: none;">
    {_p var='ynsocialstore.there_are_no_categories'}
</div>
<div class="ynstore_categories_content">
    {foreach from=$aControllerCategories item=aControllerCategory}
    <div class="ynstore_category">
        {if Phpfox::isPhrase($this->_aVars['aControllerCategory']['title'])}
            <?php $this->_aVars['value_name'] = _p($this->_aVars['aControllerCategory']['title']) ?>
        {else}
            {assign var='value_name' value=$aControllerCategory.title|convert}
        {/if}
        <div class="ynstore_category_header" data-sort="{$value_name}">
            <a title="{$value_name}" href="{permalink module='ynsocialstore.'.$sType.'.category' id=$aControllerCategory.category_id title=$value_name}">
                {if $aControllerCategory.url_photo}
                    <span class="" style="background-image: url('{$aControllerCategory.url_photo}');"></span>
                {else}
                    <span class="category_item_{$aControllerCategory.class_category_item}"></span>
                {/if}
                {$value_name}
            </a>
        </div>
        {if $aControllerCategory.sub_category}
        <div class="ynstore_subcategory_content">
            {foreach from=$aControllerCategory.sub_category key=iKey item=aControllerSubCategory}
            <div class="ynstore_subcategory_item {if $iKey >= $iLimitNumberOfCategories} subcategory_show_more {/if}" {if $iKey >= $iLimitNumberOfCategories} style="display: none;" {/if}>
                {if Phpfox::isPhrase($this->_aVars['aControllerSubCategory']['title'])}
                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aControllerSubCategory']['title']) ?>
                {else}
                    {assign var='value_name' value=$aControllerSubCategory.title|convert}
                {/if}
                <a title="{$value_name}" href="{permalink module='ynsocialstore.'.$sType.'.category' id=$aControllerSubCategory.category_id title=$value_name}" class="filter_item">
                    <span class="{if $aControllerSubCategory.url_photo}yes{/if}" style="background-image: url('{$aControllerSubCategory.url_photo}');"></span>
                    {$value_name}
                </a>
                {if $aControllerSubCategory.sub_category}
                <div class="control_icons_up_down">
                    <div class="ynstore_subsubcategory_up_icon">
                        <a href="javascript:;" onclick="toggleSubSubCategories(this);">
                            <i class="ico ico-plus"></i>
                        </a>
                    </div>

                    <div class="ynstore_subsubcategory_down_icon">
                        <a href="javascript:;" onclick="toggleSubSubCategories(this);">
                            <i class="ico ico-minus"></i>
                        </a>
                    </div>
                </div>
                <div class="ynstore_subsubcategory_holder">

                    <div class="ynstore_subsubcategory_content">
                        {foreach from=$aControllerSubCategory.sub_category item=aControllerSubSubCategory}
                        <div class="ynstore_subsubcategory_item">
                            {if Phpfox::isPhrase($this->_aVars['aControllerSubSubCategory']['title'])}
                                <?php $this->_aVars['value_name'] = _p($this->_aVars['aControllerSubSubCategory']['title']) ?>
                            {else}
                                {assign var='value_name' value=$aControllerSubSubCategory.title|convert}
                            {/if}
                            <a title="{$value_name}" href="{permalink module='ynsocialstore.'.$sType.'.category' id=$aControllerSubSubCategory.category_id title=$value_name}">
                                <span class="{if $aControllerSubSubCategory.url_photo}yes{/if}" style="background-image: url('{$aControllerSubSubCategory.url_photo}');"></span>
                                {$value_name}
                            </a>
                        </div>
                        {/foreach}
                    </div>
                </div>
                {/if}
            </div>
            {/foreach}
            {if count($aControllerCategory.sub_category) >= $iLimitNumberOfCategories}
                <div class="control_icons_more_less">
                    <div class="ynstore_subsubcategory_more_icon">
                        <a href="javascript:;" onclick="toggleMoreLessCategories(this);">
                            <i class="ico ico-angle-double-down"></i>
                            {_p var='ynsocialstore.more_dot_dot_dot'}
                        </a>
                    </div>

                    <div class="ynstore_subsubcategory_less_icon" style="display: none;">
                        <a href="javascript:;" onclick="toggleMoreLessCategories(this);">
                            <i class="ico ico-angle-double-up"></i>
                           {_p var='ynsocialstore.less'}
                        </a>
                    </div>
                </div>
            {/if}
        </div>
        {/if}
    </div>
    {/foreach}
</div>
{literal}
<script>
    function filterSubSubCatgegories(e, sChar)
    {
        hideAllCategories(e);
        var has_categories = false;
        $('.ynstore_category_header').each(function(){
            var oItem = $(this);
            if(oItem.attr("data-sort").trim().charAt(0).toUpperCase() == sChar)
            {
                has_categories = true;
                oItem.parent().show('fast');
            }
        });
        if (has_categories) {
            $('#no_categories').hide();
        } else {
            $('#no_categories').show();
        }
    }
    
    function hideAllCategories(e) {
        var oItem = $(e);
        oItem.parent().find('a').removeClass('active');
        oItem.addClass('active');

        $('.ynstore_category_header').each(function(){
            var oItem = $(this);
            oItem.parent().hide('fast');
        });
    }

    function showAllCategories(e)
    {
        var oItem = $(e);
        oItem.parent().find('a').removeClass('active');
        oItem.addClass('active');

        $('.ynstore_category_header').each(function(){
            var oItem = $(this);
            oItem.parent().show('fast');
        });
        $('#no_categories').hide();
    }

    function showAllSubSubCategories(e)
    {
        var oItem = $(e);
        oItem.parent().find('a').removeClass('active');
        oItem.addClass('active');

        $('.ynstore_subcategory_item').show('fast');
    }

    function toggleSubSubCategories(e)
    {
        var oItem = $(e);
        if(!oItem.parent().closest('.ynstore_subcategory_item ').hasClass('open')){
            $('.ynstore_subcategory_item').removeClass('open');
        }
        oItem.parent().closest('.ynstore_subcategory_item ').toggleClass('open');

    }

    function toggleMoreLessCategories(e)
    {
        var oItem = $(e);
        oItem.parent().parent().parent().find('.subcategory_show_more').toggle();

        oItem.parent().parent().find('.ynstore_subsubcategory_more_icon').toggle();
        oItem.parent().parent().find('.ynstore_subsubcategory_less_icon').toggle();
    }
    {/literal}
    {if $sType == 'store'}
    {literal}
    $Behavior.onLoadAllCategories = function(){
        if($('#page_ynsocialstore_categories').length >0){
            $('.sub_section_menu ul > li.active').removeClass("active");
            $('.header_display ul > li.active').removeClass("active");
            $('.sub_section_menu ul > li:eq(1)').addClass("active");
            $('.header_display ul > li:eq(1)').addClass("active");
        }
    }
    {/literal}
    {/if}
    {literal}
</script>
{/literal}