<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($aAuction.product_id)}
<!-- <div class="ynecommerce_responsive_table">
	{foreach from=$aRows item=aRow}
	<div class="item-section">
		<div class="item-row">
			<div class="item-label">{phrase var='bidder_name'}</div>
			<div class="item-value">
				<a href="{url link=$aRow.user_name}" title="{$aRow.full_name|clean}">{$aRow.full_name|clean|shorten:75:'...'|split:75}</a>
			</div>
		</div>
		<div class="item-row">
			<div class="item-label">{phrase var='bid_amount'}</div>
			<div class="item-value price">
				{$aRow.sSymbolCurrency}{$aRow.auctionbid_price|number_format:2}
			</div>
		</div>
		<div class="item-row">
			<div class="item-label">{phrase var='bid_time'}</div>
			<div class="item-value">
				{$aRow.auctionbid_creation_datetime|date:'core.global_update_time'}
			</div>
		</div>
	</div>
	{/foreach}
</div> -->
<div class="table-responsive">
	<table class="ynecommerce_full_table table table-striped table-bordered">
		<thead>
			<tr>
				<th class="bidder_name ynauction-paddingright">
					{phrase var='bidder_name'}
					<div class="ynauction_column_sort_up_down">
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aAuction.product_id}&page=1&sort=full-name-asc');" class="up"></a>
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aAuction.product_id}&page=1&sort=full-name-desc');" class="down"></a>
					</div>
				</th>
				<th class="bid_amount ynauction-paddingright">
					{phrase var='bid_amount'}
					<div class="ynauction_column_sort_up_down">
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aAuction.product_id}&page=1&sort=price-asc');" class="up"></a>
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aAuction.product_id}&page=1&sort=price-desc');" class="down"></a>
					</div>
				</th>
				<th class="bid_time ynauction-paddingright">
					{phrase var='bid_time'}
					<div class="ynauction_column_sort_up_down">
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aAuction.product_id}&page=1&sort=time-asc');" class="up"></a>
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aAuction.product_id}&page=1&sort=time-desc');" class="down"></a>
					</div>
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$aRows item=aRow}
			<tr>
				<td class="bidder_name">
					<a href="{url link=$aRow.user_name}" title="{$aRow.full_name|clean}">{$aRow.full_name|clean|shorten:75:'...'|split:75}</a>
				</td>
				<td class="bid_amount price">
					{$aRow.sSymbolCurrency}{$aRow.auctionbid_price|number_format:2}
				</td>
				<td class="bid_time">
					{$aRow.auctionbid_creation_datetime|date:'core.global_update_time'}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>

{$sCustomPagination}
{else}
	<div class="error_message">{phrase var='auction_is_not_valid'}</div>
{/if}