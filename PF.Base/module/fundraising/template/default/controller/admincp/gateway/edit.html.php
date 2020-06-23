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
<form method="post" action="{url link='admincp.fundraising.gateway.edit'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='gateway_details'}
            </div>
        </div>
        <div class="panel-body">
            <div>
                <div><input type="hidden" name="id" value="{$aForms.gateway_id}" /></div>
            </div>
            <div class="form-group">
                <label for="title">{required}{phrase var='title'}:</label>
                <input class="form-control" type="text" name="val[title]" id="title" value="{value type='input' id='title'}" size="40" required>
            </div>
            <div class="form-group">
                <label for="description">{phrase var='description'}:</label>
                <textarea class="form-control" cols="50" rows="6" name="val[description]" id="description">{value type='textarea' id='description'}</textarea>
            </div>
            <div class="form-group">
                <label>{phrase var='admincp.active'}:</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {phrase var='admincp.yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {phrase var='admincp.no'}</span>
                </div>
            </div>
            <div class="form-group">
                <label>{phrase var='test_mode'}:</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_test]" value="1" {value type='radio' id='is_test' default='1' selected='true'}/> {phrase var='admincp.yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_test]" value="0" {value type='radio' id='is_test' default='0'}/> {phrase var='admincp.no'}</span>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{phrase var='update'}</button>
        </div>
    </div>
</form>