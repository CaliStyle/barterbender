<div id="yndirectory_business_detail_module_job" class="yndirectory_business_detail_module_job">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeJobListFilter'
		result_div_id='js_ynd_job_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='jobs'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddJobInBusiness}
		<div id="yndirectory_menu_button">
			<a  class="btn btn-primary btn-sm" href="{$sUrlAddJob}" id="yndirectory_add_new_item">{phrase var='create_a_new_job'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_job = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'jobs');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_job_list">

		{module name='directory.detailjobslist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
