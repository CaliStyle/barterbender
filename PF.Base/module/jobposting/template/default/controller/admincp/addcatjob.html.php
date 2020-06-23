<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.jobposting.addcatjob'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='category_details'}
            </div>
        </div>
        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
            <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
            {/if}
            <div class="form-group">
                <label>{phrase var='parent_category'}:</label>
                <select title="{phrase var='parent_category'}" class="form-control" name="val[parent_id]">
                    <option value="">{phrase var='select'}:</option>
                    {foreach from=$aCategories item=aCategory}
                        <option value="{$aCategory.category_id}" {value type="select" id="parent_id" default=$aCategory.category_id}>{softPhrase var=$aCategory.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                {field_language phrase='name' label='title' field='name' format='val[name_' size=30 maxlength=100}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary">
        </div>
    </div>
</form>