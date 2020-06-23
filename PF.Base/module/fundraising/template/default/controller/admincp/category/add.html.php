<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<form method="post" action="{url link='admincp.fundraising.category.add'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='campaign_category_detail'}
            </div>
        </div>
        <div class="panel-body">
            {if $bIsEdit}
                <div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
                <div><input type="hidden" name="val[name]" value="{$aForms.title}" /></div>
            {/if}

            <div class="form-group">
                <label for="parent_id">{phrase var='parent_category'}:</label>
                <select id="parent_id" name="val[parent_id]" class="form-control">
                    <option value="">{phrase var='select'}:</option>
                    {$sOptions}
                </select>
            </div>

            <div class="form-group">
                {field_language phrase='name' label='title' field='name' format='val[name_' size=30 maxlength=100}
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{phrase var='submit'}</button>
        </div>
    </div>
</form>