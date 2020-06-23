<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 30/12/2016
 * Time: 18:21
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='ynblog.admincp.add-category'}">
    {if $bIsEdit}
        {if isset($aForms.parent_id)}
            <div><input type="hidden" name="sub" value="{$iEditId}" /></div>
        {else}
            <div><input type="hidden" name="id" value="{$iEditId}" /></div>
        {/if}
        <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
    {/if}
    <div class="form-group">
        <label for="">
            {_p('Parent category')}:
        </label>
        <select name="val[parent_id]" id="add_select" class="form-control">
            <option value="0">{_p('None')}</option>
            {foreach from=$aCategories item=aCategory}
                <option {if isset($aForms.parent_id) && $aCategory.category_id == $aForms.parent_id}selected="true"{/if} value="{$aCategory.category_id}"{value type='select' id='parent_id' default=$aCategory.category_id}>
                    {softPhrase var=$aCategory.name|convert}
                </option>
                {foreach from=$aCategory.categories item=aSubCategory}
                    <option {if isset($aForms.parent_id) && $aSubCategory.category_id == $aForms.parent_id}selected="true"{/if} value="{$aSubCategory.category_id}"{value type='select' id='parent_id' default=$aSubCategory.category_id}>
                        --{softPhrase var=$aSubCategory.name|convert}
                    </option>
                {/foreach}
            {/foreach}
        </select>
    </div>

    {foreach from=$aLanguages item=aLanguage}
    <div class="form-group">
        <label for="">
            {_p('Name')}&nbsp;{$aLanguage.title}:
        </label>
        {assign var='value_name' value="name_".$aLanguage.language_id}
        <input class="form-control" type="text" required name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" />
    </div>
    {/foreach}

    <div>
        <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
    </div>
</form>