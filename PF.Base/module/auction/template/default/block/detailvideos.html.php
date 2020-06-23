<div id="ynauction_product_detail_module_video" class="ynauction_product_detail_module_video">
	{module name='auction.filterinauction' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='auction.changeVideoListFilter'
		result_div_id='js_ynauction_video_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='videos'
		hidden_productid=$aYnAuctionDetail.aAuction.product_id
		aYnAuctionDetail=$aYnAuctionDetail
		
		hidden_select=$hidden_select

	}
	{if $bCanAddVideoInAuction}
        <div class="ynauction_detail_section_menu">
            <div id="section_menu">
                <ul>
                    <li>
                        <a href="{$sUrlAddVideo}" id="ynauction_add_new_item" class="p10">{phrase var='upload_share_a_video'}</a>
                    </li>
                </ul>
            </div>
        </div>

		{literal}
			<script type="text/javascript">
				;$Behavior.init_ynauction_product_detail_module_video = function(){
					ynauction.addAjaxForCreateNewItem({/literal}{$aYnAuctionDetail.aAuction.product_id}{literal}, 'videos');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynauction_video_list">
		{module name='auction.detailvideolist' aYnAuctionDetail=$aYnAuctionDetail}
	</div>

</div>
