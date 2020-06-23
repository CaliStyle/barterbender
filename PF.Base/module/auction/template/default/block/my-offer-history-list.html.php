<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($aProduct.product_id)}
	<!-- <div class="ynecommerce_responsive_table">
	  {foreach from=$aRows item=aRow}
		<div class="item-section">
			<div class="item-row">
				<div class="item-label">{phrase var='offer_amount'}</div>
				<div class="item-value price">{$aRow.sSymbolCurrency}{$aRow.auctionoffer_price|number_format:2}</div>
			</div>
			<div class="item-row">
				<div class="item-label">{phrase var='time'}</div>
				<div class="item-value">{$aRow.auctionoffer_creation_datetime|date:'core.global_update_time'}</div>
			</div>
			<div class="item-row">
				<div class="item-label">{phrase var='status'}</div>
				<div class="item-value">{$aRow.status_title}</div>
			</div>
			<div class="item-row">
				<div class="item-label">{phrase var='option'}</div>
				<div class="item-value action">
					<div class="offer_send_message">
								<a href="javascript:;" onclick="tb_remove(); setTimeout(function(){l} tb_show('', $.ajaxBox('mail.compose', 'height=300&width=500&id={$aProduct.user_id}&no_remove_box=true')); {r}, 1000);">{phrase var='send_message'}</a>
					</div>
					{if $aRow.auctionoffer_status == 1}
						<div class="add_to_cart_function">
							<button id="add_to_cart_button_{$aProduct.product_id}" type="button" name="add_to_cart" class="btn btn-sm btn-success" onclick="addToCart({$aProduct.product_id},{$aRow.auctionoffer_id});">{phrase var='add_to_cart'}</button>
							<div id="add_to_cart_loading_{$aProduct.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
						</div>
					{/if}
				</div>
			</div>
		</div>
	  {/foreach}
	</div> -->
<div class="table-responsive">
	<table class="ynecommerce_full_table table table-striped table-bordered">
		<thead>
			<tr>
				<th class="offer_amount ynauction-paddingright">
					{phrase var='offer_amount'}
					<div class="ynauction_column_sort_up_down">
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aProduct.product_id}&page=1&sort=price-asc');" class="up"></a>
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aProduct.product_id}&page=1&sort=price-desc');" class="down"></a>
					</div>
				</th>
				<th class="offer_time ynauction-paddingright">
					{phrase var='time'}
					<div class="ynauction_column_sort_up_down">
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aProduct.product_id}&page=1&sort=time-asc');" class="up"></a>
						<a href="javascript:;" onclick="paginationAjaxReload('id={$aProduct.product_id}&page=1&sort=time-desc');" class="down"></a>
					</div>
				</th>
				<th class="offer_status">{phrase var='status'}</th>
				<th class="offer_options">{phrase var='option'}</th>
			</tr>
		</thead>
		<tbody class="body">
			{foreach from=$aRows item=aRow}
			<tr>
				<td class="offer_amount price">
					{$aRow.sSymbolCurrency}{$aRow.auctionoffer_price|number_format:2}
				</td>
				<td class="offer_time">
					{$aRow.auctionoffer_creation_datetime|date:'core.global_update_time'}
				</td>
				<td class="offer_status">{$aRow.status_title}</td>
				<td class="offer_options">
					<div class="offer_send_message">
						<a href="javascript:;" onclick="$Core.composeMessage({l}user_id: {$aProduct.user_id}{r}); return false;">{phrase var='send_message'}</a>
					</div>
					{if $aRow.auctionoffer_status == 1}
					<div class="add_to_cart_function">
						<button id="add_to_cart_button_{$aProduct.product_id}" type="button" name="add_to_cart" class="btn btn-sm btn-success" onclick="addToCart({$aProduct.product_id},{$aRow.auctionoffer_id});">{phrase var='add_to_cart'}</button>
						<div id="add_to_cart_loading_{$aProduct.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
					</div>
					{/if}
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