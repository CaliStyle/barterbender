<div class="ynauction-detail-bidhistory">
	{if $iPage == 1}
		 <div class="ynauction_trix_header">
			  <div class="section_title">
					<i class="fa fa-th-list"></i>
					{phrase var='offer_list'}
			  </div>
		 </div>
	{/if}
	{if count($aOfferList)}
<!--     <div class="ynecommerce_responsive_table">
		  {foreach from=$aOfferList key=iKey item=aOffer}
		  <div class="item-section">
				<div class="item-row">
					 <div class="item-label">{phrase var='bidder_name'}</div>
					 <div class="item-value">{$aOffer|user}</div></div>
				<div class="item-row">
					 <div class="item-label">{phrase var='offer_amount'}</div>
					 <div class="item-value price">{$aOffer.sSymbolCurrency}{$aOffer.auctionoffer_price|number_format:2}</div></div>
				<div class="item-row">
					 <div class="item-label">{phrase var='time'}</div>
					 <div class="item-value">{$aOffer.auctionoffer_creation_datetime}</div></div>
				<div class="item-row">
					 <div class="item-label">{phrase var='status'}</div>
					 <div class="item-value">{$aOffer.auctionoffer_status_text}</div></div>
				<div class="item-row">
					 <div class="item-label">{phrase var='option'}</div>
					 <div class="item-value action">{if $aOffer.auctionoffer_status == 0}
					 <span><a onclick="$.ajaxCall('auction.approveOffer', 'product_id={$aOffer.auctionoffer_product_id}&offer_id={$aOffer.auctionoffer_id}');">{phrase var='approve'}</a></span>
					 <span><a onclick="$.ajaxCall('auction.denyOffer', 'product_id={$aOffer.auctionoffer_product_id}&offer_id={$aOffer.auctionoffer_id}');">{phrase var='deny'}</a></span>
					 {/if}
					 <span><a onclick="{literal}$Core.composeMessage({user_id:{/literal}{$aAuction.user_id}{literal}});return false;{/literal}">{phrase var='send_message'}</a></span></div>
				</div>
				<div class="item-row">
					 <div class="item-label">{phrase var='day_left'}</div>
					 <div class="item-value">{$aOffer.auctionoffer_day_left}</div>
				</div>
		  </div>
		  {/foreach}
	 </div> -->
	 <div class="table-responsive">
		 <table class="ynecommerce_full_table table table-striped table-bordered">
			 {if $iPage == 1}
			  <thead>
				  <tr>
						<th class="ynauction-paddingright">{phrase var='bidder_name'}

						<span class="ynauction_column_sort_up_down">
							 <a class="up" href="{$orgLink}sortfield_name/sorttype_asc" {if $sSort == 'name_asc'}class='sort_active'{/if}></a>
							 <a class="down" href="{$orgLink}sortfield_name/sorttype_desc" {if $sSort == 'name_desc'}class='sort_active'{/if}></a>
						</span>
				  </th>
				  <th class="ynauction-paddingright">{phrase var='offer_amount'}
						<span class="ynauction_column_sort_up_down">
							 <a class="up" href="{$orgLink}sortfield_amount/sorttype_asc" {if $sSort == 'amount_asc'}class='sort_active'{/if}></a>
							 <a class="down" href="{$orgLink}sortfield_amount/sorttype_desc" {if $sSort == 'amount_desc'}class='sort_active'{/if}></a>
						</span>
				  </th>
				  <th class="ynauction-paddingright">{phrase var='time'}
						<span class="ynauction_column_sort_up_down">
							 <a class="up" href="{$orgLink}sortfield_time/sorttype_asc" {if $sSort == 'time_asc'}class='sort_active'{/if}></a>
							 <a class="down" href="{$orgLink}sortfield_time/sorttype_desc" {if $sSort == 'time_desc'}class='sort_active'{/if}></a>
						</span>
				  </th>
				  <th>{phrase var='status'}</th>
				  <th>{phrase var='option'}</th>
				  <th>{phrase var='day_left'}</th>
			 </tr>
			 </thead>
			 {/if}
			 <tbody>
			 {foreach from=$aOfferList key=iKey item=aOffer}
			 <tr id="js_row{$aOffer.auctionoffer_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
				  <td>{$aOffer|user}</td>
				  <td class="price">{$aOffer.sSymbolCurrency}{$aOffer.auctionoffer_price|number_format:2}</td>
				  <td>{$aOffer.auctionoffer_creation_datetime}</td>
				  <td>{$aOffer.auctionoffer_status_text}</td>
				  <td class="action">
						{if $aOffer.auctionoffer_status == 0}
						<span><a onclick="$.ajaxCall('auction.approveOffer', 'product_id={$aOffer.auctionoffer_product_id}&offer_id={$aOffer.auctionoffer_id}');">{phrase var='approve'}</a></span>
						<span><a onclick="$.ajaxCall('auction.denyOffer', 'product_id={$aOffer.auctionoffer_product_id}&offer_id={$aOffer.auctionoffer_id}');">{phrase var='deny'}</a></span>
						{/if}
						<span><a onclick="{literal}$Core.composeMessage({user_id:{/literal}{$aAuction.user_id}{literal}});return false;{/literal}">{phrase var='send_message'}</a></span>
				  </td>
				  <td>
						{$aOffer.auctionoffer_day_left}
				  </td>
			 </tr>
			 {/foreach}
		 </tbody>
	 </table>
	</div>
	 {pager}
	 {elseif $iPage <=1}
	 <div class="p_4">
		  {phrase var='no_offer_has_been_submited'}
	 </div>
	 {/if}
</div>