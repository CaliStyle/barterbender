<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
<div id="js_field_holder">
    {$sCustomCreateJs}
    <form method="post" action="{url link='admincp.fevent.custom.add'}" id="js_custom_field" onsubmit="{$sCustomGetJsForm}">
        {if $bIsEdit}<input type="hidden" id='field_id' name='val[field_id]' value="{$iId}">{/if}
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p var='event_category_details'}
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="category">
                        {required}{_p var='fevent.event_category'}:
                    </label>
                    {$sOptions}
                </div>
            
                <div class="form-group">
                    <label for="required">
                        {_p var='fevent.required'}:
                    </label>
                    <div class="radio">
                        <label class="radio-inline"><input type="radio" name="val[is_required]" value="1" class="v_middle checkbox" {value type='radio' id='is_required' default='1'}/>{_p var='yes'}</label>
                        <label class="radio-inline"><input type="radio" name="val[is_required]" value="0" class="v_middle checkbox" {value type='radio' id='is_required' default='0' selected=true}/>{_p var='no'}</label>
                    </div>
                </div>
        
                {if !$bIsEdit}
                    <div class="form-group">
                        <label for="type">
                            {required}{_p var='custom.type'}:
                        </label>
                        <select name="val[var_type]" class="var_type form-control">
                            <option value="">{_p var='fevent.select'}:</option>
                            <option value="textarea"{value type='select' id='var_type' default='textarea'}>{_p var='custom.large_text_area'}</option>
                            <option value="text"{value type='select' id='var_type' default='text'}>{_p var='custom.small_text_area_255_characters_max'}</option>
                            <option value="select"{value type='select' id='var_type' default='select'}>{_p var='custom.selection'}</option>
                            <option value="multiselect"{value type='select' id='var_type' default='multiselect'}>{_p var='core.multiple_selection'}</option>
                            <option value="radio"{value type='select' id='var_type' default='radio'}>{_p var='core.radio'}</option>
                            <option value="checkbox"{value type='select' id='var_type' default='checkbox'}>{_p var='core.checkbox'}</option>
                        </select>
                    </div>
                {else}
                    <div class="form-group">
                        <label for="type">
                            {required}{_p var='custom.type'}:
                        </label>
                        {$sTypeCustomFieldText}
                        <input type="hidden" class="var_type" name="val[var_type]" value="{$sTypeCustomField}">
                    </div>
                {/if}
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p var='fevent.field_name_amp_values'}
                </div>
            </div>
            <div class="panel-body">
                <label for="">{required}{_p var='name'}:</label>
                {if isset($aForms.name) && is_array($aForms.name)}
                    {foreach from=$aForms.name key=sPhrase item=aValues}
                        {module name='language.admincp.form' type='text' id='name' mode='text' value=$aForms.name}
                    {/foreach}
                {else}
                    {module name='language.admincp.form' type='text' id='name' mode='text'}
                {/if}
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                {if $bIsEdit && isset($aForms.option)}
                    <label>{_p var='current_values'}</label>
                    <div>
                        {foreach from=$aForms.option name=options key=iKey item=aOptions}
                        <div class="table js_current_value js_option_holder" id="js_current_value_{$iKey}">
                            <span>{_p var='option_count' count=$phpfox.iteration.options}:</b> <a href="#?id={$iKey}" class="js_delete_current_option"><i class="fa fa-remove"></i></a></span>
                            <div class="form-group">
                                {module name='language.admincp.form' type='text' id='current' value=$aOptions mode='text'}
                            </div>
                        </div>
                        {/foreach}
                    </div>
                {/if}
                <!--{* This next block is used as a template *}-->
                <div id="js_sample_option" style="display:none;">
                    <div class="js_option_holder">
                        <div class="form-group">
                            <span>{_p var='option_html_count'}:</b> <span class="js_option_delete"></span></span>
                            {foreach from=$aLanguages item=aLang}
                            <div>
                                <input type="text" name="val[option][#][{$aLang.language_id}][text]" value="" placeholder="{$aLang.title}" class="form-control"/>
                            </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="_table" id="tbl_option_holder" {if $bHideOptions} style="display:none;"{/if} >
                    <label>{if $bIsEdit}{_p var="Extra Values"}{else}{_p var='values'}{/if}</label>
                    <div id="js_option_holder"></div>
                </div>
                <div class="table_clear_more_options" id="tbl_add_custom_option" {if $bHideOptions} style="display:none;"{/if} >
                    <a role="button" class="js_add_custom_option">{_p var='add_new_option'}</a>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="panel-bottom">
                    {if !$bIsEdit}
                        <input type="submit" value="{_p var='fevent.add'}" class="btn btn-primary" />
                    {else}
                        <input type="submit" value="{_p var='fevent.update'}" class="btn btn-primary" />
                    {/if}
                </div>
            </div>
        </div>
    </form>
</div>

{literal}
<script type="text/javascript">
;
$Behavior.ynFeventCustomField = function() {
    $('.js_jc_delete_current_option').click(function(){
        var obj= this;
        $Core.jsConfirm({message: oTranslations['are_you_sure_you_want_to_delete_this_custom_option']}, function() {
            aParams = $.getParams(obj.href);
            $.ajaxCall('fevent.deleteOption', 'id=' + aParams['id'] );
        }, function(){});
        return false;
    });
};
    
</script>
{/literal}
