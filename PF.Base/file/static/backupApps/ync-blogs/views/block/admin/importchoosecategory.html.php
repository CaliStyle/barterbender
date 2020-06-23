<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/01/2017
 * Time: 11:17
 */
?>
<form method="post">
    <div class="table form-group">
        <div class="table_left">
            {_p('parent_category')}:
        </div>
        <div class="table_right">
            <select required name="category" id="add_select" class="form-control">
                <option value="">{_p('all_categories')}</option>
                {foreach from=$aCategories item=aCategory}
                    <option {if isset($aForms.category) && $aCategory.category_id == $aForms.category}selected="true"{/if} value="{$aCategory.category_id}"{value type='select' id='category_id' default=$aCategory.category_id}>
                        {softPhrase var=$aCategory.name|convert}
                    </option>
                    {foreach from=$aCategory.categories item=aSubCategory}
                        <option {if isset($aForms.category) && $aSubCategory.category_id == $aForms.category}selected="true"{/if} value="{$aSubCategory.category_id}"{value type='select' id='category_id' default=$aSubCategory.category_id}>
                            --{softPhrase var=$aSubCategory.name|convert}
                        </option>

                        {foreach from=$aSubCategory.categories item=aSubSubCategory}
                            <option {if isset($aForms.category) && $aSubSubCategory.category_id == $aForms.category}selected="true"{/if} value="{$aSubSubCategory.category_id}"{value type='select' id='category_id' default=$aSubSubCategory.category_id}>
                                ----{softPhrase var=$aSubSubCategory.name|convert}
                            </option>
                        {/foreach}
                    {/foreach}
                {/foreach}
            </select>
        </div>
        <input type="hidden" name="iBlogId" value="{$iBlogId}">
        <input type="hidden" name="sBlogId" value="{$sBlogId}">
        <div class="clear"></div>
    </div>

    <div class="">
        <input class="btn btn-primary" type="button" onclick="js_box_remove(this); ynadvancedblog_manage.importBlogInAdminProcess(this);" value="{_p('Import')}" class="button"/>
        <input class="btn btn-danger" type="button" onclick="js_box_remove(this)" value="{_p('Cancel')}" class="button"/>
    </div>
</form>
