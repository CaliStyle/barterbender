{if count($aEmployee)>0}
{foreach from=$aEmployee  name=employee item=aItem}
<div class="ynjp_row_employee_loop">
	<div class="ynjp_employee_image">
		{img user=$aItem suffix='_50_square' max_width=50 max_height=50}
	</div>
	<div class="ynjp_row_employee_name">
		<a href="{url link=''}{$aItem.user_name}/"> {$aItem.full_name} </a>
	</div>
</div>	
{/foreach}
{/if}