<div id="yndirectory_business_detail_module_member" class="yndirectory_business_detail_module_member">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeMemberListFilter'
		result_div_id='js_ynd_member_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='members'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	
	<div id="js_ynd_member_list">
		{module name='directory.detailmemberslists'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>
</div>
