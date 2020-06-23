<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="auction_categories_alfabet">
    <div class="alfabet_products">{phrase var='categories_by'}:</div>
    <div class="alfabet_products_list">
        <a href="javascript:;" class="active" onclick="showAllCategories(this);">{phrase var='all'}</a>
        {foreach from=$aAlfabet item=sChar}
            <a href="javascript:;" class="" onclick="filterSubSubCatgegories(this, '{$sChar}');">{$sChar}</a>
        {/foreach}
    </div>
</div>
<div class="auction_categories_content">
    {foreach from=$aControllerCategories item=aControllerCategory}
        <div class="auction_category">
            {if Phpfox::isPhrase($this->_aVars['aControllerCategory']['title'])}
                <?php $this->_aVars['value_name'] = _p($this->_aVars['aControllerCategory']['title']) ?>
            {else}
                {assign var='value_name' value=$aControllerCategory.title|convert}
            {/if}
            <div class="auction_category_header" data-sort="{$value_name}">
                <a href="{permalink module='auction.category' id=$aControllerCategory.category_id title=$value_name}">
                    {if $aControllerCategory.url_photo}
                        <span class="" style="background-image: url('{$aControllerCategory.url_photo}');"></span>
                    {else}
                        <span class="category_item_{$aControllerCategory.class_category_item}"></span>
                    {/if}
                    {$value_name}
                </a>
            </div>
            {if $aControllerCategory.sub_category}
                <div class="auction_subcategory_content">
                    {foreach from=$aControllerCategory.sub_category key=iKey item=aControllerSubCategory}
                        <div class="auction_subcategory_item {if $iKey >= $iLimitNumberOfCategories} subcategory_show_more {/if}" {if $iKey >= $iLimitNumberOfCategories} style="display: none;" {/if}>
                            {if Phpfox::isPhrase($this->_aVars['aControllerSubCategory']['title'])}
                                <?php $this->_aVars['value_name'] = _p($this->_aVars['aControllerSubCategory']['title']) ?>
                            {else}
                                {assign var='value_name' value=$aControllerSubCategory.title|convert}
                            {/if}
                            <a href="{permalink module='auction.category' id=$aControllerSubCategory.category_id title=$value_name}" class="filter_item">
                                <span class="{if $aControllerSubCategory.url_photo}yes{/if}" style="background-image: url('{$aControllerSubCategory.url_photo}');"></span>
                                {$value_name}
                            </a>
                            {if $aControllerSubCategory.sub_category}
                            <div class="control_icons_up_down">
                                <div class="auction_subsubcategory_up_icon"><a href="javascript:;" onclick="toggleSubSubCategories(this);">{img theme='misc/vote_up_off.gif'}</a></div>
                                <div class="auction_subsubcategory_down_icon"><a href="javascript:;" onclick="toggleSubSubCategories(this);">{img theme='misc/vote_down_off.gif'}</a></div>
                            </div>
                                <div class="auction_subsubcategory_holder">

                                    <div class="auction_subsubcategory_content">
                                        {foreach from=$aControllerSubCategory.sub_category item=aControllerSubSubCategory}
                                            <div class="auction_subsubcategory_item">
                                                {if Phpfox::isPhrase($this->_aVars['aControllerSubSubCategory']['title'])}
                                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aControllerSubSubCategory']['title']) ?>
                                                {else}
                                                    {assign var='value_name' value=$aControllerSubSubCategory.title|convert}
                                                {/if}
                                                <a href="{permalink module='auction.category' id=$aControllerSubSubCategory.category_id title=$value_name}">
                                                    <span class="" style="background-image: url('{$aControllerSubSubCategory.url_photo}');"></span>
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
							<div class="auction_subsubcategory_more_icon"><a href="javascript:;" onclick="toggleMoreLessCategories(this);">{img theme='layout/video_show_more.png'} {phrase var='more_dot_dot_dot'}</a></div>
							<div class="auction_subsubcategory_less_icon" style="display: none;"><a href="javascript:;" onclick="toggleMoreLessCategories(this);">{img theme='layout/video_show_less.png'} {phrase var='less'}</a></div>
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
        showAllCategories(e);
        
        $('.auction_category_header').each(function(){
            var oItem = $(this);
            if(oItem.attr("data-sort").trim().charAt(0).toUpperCase() != sChar)
            {
            	oItem.parent().hide('fast');
            }
        });
    }
    
    function showAllCategories(e)
    {
        var oItem = $(e);
        oItem.parent().find('a').removeClass('active');
        oItem.addClass('active');
        
         $('.auction_category_header').each(function(){
            var oItem = $(this);
        	oItem.parent().show('fast');
        });
    }
    
    function showAllSubSubCategories(e)
    {
        var oItem = $(e);
        oItem.parent().find('a').removeClass('active');
        oItem.addClass('active');
        
        $('.auction_subcategory_item').show('fast');
    }
    
    function toggleSubSubCategories(e)
    {
        var oItem = $(e);
        if(!oItem.parent().closest('.auction_subcategory_item ').hasClass('open')){
            $('.auction_subcategory_item').removeClass('open');            
        }
        oItem.parent().closest('.auction_subcategory_item ').toggleClass('open');
    
    }
	
	function toggleMoreLessCategories(e)
	{
		var oItem = $(e);
		oItem.parent().parent().parent().find('.subcategory_show_more').toggle('fast');
		
		oItem.parent().parent().find('.auction_subsubcategory_more_icon').toggle('fast');
        oItem.parent().parent().find('.auction_subsubcategory_less_icon').toggle('fast');
	}
</script>
{/literal}