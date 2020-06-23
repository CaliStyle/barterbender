<div id="yndirectory_business_detail_module_event" class="yndirectory_business_detail_module_event">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeEventListFilter'
		result_div_id='js_ynd_event_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='events'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddEventInBusiness}
		<div id="yndirectory_menu_button">
			<a  class="btn btn-primary btn-sm" href="{$sUrlAddEvent}" id="yndirectory_add_new_item">{phrase var='create_new_event'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_event = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'events');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_event_list">

		{module name='directory.detaileventslist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
