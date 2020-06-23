<div id="yndirectory_business_detail_module_coupon" class="yndirectory_business_detail_module_coupon">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeCouponListFilter'
		result_div_id='js_ynd_coupon_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='coupons'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	
	{if $bCanAddCouponInBusiness}
		<div id="yndirectory_menu_button">
			<a class="btn btn-primary btn-sm" href="{$sUrlAddCoupon}" id="yndirectory_add_new_item">{phrase var='create_a_new_coupon'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_coupon = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'coupons');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_coupon_list">

		{module name='directory.detailcouponslist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
