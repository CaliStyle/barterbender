<div>
	<div id="employee_action_loader">
		{img theme='ajax/add.gif'}
	</div>
	<ul class="ynjp_view_participant">
		{foreach from=$pendingParticipant item=aPendingParticipant}
		<li id="employee_parent_{$aPendingParticipant.user_id}" class="ynjp_row_employee_loop">
			<div class="ynjp_employee_image">
				{img user=$aPendingParticipant suffix='_50_square' max_width=50 max_height=50}
			</div>
			<div class="ynjp_row_employee_name">
				<a href="{url link=''}{$aPendingParticipant.user_name}/">{$aPendingParticipant.full_name}</a>
			</div>
			<div id="employee_{$aPendingParticipant.user_id}" class="ynjp_row_employee_name buttonAction">
				<a class="accept" href="javascript:void();" onclick=" $('#employee_action_loader').show(); $.ajaxCall('jobposting.acceptWorkingCompany', 'type=company&userID={$aPendingParticipant.user_id}&companyID={$aCompany.company_id}'); return false;" title="{phrase var='em_accept'}"></a>
				<a class="reject" href="javascript:void();" onclick="reject_employee({$aPendingParticipant.user_id},{$aCompany.company_id}) " title="{phrase var='em_reject'}"></a>
			</div>
		</li>		
		{/foreach}

		{template file="jobposting.block.company.mini_participant_company"}
		<span id="view_more_employee"></span>
	</ul>
</div>
<div style="clear: both;"></div>
{if $ViewMore}
	<div id="href_view_more_employee">
		<a href="#" onclick="$.ajaxCall('jobposting.view_more_employee','iPage={$iPage}&company_id={$aCompany.company_id}');return false;">{phrase var='view_more'}</a>
	</div>
{/if}
{if count($aParticipant)==0 && count($pendingParticipant)==0}
	{phrase var='no_employees_found'}.
{/if}

{literal}
<script type="text/javascript">
	function reject_employee (user_id, company_id){		
		tb_show('{/literal}{phrase var='notice'}{literal}', $.ajaxBox('jobposting.showPopupConfirmYesNo', 'height=300&width=300&function=rejectWorkingCompany&type=company&company_id='+company_id+'&user_id='+user_id+'&phare=do_you_want_to_remove_this_user'));
		
		return false;
	}
</script>
{/literal}
