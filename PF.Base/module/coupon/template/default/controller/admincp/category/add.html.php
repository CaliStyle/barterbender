<?php 
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.coupon.category.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
{/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='coupon_category_detail'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='parent_category'}:</label>
                <select name="val[parent_id]" class="form-control">
                    <option value="">{phrase var='select'}:</option>
                    {$sOptions}
                </select>
            </div>
            {foreach from=$aLanguages item=aLanguage}
            <div class="form-group">
                <label for="">{required} {phrase var='title'}&nbsp;{$aLanguage.title}:</label>
                {assign var='value_name' value="name_"$aLanguage.language_id}
                <input class="form-control" type="text" maxlength="40" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" />
            </div>
            {/foreach}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>