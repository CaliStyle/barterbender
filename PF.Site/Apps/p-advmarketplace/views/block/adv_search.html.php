<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="" class="js_p_search_wrapper" >
    <div  class=" js_p_search_result hide item_is_active_holder item_selection_active p-advance-search-button">
        <a class="js_p_enable_adv_search_btn" href="javascript:void(0)" onclick="p_core.pEnableAdvSearch();return false;">
            <i class="ico ico-dottedmore-o"></i>
        </a>
    </div>
</div>
<div class="js_p_adv_search_wrapper p-advance-search-form p-advmarketplace-search-wrapper" style="display: none">
        <div class="hidden">
            <input type="hidden" name="s" value="1">
            <input type="hidden" value="{$sListingView}" name="view">
            {if !empty($sCategoryUrl)}
            <input type="hidden" name="category" value="{$sCategoryUrl}">
            {/if}
        </div>
        <div class="p-advmarketplace-search-formgroup-wrapper">
            <div class="form-group js_core_init_selectize_form_group dont-unbind-children">
                <label>{_p var='advancedmarketplace.location'}</label>
                <div>
                    {select_location}
                    {module name='core.country-child'}
                </div>
            </div>

            
            <div class="form-group">
                <label for="">{phrase var='advancedmarketplace.city'}</label>
                <div class="d-block">
                    <input id="search_city" type="text" value="{if isset($sCity)}{$sCity}{/if}" name="city" class="search_keyword form-control" placeholder="{_p var = 'city_name'}">
                </div>
            </div>
            <div class="form-group">
                <label for="">{phrase var='advancedmarketplace.zip_postal_code'}</label>
                <div class="d-block">
                    <input id="search_zipcode" type="text" value="{if isset($sZipCode)}{$sZipCode}{/if}" name="zipcode" class="search_keyword form-control" placeholder="{_p var='xxxxxx'}">
                </div>
            </div>
            {if empty($sCategoryUrl) && !empty($parentCategories)}
            <div class="form-group js_core_init_selectize_form_group dont-unbind-children">
                <label>{_p var='category'}</label>
                <div class="d-block">
                    <select class="form-control" name="category" id="category">
                        <option value="">{_p var='any'}</option>
                        {foreach from=$parentCategories item=parentCategory}
                        <option value="{$parentCategory.category_id}" {value type='select' id='category' default=$parentCategory.category_id}>{_p var=$parentCategory.name}</option>
                        {/foreach}
                    </select>
                </div>
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
                <a class="btn btn-default btn-sm" href="javascript:void(0);" id="js_p_search_reset">{_p var='reset'}</a>
                <button name="submit" class="btn btn-primary ml-1 btn-sm" ><i class="ico ico-search-o mr-1"></i>{_p var='search'}</button>
            </div>
        </div>
</div>

{literal}
<script type="text/javascript">
    $Ready(function(){
        $('#js_p_search_reset').click(function(){
            $("#search_city").val('');
            $("#search_zipcode").val('');
            $('.js_p_adv_search_wrapper .js_core_init_selectize_form_group').each(function(){
               var select = $(this).find('select');
               if(select.length) {
                   if(select.hasClass('selectized')) {
                       (select.selectize())[0].selectize.setValue('');
                   } else {
                       select.val('');
                   }
               }
            });
        });
    });
</script>
{/literal}
