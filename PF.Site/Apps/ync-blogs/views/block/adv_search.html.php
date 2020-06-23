<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 17/01/2017
 * Time: 10:42
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_ynblog_search_wrapper" class="js_p_search_wrapper">
    <input type="hidden" value="{if !empty($aForms) && count($aForms) > 1}1{/if}" id="js_adv_search_value" name="search[adv_search]"/>
    <div class="js_p_search_result hide p-advance-search-button">
        <a href="javascript:void(0)" class="js_p_enable_adv_search_btn" onclick="p_core.pEnableAdvSearch();return false;" title="{_p var='ynblog_advsearch_tooltip'}">
            <i class="ico ico-dottedmore-o"></i>
        </a>
    </div>
</div>
<div class="js_p_adv_search_wrapper p-advance-search-form p-advblog-search-wrapper" style="display: none;">
<form id="js_ynblog_adv_search_wrapper" action="{$sFormUrl}" onsubmit="return checkOnSearchProductSubmit()" >
    <input type="hidden" name="s" value="1">
    <input type="hidden" name="view" value="{$sView}">
    <input type="hidden" name="search[search]" id="ynblog_input_core_search" value="{value type='input' id='search'}">
    <input type="hidden" name="search[category_id]" id="ynblog_input_core_search" value="{value type='input' id='category_id'}">
    <input type="hidden" name="search[blogger_id]" id="ynblog_input_core_search" value="{value type='input' id='blogger_id'}">
    <div class="p-advblog-search-formgroup-wrapper">
        {if !defined('PHPFOX_IS_USER_PROFILE')}
        <div class="form-group">
            <label>{_p var="Author"}:</label>
            <input class="form-control" type="text" name="search[author_name]" value="{value type='input' id='author_name'}" id="title" size="50" />
        </div>
        {/if}
        {if empty($bIsCategoryRequest)}
        <div class="form-group">
            <label>{_p var="Category"}:</label>
            <select name="search[category_id]" class="js_mp_category_list form-control">
                <option value="0">{_p var='all_categories'}</option>
                {foreach from=$aCategories item=aCategory}
                    <option {if isset($aForms.category_id) && $aCategory.category_id == $aForms.category_id}selected="true"{/if} value="{$aCategory.category_id}"{value type='select' id='category_id' default=$aCategory.category_id}>
                        {softPhrase var=$aCategory.name|convert}
                    </option>
                    {foreach from=$aCategory.categories item=aSubCategory}
                        <option {if isset($aForms.category_id) && $aSubCategory.category_id == $aForms.category_id}selected="true"{/if} value="{$aSubCategory.category_id}"{value type='select' id='category_id' default=$aSubCategory.category_id}>
                            --{softPhrase var=$aSubCategory.name|convert}
                        </option>

                        {foreach from=$aSubCategory.categories item=aSubSubCategory}
                            <option {if isset($aForms.category_id) && $aSubSubCategory.category_id == $aForms.category_id}selected="true"{/if} value="{$aSubSubCategory.category_id}"{value type='select' id='category_id' default=$aSubSubCategory.category_id}>
                                ----{softPhrase var=$aSubSubCategory.name|convert}
                            </option>
                        {/foreach}
                    {/foreach}
                {/foreach}
            </select>
        </div>
        {/if}
    </div>
    <div class="form-group clearfix advance_search_form_button">
        <div class="pull-left">
            <span class="advance_search_dismiss" onclick="p_core.pEnableAdvSearch(); return false;">
                <i class="ico ico-close"></i>
            </span>
        </div>
        <div class="pull-right">
            <button type="reset" class="btn btn-default btn-sm" onclick="p_core.pEnableAdvSearch(); return false;" id="ynadvblog_btn-close">{_p var="Cancel"}</button>
            <button type="submit" class="btn btn-primary btn-sm ml-1">{_p var="Submit"}</button>
        </div>
    </div>
</form>
</div>
{literal}
<script type="text/javascript">
   

    function checkOnSearchProductSubmit()
    {
        if ($("#form_main_search input[name='search[search]']").length > 0){
            var val = $("#form_main_search input[name='search[search]']").val();
            $('#js_ynblog_adv_search_wrapper #ynblog_input_core_search').val(val);
        }

        return true;
    }
</script>
{/literal}

