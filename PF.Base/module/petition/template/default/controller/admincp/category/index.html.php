<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<form method="get" action="{url link="admincp.petition.category"}">
<div class="table_header">
	{phrase var='petition.search_filter'}
</div>
<div class="table">
	<div class="table_left">
		{phrase var='petition.search_for_text'}: 
	</div>
	<div class="table_right">
		{$aFilters.search}
	</div>
	<div class="clear"></div>
</div>
<div class="table">
	<div class="table_left">
		{phrase var='petition.display'}: 
	</div>
	<div class="table_right">
		{$aFilters.display}
	</div>
	<div class="clear"></div>
</div>
<div class="table">
	<div class="table_left">
		{phrase var='petition.sort'}: 
	</div>
	<div class="table_right">
		{$aFilters.sort} {$aFilters.sort_by}
	</div>
	<div class="clear"></div>
</div>
<div class="table_clear">
	<input type="submit" name="search[submit]" value="{phrase var='petition.submit'}" class="button" />
	<input type="submit" name="search[reset]" value="{phrase var='petition.reset'}" class="button" />	
</div>
</form>
{pager}
{if count($aCategories)}
{module name='help.info' phrase='petition.tip_delete_category'}
<form method="post" action="{url link='admincp.petition.category'}">
	<table>
	<tr>
		<th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
		<th>{phrase var='petition.name'}</th>
		<th>{phrase var='petition.created_date'}</th>
		<th>{phrase var='petition.total_petitions'}</th>
	</tr>
	{foreach from=$aCategories key=iKey item=aCategory}
	<tr id="js_row{$aCategory.category_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
		<td><input type="checkbox" name="id[]" class="checkbox" value="{$aCategory.category_id}" id="js_id_row{$aCategory.category_id}" /></td>
		<td>
			<a href="{url link='admincp.petition.category.add' id=$aCategory.category_id}">{$aCategory.name|convert|clean}</a>
		</td>
		<td>{$aCategory.added|date:'petition.petition_time_stamp'}</td>
		<td>{if $aCategory.used > 0}{$aCategory.used}{else}{phrase var='petition.none'}{/if}</td>
	</tr>
	{/foreach}
	</table>
	<div class="table_bottom">
		<input type="submit" name="delete" value="{phrase var='petition.delete_selected'}" class="sJsConfirm delete button sJsCheckBoxButton disabled" disabled="true" />
	</div>
	{else}
	<div class="p_4">
		{phrase var='petition.no_petition_categories_have_been_created'} <a href="{url link='admincp.petition.category.add'}">{phrase var='petition.create_one_now'}</a>.
	</div>
	{/if}
</form>

{pager}