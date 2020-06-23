<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_field_holder" class="ecommerce_admincp_custom_field">
    <form method="post" onsubmit="return onSubmitCreateCustomField()" id="js_custom_field">
        {if $bIsEdit}
        <div><input id="js_mode_edit" type="hidden" name="val[id]" value="{$iId}"></div>
        {/if}
        <div><input type="hidden" name="val[groupid]" value="{$iGroupId}"></div>
        {if empty($bIsEdit)}
        <div class="form-group">
            <label for="var_type">{required}{phrase var='custom.type'}:</label>
            <select name="val[var_type]" class="var_type form-control">
                <option value="">{phrase var='custom.select'}:</option>
                <option value="textarea" {value type='select' id='var_type' default='textarea' }>{phrase var='custom.large_text_area'}</option>
                <option value="text" {value type='select' id='var_type' default='text' }>{phrase var='custom.small_text_area_255_characters_max'}</option>
                <option value="select" {value type='select' id='var_type' default='select' }>{phrase var='custom.selection'}</option>
                <option value="multiselect" {value type='select' id='var_type' default='multiselect' }>{phrase var='core.multiple_selection'}</option>
                <option value="radio" {value type='select' id='var_type' default='radio' }>{phrase var='core.radio'}</option>
                <option value="checkbox" {value type='select' id='var_type' default='checkbox' }>{phrase var='core.checkbox'}</option>
            </select>
        </div>
        {else}
            <input type="hidden" name="val[var_type]" value="{$aForms.var_type}">
        {/if}

        <div class="form-group">
            <label for="">{required}{phrase var='custom.name'}:</label>
            {if $bIsEdit && isset($aForms.name) && Phpfox::getLib('locale')->isPhrase('$aForms.name')}
                {module name='language.admincp.form' type='text' id='name' mode='text' value=$aForms.name}
            {else}
                {if isset($aForms.name) && is_array($aForms.name)}
                    {foreach from=$aForms.name key=sPhrase item=aValues}
                        {module name='language.admincp.form' type='text' id='name' mode='text' value=$aForms.name}
                    {/foreach}
                {else}
                    {module name='language.admincp.form' type='text' id='name' mode='text'}
                {/if}
            {/if}
            <label><input type="checkbox" name="val[is_required]" id="is_required" {if $aForms.is_required}checked="checked" {/if}/> {phrase var='require_field'}</label>
        </div>

        {if $bIsEdit && isset($aForms.option)}
        <div class="form-group" id="tbl_edit">
            <label for="">{phrase var='custom.current_values'}:</label>
            {foreach from=$aForms.option name=options key=iKey item=aOptions}
            <div class="p_4 js_current_value js_option_holder" id="js_current_value_{$iKey}">
                <b>{phrase var='custom.option_count' count=$phpfox.iteration.options}:</b> <a href="javascript:void(0);" data-params="#?id={$iKey}" class="js_jc_delete_current_option">
                    <i class="fa fa-remove"></i>
                </a>
                <div class="p_4">
                    {module name='language.admincp.form' type='text' id='current' value=$aOptions mode='text'}
                </div>
            </div>
            {/foreach}
        </div>
        {/if}

        <div class="form-group" id="js_multi_select" style="{if $bHideOptions || $bIsEdit}display:none;{/if}">
            <label for="">{if $bIsEdit}{_p var='Extra Values'}{else}{phrase var='custom.values'}{/if}:</label>
            <div id="js_sample_option">
                <div class="js_option_holder">
                    <div class="p_4">
                        <b>{phrase var='custom.option_html_count'}:</b> <span class="js_option_delete"></span>
                        <div class="p_4">
                            {foreach from=$aLanguages item=aLang}
                            <div>
                                <input class="form-control" type="text" name="val[option][#][{$aLang.language_code}][text]" value="" /> {$aLang.title}
                            </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {if empty($bIsEdit) || ($aForms.var_type != 'textarea' && $aForms.var_type != 'text')}
        <div class="form-group" id="tbl_option_holder">
            <label>{if !$bIsEdit}{_p var='Values'}: {else}{_p var='Extra Values'}:{/if}</label>
            <div id="js_option_holder"></div>
        </div>
        <div class="form-group" id="tbl_add_custom_option">
            <label></label>
            <a href="#" class="js_add_custom_option">{phrase var='custom.add_new_option'}</a>
        </div>
        {/if}
        <input type="submit" value="{if $bIsEdit}{phrase var='custom.update'}{else}{phrase var='custom.add'}{/if}" class="btn btn-primary" id="js_add_field_button" />
        <span id="js_add_field_loading"></span>
    </form>
</div>


<script type="text/javascript" src="{$urlModule}/custom/static/jscript/admin.js"></script>
{literal}
<script type="text/javascript">
    function onSubmitCreateCustomField() {
        $('#js_add_field_button').attr('disabled', true);
        $('#js_add_field_loading').html($.ajaxProcess(oTranslations['saving'])).show();

        var is_edit = $('#js_mode_edit').length;
        if (is_edit) {
            $.ajaxCall('ecommerce.updateField', $('#js_custom_field').serialize(), 'post');
        } else {
            $.ajaxCall('ecommerce.addField', $('#js_custom_field').serialize(), 'post');
        }
        return false;
    }
</script>
{/literal}
<script type="text/javascript">
var bIsEdit = false;

$(function(){l}
	$Core.custom.init({$iDefaultSelect});
    {if !$bIsEdit}
    $('#tbl_option_holder').hide();
    $('#tbl_add_custom_option').hide();
    {/if}
{r});

$Behavior.initDeleteOption = function() {l}
    $('.js_jc_delete_current_option').click(function(){l}
    if (confirm('{$phrase}')){l}
        var aParams = $.getParams($(this).data('params'));
        $.ajaxCall('ecommerce.deleteOption', 'id=' + aParams['id'] );
    {r}
    return false;
    {r});
{r}

$Behavior.ecommerce_custom_admin_init();

</script>