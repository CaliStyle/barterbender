<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 1163 2009-10-09 08:02:14Z Anna_Eliasson $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{module name='foxfeedspro.js-block-remove-add-button'}
{literal}
<style type="text/css">
	.js_box_content ul.action  li:nth-child(2)
	{
		display:none;
	}
</style>
{/literal}
{if !$bIsAddNews}
	{literal}
		<script type="text/javascript">
			$(document).ready(function(){
				$('.breadcrumbs_menu')[0].find('li:first').hide();
			});
		</script>
	{/literal}
{/if}
{if !$bIsAddFeed}
	{literal}
		<script type="text/javascript">
			$(document).ready(function(){
				$('.breadcrumbs_menu')[0].find('li:first').next().hide();
			});
		</script>
	{/literal}
{/if}
{literal}
<form class="form-add-categories" method="post" action="{url link='foxfeedspro.add'}{if $bIsEdit}id_{$aForms.category_id}{/if}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
{/if}
    <div class="form-group">
        <label for="">{required}{phrase var='foxfeedspro.category_name'}:</label>
        <input type="text" class="form-control" name="val[name]" size="30" maxlength="100" value="{value type='input' id='name'}" />
    </div>
    <div class="form-group">
        <label for="">{phrase var='foxfeedspro.parent_category'}:</label>
        <select class="form-control" name="val[parent_id]">
            <option value="">{phrase var='foxfeedspro.select'}:</option>
            {$sOptions}
        </select>
    </div>
    <div class="">
        <input type="submit" value="{phrase var='foxfeedspro.submit'}" class="btn btn-primary btn-sm" />
    </div>
</form>