<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 2197 2010-11-22 15:26:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_menu_drop_down" style="display:none;">
	<div class="link_menu dropContent" style="display:block;">
		<ul>
			<li><a href="#" onclick="return $Core.advancedmarketplace.action(this, 'edit');">{phrase var='advancedmarketplace.edit'}</a></li>
			<li><a href="#" onclick="return $Core.advancedmarketplace.action(this, 'delete');">{phrase var='advancedmarketplace.delete'}</a></li>
			<li><a href="#" onclick="return $Core.advancedmarketplace.action(this, 'manage_customfield');">{phrase var='advancedmarketplace.admin_menu_manage_custom_fields'}</a></li>
		</ul>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='advancedmarketplace.admin_menu_manage_custom_fields'}
        </div>
    </div>
    <form method="post" action="{url link='admincp.advancedmarketplace'}">
        <div class="panel-body">
            <div class="table table-bordered">
                <div class="sortable">
                    {$sCategories}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='advancedmarketplace.update_order'}" class="btn btn-primary" />
        </div>
    </form>
</div>