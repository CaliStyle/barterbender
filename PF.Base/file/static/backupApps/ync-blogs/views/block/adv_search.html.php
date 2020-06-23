<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 17/01/2017
 * Time: 10:42
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_ynblog_search_wrapper">
    <input type="hidden" value="{if !empty($aForms) && count($aForms) > 1}1{/if}" id="js_adv_search_value" name="search[adv_search]"/>

    <a href="javascript:void(0)" class="btn btn-default dropdown-toggle" onclick="ynblogEnableAdvSearch(this);return false;">
        {_p var='advanced_search'} <span class="ico ico-caret-down"></span>
    </a>
</div>

<form id="js_ynblog_adv_search_wrapper" action="{$sFormUrl}" onsubmit="return checkOnSearchProductSubmit()" class="colapse">
    <input type="hidden" name="s" value="1">
    <input type="hidden" name="view" value="{$sView}">
    <input type="hidden" name="search[search]" id="ynblog_input_core_search" value="{value type='input' id='search'}">
    <input type="hidden" name="search[category_id]" id="ynblog_input_core_search" value="{value type='input' id='category_id'}">
    <input type="hidden" name="search[blogger_id]" id="ynblog_input_core_search" value="{value type='input' id='blogger_id'}">
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
            <option value="0">{_p var='All Categories'}</option>
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
    <div class="ynblog_button">
        <button type="submit" class="btn btn-primary">{_p var="Submit"}</button>
        <button type="reset" class="btn btn-default" onclick="ynblogEnableAdvSearch(this)" id="ynadvblog_btn-close">{_p var="Cancel"}</button>
    </div>
</form>

{literal}
<script type="text/javascript">
    $Behavior.initYnBlogSearch = function() {
        var parent = '';
        if ($("body[id^='page_route_advanced-blog']").length)
            parent = "body[id^='page_route_advanced-blog']";
        else if ($("body[id^='page_ynblog_index']").length)
            parent = "body[id^='page_ynblog_index']";

        if (parent && $('#form_main_search') && $(parent).length && $('#js_ynblog_search_wrapper') && $('#form_main_search').find('#js_ynblog_search_wrapper').length == 0) {
            if ($('.header_bar_menu').find('.header_filter_holder').length == 0) {
                $("#js_ynblog_search_wrapper").detach().appendTo(parent + ' .header-filter-holder');
                $("#js_ynblog_search_wrapper").addClass("filter-options");
            }
            else {
                $("#js_ynblog_search_wrapper").detach().appendTo(parent + ' .header_filter_holder');
                $("#js_ynblog_search_wrapper").addClass("inline-block");
            }
            $("#js_ynblog_adv_search_wrapper").insertBefore('.location_2');
            if ($('#js_adv_search_value').val() == '1') {
                $("#js_ynblog_adv_search_wrapper").show();
                $('#js_forum_search_result').find('i').removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
            } else {
                $("#js_ynblog_adv_search_wrapper").hide();
                $('#js_forum_search_result').find('i').removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
            }
        }
    }

    function ynblogEnableAdvSearch(obj) {
        if ($('#js_adv_search_value').val() == '0' || $('#js_adv_search_value').val() == '') {
            $('#js_adv_search_value').val(1);
            $("#js_ynblog_adv_search_wrapper").slideDown();
            $('#js_forum_search_result').find('i').removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
        }
        else {
            $("#js_ynblog_adv_search_wrapper").slideUp();
            $('#js_adv_search_value').val(0);
            $('#js_forum_search_result').find('i').removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
        }
    }

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

