{foreach from=$aParticipant item=aParticipant}
<li id="employee_parent_{$aParticipant.user_id}" class="ynjp_row_employee_loop">
	<div class="ynjp_employee_image">
		{img user=$aParticipant suffix='_50_square' max_width=50 max_height=50}
	</div>
	<div class="ynjp_row_employee_name">
		<a href="{url link=''}{$aParticipant.user_name}/">{$aParticipant.full_name}</a>
	</div>
	{if $isCompanyOwnerOrAdmin}
		<div id="employee_{$aParticipant.user_id}" class="ynjp_row_employee_name buttonAction">
			<a class="remove" href="javascript:void();" onclick="remove_employee({$aParticipant.user_id},{$aCompany.company_id})" title="{phrase var='em_remove'}">
				<i class="fa fa-trash-o"></i>
			</a>
		</div>
	{/if}
</li>		
{/foreach}

{literal}
<script type="text/javascript">
	function remove_employee (user_id, company_id){		
		tb_show('{/literal}{phrase var='notice'}{literal}', $.ajaxBox('jobposting.showPopupConfirmYesNo', 'height=300&width=300&function=removeWorkingCompany&type=company&company_id='+company_id+'&user_id='+user_id+'&phare=do_you_want_to_remove_this_user'));
		
		return false;
		//$('#employee_action_loader').show(); $.ajaxCall('jobposting.removeWorkingCompany', 'type=company&userID={$aParticipant.user_id}&companyID={$aCompany.company_id}'); return false;
	}
</script>
{/literal}