<div id="yndirectory_business_detail_module_follower" class="yndirectory_business_detail_module_follower">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeFollowerListFilter'
		result_div_id='js_ynd_follower_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='followers'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	
	<div id="js_ynd_follower_list">
		{module name='directory.detailfollowerslists'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>
</div>
