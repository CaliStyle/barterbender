<?php 
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
?>
<form method="post" action="{url link="admincp.resume.addcategory"}">
    <div class="panel panel-default">
    {if $bIsEdit}
        <div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
        <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
    {/if}
        <div class="panel-heading">
            {_p var='category_details'}
        </div>
        <div class="panel-body">
            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='admincp.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>