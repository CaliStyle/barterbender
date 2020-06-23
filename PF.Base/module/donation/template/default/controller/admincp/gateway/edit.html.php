<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.donation.gateway.edit'}">
	<div><input type="hidden" name="id" value="{$aForms.gateway_id}" /></div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
		        {phrase var='donation.gateway_details'}
            </div>
        </div>
	    <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='donation.title'}:</label>
                <input class="form-control" type="text" name="val[title]" id="title" value="{value type='input' id='title'}" size="40" />
            </div>
            <div class="form-group">
                <label for="">{phrase var='donation.description'}:</label>
                <textarea class="form-control" cols="50" rows="6" name="val[description]" id="description">{value type='textarea' id='description'}</textarea>
            </div>
            <div class="form-group">
                <label for="">{phrase var='admincp.active'}:</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {phrase var='admincp.yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {phrase var='admincp.no'}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="">{phrase var='donation.test_mode'}:</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_test]" value="1" {value type='radio' id='is_test' default='1' selected='true'}/> {phrase var='admincp.yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_test]" value="0" {value type='radio' id='is_test' default='0'}/> {phrase var='admincp.no'}</span>
                </div>
            </div>
        </div>
	
        <div class="panel-footer">
            <input type="submit" value="{phrase var='donation.update'}" class="btn btn-primary" />
        </div>
    </div>
</form>