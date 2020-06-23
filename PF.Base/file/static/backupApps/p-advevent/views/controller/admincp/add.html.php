<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<form method="post" action="{url link='admincp.fevent.add'}">
	{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
	<div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
	{/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='event_category_details'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="parent_category">{_p var='parent_category'}:</label>
                <select class="form-control" name="val[parent_id]">
                    <option value="">{_p var='select_form_select'}:</option>
                    {$sOptions}
                </select>
                <div class="clear"></div>
            </div>
            <div class="form-group">
                {field_language phrase='name' label='Name' field='name' format='val[name_' size=30 maxlength=255 required=true}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
        </div>
</form>