<?php
?>
<div>
	<form method="post" action="{url link='admincp.advancedmarketplace.addcustomgroup'}" id="js_group_form">
		<div class="table_header">Group details</div>
		{if $bIsEdit}
		<div><input type="hidden" name="id" value="{$aForms.group_id}" /></div>
		{/if}
		<div class="table">
			<div class="table_left">
				{required}Group Name:
			</div>
			<div class="table_right">
				{if $bIsEdit}
					{module name='language.admincp.form' type='text' id='group' value=$aForms.group}
				{else}
					{module name='language.admincp.form' type='text' id='group'}
				{/if}
			</div>
			<div class="clear"></div>
		</div>
		{if !$bIsEdit}
			<div class="table">
				<div class="table_left">
				{required}<label for="category">{phrase var='advancedmarketplace.category'}:</label>
				</div>
				<div class="table_right">
					{$sCategories}
				</div>
			</div>
		{/if}
		<div class="table_clear">
			<input type="submit" value="{phrase var='advancedmarketplace.submit'}" class="button" />
		</div>
	</form>
</div>