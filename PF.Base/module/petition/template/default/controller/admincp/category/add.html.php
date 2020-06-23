<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link="admincp.petition.category.add"}" id="js_form">
	{if $bIsEdit}
		<div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
		<div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
	{/if}
	<div class="table_header">
		{phrase var='petition.category_details'}
	</div>
	{foreach from=$aLanguages item=aLanguage}
	<div class="table form-group">
		<div class="table_left">
			{required} {phrase var='title'}&nbsp;<strong>{$aLanguage.title}</strong>:
		</div>
		<div class="table_right">
			{assign var='value_name' value="name_"$aLanguage.language_id}
			<input type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" />
		</div>
		<div class="clear"></div>
	</div>
	{/foreach}
	<div class="table_clear">
		<input type="submit" value="{phrase var='petition.submit'}" class="button" />
	</div>
</form>