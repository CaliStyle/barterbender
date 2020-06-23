<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.ecommerce.customfield.add'}" id="js_add_group_name_form" name="js_add_group_name_form" >
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if !empty($bIsEditGroup) }
                    {phrase var='edit_custom_field_groups'}
                {else}
                    {phrase var='add_new_custom_field_groups'}
                {/if}
            </div>
        </div>

        <div class="panel-body">
            {if !empty($bIsEditGroup)}
            <div><input type="hidden" name="id" value="{$aGroup.group_id}"></div>
            {/if}
            <div class="form-group">
                <label for="">{required}{phrase var='group_name'}</label>
                {foreach from=$aLanguages item=aLanguage}
                    <input class="form-control" type="text" name="val[group_name]{if isset($aLanguage.phrase_var_name)}[{$aLanguage.phrase_var_name}]{/if}[{$aLanguage.language_id}]{if isset($sMode)}[{$sMode}]{/if}" value="{$aLanguage.post_value|htmlspecialchars}" />
                    <p class="help-block">{$aLanguage.title}</p>
                {/foreach}
            </div>

            {if !empty($bIsEditGroup)}
                <div class="form-group">
                    <label>{phrase var='mapping_categories'}</label>
                    <div id="mapping_categories">
                        {foreach from=$aCategories key=iKeyCate item=aCategory}
                        <div class="checkbox">
                            <label><input type="checkbox" name="val[categories][]" value="{$aCategory.category_id}" {if in_array($aCategory.category_id, $aGroup.categories)} checked="checked" {/if}>
                            {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                {phrase var=$aCategory.title}
                            {else}
                                {$aCategory.title|convert|clean}
                            {/if}
                            </label>
                        </div>
                        {/foreach}
                    </div>
                </div>
                {if $aGroup.customfield}
                <div class="table-responsive">
                    <label>{phrase var='custom_fields'}</label>
                    <table class="table table-bordered">
                        <tr>
                            <th>{phrase var='custom_field_name'}</th>
                            <th class="t_center w220">{phrase var='option'}</th>
                        </tr>
                        {foreach from=$aGroup.customfield key=iKey item=iField}
                        <tr>
                            <input type="hidden" name="val[customfield][]" value="{$iField.field_id}">
                            <td>{phrase var=$iField.phrase_var_name}</td>
                            <td class="t_center w220">
                                <a href="javascript:void(0)" onclick="tb_show('', $.ajaxBox('ecommerce.AdminAddCustomFieldBackEnd', 'height=300&amp;width=300&action=edit&id={$iField.field_id}'))">{phrase var='edit'}</a>
                                /
                                <a href="{$sUrl}?id={$aGroup.group_id}&delete={$iField.field_id}" class="sJsConfirm">{phrase var='delete'}</a>
                            </td>
                        </tr>
                        {/foreach}
                    </table>
                </div>
                {/if}
                <div class="js_mp_parent_holder" id="js_mp_holder">
                    {if isset($bIsEditGroup) && $bIsEditGroup}
                        <a href="javascript:void(0)" onclick="tb_show('', $.ajaxBox('ecommerce.AdminAddCustomFieldBackEnd', 'height=300&width=300&action=add&iGroupId={$aGroup.group_id}')); return false;">{phrase var='add_custom_field'}</a>
                    {/if}
                </div>
            {/if}
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary">
        </div>
    </div>
</form>