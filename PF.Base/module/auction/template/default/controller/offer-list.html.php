
<div id="ynauction_offerlist" class="main_break">
	{if count($aOfferList)}
	 <div class="table-responsive">
	<table class="ynecommerce_full_table table table-striped table-bordered">
		{if $iPage == 1}
		<thead>
		<tr>
			<th class="ynauction-paddingright">{phrase var='bidder_name'}
				<span class="ynauction_column_sort_up_down">
					<a class="up" href="{$orgLink}sortfield_name/sorttype_asc" {if $sSort== 'name_asc'}class='sort_active'{/if}></a>
					<a class="down" href="{$orgLink}sortfield_name/sorttype_desc" {if $sSort== 'name_desc'}class='sort_active'{/if}></a>
				</span>
			</th>
			<th class="ynauction-paddingright">{phrase var='offer_amount'}
				<span class="ynauction_column_sort_up_down"><a class="up" href="{$orgLink}sortfield_amount/sorttype_asc"
															   {if $sSort== 'amount_asc'}class='sort_active'{/if}></a>
					<a class="down" href="{$orgLink}sortfield_amount/sorttype_desc" {if $sSort== 'amount_desc'}class='sort_active'{/if}></a></span>
			</th>
			<th class="ynauction-paddingright">{phrase var='time'}
				<span class="ynauction_column_sort_up_down">
					<a class="up" href="{$orgLink}sortfield_time/sorttype_asc" {if $sSort== 'time_asc'}class='sort_active'{/if}></a>
					<a class="down" href="{$orgLink}sortfield_time/sorttype_desc" {if $sSort== 'time_desc'}class='sort_active'{/if}></a>
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
			<td>{$aOffer.sSymbolCurrency}{$aOffer.auctionoffer_price|number_format:2}</td>
			<td>{$aOffer.auctionoffer_creation_datetime}</td>
			<td>{$aOffer.auctionoffer_status_text}</td>
			<td>
				{if $aOffer.auctionoffer_status == 0}
				<span class="ynauction-separate"><a
						onclick="$.ajaxCall('auction.approveOffer', 'product_id={$aOffer.auctionoffer_product_id}&offer_id={$aOffer.auctionoffer_id}');">{phrase
						var='auction.approve'}</a></span>
				 <span class="ynauction-separate"><a
						onclick="$.ajaxCall('auction.denyOffer', 'product_id={$aOffer.auctionoffer_product_id}&offer_id={$aOffer.auctionoffer_id}');">{phrase
						var='auction.deny'}</a></span>
				{/if}
				 <span class="ynauction-separate">
					 <a onclick="{literal}$Core.composeMessage({user_id:{/literal}{$aAuction.user_id}{literal}});return false;{/literal}">
						 {phrase var='send_message'}</a>
				 </span>
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
	{else}
	<div class="p_4">
		{phrase var='no_offer_has_been_submited'}
	</div>
	{/if}
</div>