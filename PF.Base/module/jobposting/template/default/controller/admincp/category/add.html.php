<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.jobposting.category.add'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='campaign_category_details'}
            </div>
        </div>

        <div class="panel-body">
            {if $bIsEdit}
                <div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
                <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
            {/if}
            <div class="form-group">
                <label>{phrase var='parent_industry'}:</label>
                <select title="{phrase var='parent_industry'}" name="val[parent_id]" class="form-control">
                    <option value="">{phrase var='select'}:</option>
                    {$sOptions}
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