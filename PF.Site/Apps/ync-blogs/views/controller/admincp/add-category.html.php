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

    {field_language phrase='name' required=true label='name' field='name' format='val[name_' size=30 maxlength=100 }

    <div>
        <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
    </div>
</form>