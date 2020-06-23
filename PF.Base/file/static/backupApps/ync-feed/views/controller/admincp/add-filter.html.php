<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 30/12/2016
 * Time: 18:21
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.ynfeed.add-filter'}" enctype="multipart/form-data">
    <div class="panel panel-default">
        {if $bIsEdit}
            <div class="panel-heading">
                <div class="panel-title">
                    {_p('edit_filter')}: {softPhrase var=$aForms.title}
                </div>
            </div>
            <div><input type="hidden" name="edit_id" value="{$iEditId}" /></div>
            <div><input type="hidden" name="val[name]" value="{$aForms.title}" /></div>
        {else}
            <div class="panel-heading">
                <div class="panel-title">
                    {_p('add_filter')}:
                </div>
                <p class="extra_info">{_p('add_new_filter_des')}</p>
            </div>
        {/if}
        <div class="panel-body">
            {if !$bIsEdit}
                <div class="form-group">
                    <label for="">{_p('module')}:</label>
                    <select name="val[module_id]" class="form-control">
                        <option value="">{_p('select')}</option>
                        {foreach from=$aModules key=sModuleId item=sModuleName}
                        <option value="{$sModuleId}">{$sModuleName}</option>
                        {/foreach}
                    </select>
                </div>
            {/if}

            {foreach from=$aLanguages item=aLanguage}
                <div class="form-group">
                    <label for="">{_p('filter_name')}&nbsp;<strong>{$aLanguage.title}</strong>:</label>
                    {assign var='value_name' value="name_".$aLanguage.language_id}
                    <input type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" class="form-control"/>
                    <div class="extra_info">
                        {_p('ynfeed_filter_title_des')}
                    </div>
                </div>
            {/foreach}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p('submit')}" class="button btn-primary" />
        </div>
    </div>
</form>