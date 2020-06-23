<?php 
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */
?>
<!-- Category Add Form Layout -->
<form method="post" action="{url link="admincp.foxfeedspro.addcategory"}" id="js_form">
	<!-- Category ID for Edit Mode -->
	{if $bIsEdit}
		<div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
		<div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
	{/if}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='foxfeedspro.category_details'}
            </div>
        </div>
        <div class="panel-body">
           {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=40}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='admincp.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>