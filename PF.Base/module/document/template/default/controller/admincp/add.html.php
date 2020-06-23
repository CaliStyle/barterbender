<?php
/**
 *
 *
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !empty($error_message)}
<div id="jp_document_file_message" class="error_message" style="display:block">{$error_message}</div>
{/if}
<form method="post" action="{url link='admincp.document.add'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='document_category_details'}
            </div>
        </div>
        <div class="panel-body">
            {if $bIsEdit}
            <input type="hidden" name="id" value="{$aForms.category_id}"/>
            <input type="hidden" name="val[name]" value="{$aForms.name}"/>
            {/if}
            <div class="form-group">
                <label for="parent_id">{phrase var='parent_category'}:</label>
                <select name="val[parent_id]" id="parent_id" class="form-control">
                    <option value="">{phrase var='select'}:</option>
                    {$sOptions}
                </select>
            </div>
            <div class="form-group">
                {field_language phrase='name' label='title' field='name' format='val[name_' size=30 maxlength=100}
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" class="btn btn-primary">{phrase var='submit'}</button>
    </div>
</form>