<div id="ynauction_product_detail_module_photo" class="ynauction_product_detail_module_photo">	
	{module name='auction.filterinauction' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='auction.changePhotoListFilter'
		result_div_id='js_ynauction_photo_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='photos'
		hidden_productid=$aYnAuctionDetail.aAuction.product_id
		aYnAuctionDetail=$aYnAuctionDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddPhotoInAuction}

    <div class="ynauction_detail_section_menu">
        <div id="section_menu">
            <ul>
                <li>
                    <a href="{$sUrlAddPhoto}" id="ynauction_add_new_item">{phrase var='upload_a_new_image'}</a>
                </li>
            </ul>
        </div>
    </div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_ynauction_product_detail_module_photo = function(){
					ynauction.addAjaxForCreateNewItem({/literal}{$aYnAuctionDetail.aAuction.product_id}{literal}, 'photos');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynauction_photo_list">

		{module name='auction.detailphotolist' aYnAuctionDetail=$aYnAuctionDetail }
	</div>

</div>
