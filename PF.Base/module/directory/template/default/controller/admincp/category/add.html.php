<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<form method="post" action="{url link='admincp.directory.category.add'}" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='business_category_detail'}
            </div>
        </div>

        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.category_id}"/></div>
            <div><input type="hidden" name="val[name]" value="{$aForms.title}"/></div>
            {/if}
            <div class="form-group">
                <label for="parent_category">{phrase var='parent_category'}:</label>
                <select name="val[parent_id]" id="parent_category" class="form-control">
                    <option value="">{phrase var='select'}:</option>
                    {$sOptions}
                </select>
            </div>

            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary"/>
        </div>
    </div>
</form>
