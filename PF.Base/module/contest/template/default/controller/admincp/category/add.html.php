<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<form method="post" action="{url link='admincp.contest.category.add'}">
    <div class="panel panel-default">
    {if $bIsEdit}
        <div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
        <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
    {/if}
        <div class="panel-heading">
            {phrase var='contest_category_detail'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='parent_category'}:</label>
                <select name="val[parent_id]" class="form-control">
                    <option value="">{phrase var='select_form_select'}:</option>
                    {$sOptions}
                </select>
            </div>
            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}

        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='contest.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>