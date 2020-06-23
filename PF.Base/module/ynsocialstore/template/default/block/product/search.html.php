<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/12/16
 * Time: 10:40 AM
 */
?>
<div class="yns adv-search-block" id ="ynsocialstore_adv_searchproduct" {if !$bIsAdvSearch }style="display:none;"{else}style="display:block;"{/if}>
    <form id="ynsocialstore_adv_search_product_form" onsubmit="checkOnSearchProductSubmit()" method="get" action="{url link='current' isAdvancedSearch=true}">
        <input type="hidden" id="flag_advancedsearchproduct" {if $bIsAdvSearch }value="1"{/if} name="flag_advancedsearchproduct"/>
        <input type="hidden" id="sort" name="sort" value="{if isset($aSearch.sort)}{$aSearch.sort}{/if}">
        <input type="hidden" id="when" name="when" value="{if isset($aSearch.when)}{$aSearch.when}{/if}">
        <input type="hidden" id="show" name="show" value="{if isset($aSearch.show)}{$aSearch.show}{/if}">
        <input type="hidden" id="view" name="view" value="{$sView}">
        <input type="hidden" id="keywords" name="keywords" value="">

        <div class="row">
            <div class="col-md-6 col-sm-12 ynstore-paddingright-5">
                <div class="form-group">
                    <div class="table_right">
                        <input type="text" placeholder="{_p var='ynsocialstore.price_from'}" name="search[price_from]" value="{if isset($aForms.price_from)}{$aForms.price_from}{/if}" class="form-control" id="price_from"/>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12 ynstore-paddingleft-5">
                <div class="form-group">
                    <div class="table_right">
                        <input type="text" placeholder="{_p var='ynsocialstore.price_to'}" name="search[price_to]" value="{if isset($aForms.price_to)}{$aForms.price_to}{/if}" class="form-control" id="price_to" />
                    </div>
                </div>
            </div>
        </div>

        {if empty($bIsSearchByCategory)}
        <div class="form-group">
            <div class="table_right">
                <select name="search[category_id]" class="form-control">
                    <option id="0" value="0">{_p var='ynsocialstore.categories'}</option>
                    {foreach from=$aCategories item=aCategory}
                        {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                        {else}
                            {assign var='value_name' value=$aCategory.title|convert}
                        {/if}
                        <option id="{$aCategory.category_id}" {if isset($aForms.category_id) && $aForms.category_id == $aCategory.category_id} selected="selected" {/if} value="{$aCategory.category_id}">{$value_name}</option>
                        {if !empty($aCategory.sub_category)}
                            {foreach from=$aCategory.sub_category item=aSubCategory}
                                {if Phpfox::isPhrase($this->_aVars['aSubCategory']['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aSubCategory.title|convert}
                                {/if}
                                <option id="{$aSubCategory.category_id}" {if isset($aForms.category_id) && $aForms.category_id == $aSubCategory.category_id} selected="selected" {/if} value="{$aSubCategory.category_id}">--{$value_name}</option>
                                {if !empty($aSubCategory.sub_category)}
                                    {foreach from=$aSubCategory.sub_category item=aSubSubCategory}
                                        {if Phpfox::isPhrase($this->_aVars['aSubSubCategory']['title'])}
                                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubSubCategory']['title']) ?>
                                        {else}
                                            {assign var='value_name' value=$aSubSubCategory.title|convert}
                                        {/if}
                                        <option id="{$aSubSubCategory.category_id}" {if isset($aForms.category_id) && $aForms.category_id == $aSubSubCategory.category_id} selected="selected" {/if} value="{$aSubSubCategory.category_id}">----{$value_name}</option>
                                    {/foreach}
                                {/if}
                            {/foreach}
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>
        {/if}
        <div class="ynstore-btn-block">
            <a class="ynstore-btn-close" onclick="ynsocialstore.advSearchProductDisplay(); return false;" href="javascript:void(0)">
                <i class="fa fa-times" aria-hidden="true"></i>
                {_p var='ynsocialstore.close'}
            </a>

            <div class="pull-right">
                <input type="submit" id="filter_submit" name="search[submit]" value="{_p var='ynsocialstore.search'}" class="btn btn-primary"/>
                <input type="button" onclick="clearInputSearchProduct()" id="filter_submit" name="search[reset]" value="{_p var='ynsocialstore.reset'}" class="btn btn-default"/>
            </div>
        </div>
    </form>
</div>

{literal}
<script type="text/javascript">
    function clearInputSearchProduct() {
        $('#page_ynsocialstore_index #ynsocialstore_adv_searchproduct input[type=text]').val('');
        $('#page_ynsocialstore_index #ynsocialstore_adv_searchproduct select').val(0);
        $('#page_ynsocialstore_index #form_main_search input[type=search]').val('');
    }
    function checkOnSearchProductSubmit()
    {
        if ($(".header_bar_search input[name='search[search]']").length > 0){
            var val = $(".header_bar_search input[name='search[search]']").val();
            $('#ynsocialstore_adv_searchproduct #keywords').val(val);
        }

        return true;
    }
    $Behavior.ynsocialstore_advsearch_product = function() {
        if($('#form_main_search').length && !$('#ynsocialstore_adv_searchproduct').hasClass('init')) {
            let advsearchObject = $('#ynsocialstore_adv_searchproduct');
            let parent = advsearchObject.closest('._block_content');
            advsearchObject.detach().prependTo(parent.get(0));
            $('#ynsocialstore_adv_searchproduct').addClass('init');
        }
    }
</script>
{/literal}
