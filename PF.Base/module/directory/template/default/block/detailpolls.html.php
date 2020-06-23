<div id="yndirectory_business_detail_module_polls" class="yndirectory_business_detail_module_polls">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changePollsListFilter'
		result_div_id='js_ynd_polls_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='polls'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddPollsInBusiness}
		<div id="yndirectory_menu_button">
			<a class="btn btn-primary btn-sm" href="{$sUrlAddPolls}" id="yndirectory_add_new_item">{phrase var='add_new_poll'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_polls = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'polls');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_polls_list">

		{module name='directory.detailpollslist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
