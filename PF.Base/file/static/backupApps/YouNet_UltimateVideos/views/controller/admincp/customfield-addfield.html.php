<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<div id="js_field_holder" class="ynuv_admincp_custom_field">
    <form method="post" onsubmit="return onSubmitValid1(this, {$iGroupId}, {$bIsEdit});" action="" id="js_custom_field">
        {if $bIsEdit}<input type="hidden" name="val[id]" value="{$iId}" />{/if}
        <input type="hidden" name="val[groupid]" value="{$iGroupId}"/>
        <div class="table dont-unbind-children"{if $bIsEdit} style="display:none;"{/if}>
            <div class="table_left">
                {required}{_p var='type'}:
            </div>
            <div class="table_right">
                <select name="val[var_type]" class="var_type">
                    <option value="">{_p var='select'}:</option>
                    <option value="textarea"{value type='select' id='var_type' default='textarea'}>{_p var='ultimatevideo_large_text_area'}</option>
                    <option value="text"{value type='select' id='var_type' default='text'}>{_p var='ultimatevideo_small_text_area_255_characters_max'}</option>
                    <option value="select"{value type='select' id='var_type' default='select'}>{_p var='selection'}</option>
                    <option value="multiselect"{value type='select' id='var_type' default='multiselect'}>{_p var='multiple_selection'}</option>
                    <option value="radio"{value type='select' id='var_type' default='radio'}>{_p var='radio'}</option>
                    <option value="checkbox"{value type='select' id='var_type' default='checkbox'}>{_p var='checkbox'}</option>
                </select>
            </div>
        </div>

        <div class="table">
            <div class="table_left">
                {required}{_p var='name'}:
            </div>
            <div class="table_right">
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
                <label><input type="checkbox" name="val[is_required]" id="is_required"
                              {if $aForms.is_required}checked="checked" {/if}/> {_p('require_field')}</label>
            </div>
        </div>

        {if $bIsEdit && isset($aForms.option)}
            <div class="table" id="tbl_edit">
                <div class="table_left">
                    {_p var='current_values'}:
                </div>
                <div class="table_right">
                    {foreach from=$aForms.option name=options key=iKey item=aOptions}
                        <div class="p_4 js_current_value js_option_holder" id="js_current_value_{$iKey}">
                            <b>{_p var='option_count' count=$phpfox.iteration.options}:</b>
                            <a href="#?id={$iKey}" onclick="deleteCurrentOption({$iKey});"
                               class="js_jc_delete_current_option"><i class="ico ico-close"></i></a>
                            <div class="p_4">
                                {module name='language.admincp.form' type='text' id='current' value=$aOptions mode='text'}
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/if}

        {* This next block is used as a template *}
        <div class="table" id="js_multi_select"{if $bHideOptions || $bIsEdit} style="display:none;"{/if}>
            <div class="table_left">
                {if $bIsEdit}Extra Values{else}{_p var='values'}{/if}:
            </div>
            <div class="table_right">
                <div id="js_sample_option">
                    <div class="js_option_holder">
                        <div class="p_4">
                            <b>{_p var='option_html_count'}:</b> <span class="js_option_delete"></span>
                            <div class="p_4">
                                {foreach from=$aLanguages item=aLang}
                                    <div>
                                        <input type="text" name="val[option][#][{$aLang.language_code}][text]"
                                               value=""/> {$aLang.title}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {if $aForms.var_type != 'textarea' && $aForms.var_type != 'text'}
            <div class="form-group" id="tbl_option_holder">
                <label>{if !$bIsEdit}{_p var='Values'}: {else}{_p var='Extra Values'}:{/if}</label>
                <div id="js_option_holder"></div>
            </div>
            <div class="form-group" id="tbl_add_custom_option">
                <label></label>
                <a href="#" class="js_add_custom_option">{phrase var='custom.add_new_option'}</a>
            </div>
        {/if}

        <div class="table_clear">
            <input type="submit" value="{if $bIsEdit}{_p var='update'}{else}{_p var='add'}{/if}" class="btn btn-primary"
                   id="js_add_field_button"/>
            <span id="js_add_field_loading"></span>
        </div>
    </form>
</div>

<script type="text/javascript" src="{$urlModule}custom/static/jscript/admin.js"></script>
{literal}
    <style>
        .js_jc_delete_current_option i {
            color: red;
            margin-left: 8px;
        }
    </style>
    <script type="text/javascript">
        function deleteCurrentOption(iKey) {
            if (confirm('Are you sure?')) {
                $.ajaxCall('ultimatevideo.deleteOption', 'id=' + iKey, 'post');
            }
        }

        function onSubmitValid1(obj, iGroupId, bIsEdit) {
            if (bIsEdit) {
                $.ajaxCall('ultimatevideo.updateField', $(obj).serialize() + '&id=' + iGroupId, 'post');
                return false;
            } else {
                $.ajaxCall('ultimatevideo.addField', $(obj).serialize() + '&id=' + iGroupId, 'post');
            }

            return false;
        }

    </script>
{/literal}