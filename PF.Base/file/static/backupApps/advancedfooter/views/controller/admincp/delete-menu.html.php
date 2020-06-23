<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.advancedfooter.delete-menu'}">
    <div class="panel panel-body">
    <div><input type="hidden" name="delete" value="{$iDeleteId}" /></div>
    <div class="alert alert-warning">
        {_p('Are you sure you want to delete this menu')}
    </div>
        <div><input type="hidden" name="val[delete_type]" value="0" /></div>
    </div>
    <div class="panel-footer">
        <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
        <input onclick="return js_box_remove(this);" type="submit" value="{_p('Cancel')}" class="btn btn-default" />
    </div>
</form>