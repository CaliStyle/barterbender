
<div id="ynauction_bidhistory" class="main_break">
	{if $iPage == 1}
	 <input type="hidden" id='ynauction_product_id' name='ynauction_product_id' value="{$aAuction.product_id}"/>
	 <div id="ynd_range_of_dates_picker" class="bid_history_search_form">
	 	<div class="ynauction-time-start">
		  <div class="item_label">{phrase var='start'} : </div>
		  <div class="item_date">
				{select_date prefix='start_' id='_begin_time' start_year='2000' end_year='+10' field_separator=' / '
				field_order='MDY' }
		  </div>
		</div>
		<div class="ynauction-time-end">
		  <div class="item_label">{phrase var='end'} : </div>
		  <div class="item_date">
				{select_date prefix='end_' id='_begin_time' start_year='2000' start_day='1' start_month='1'
				end_year='+10' field_separator=' / ' field_order='MDY' }
		  </div>
		</div>
		  <div class="item_submit">
				<button class="btn btn-sm btn-primary" type="button" id='filter_chart' >{phrase var='go_to_chart'}</button>
		  </div>
	 </div>
	 <br />
	 <div class="ynauction-demo-container">
		  <div id="placeholder" class="demo-placeholder" style="width:600px;height:350px;"></div>
	 </div>
	 {/if}
	 <br />
	 {if count($aBidHistory)}
		 <!-- <div class="ynecommerce_responsive_table">
			  {foreach from=$aBidHistory key=iKey item=aBid}
			  <div class="item-section">
					<div class="item-row">
						 <div class="item-label">{phrase var='bidder_name'}</div>
						 <div class="item-value">{$aBid|user}</div></div>
					<div class="item-row">
						 <div class="item-label">{phrase var='bid_amount'}</div>
						 <div class="item-value price">{$aBid.sSymbolCurrency}{$aBid.auctionbid_price|number_format:2}</div></td>
					<div class="item-row">
						 <div class="item-label">{phrase var='bid_time'}</div>
						 <div class="item-value">{$aBid.auctionbid_creation_datetime}</div></div>
			  </div>
			  {/foreach}
		 </div> -->
			<div class="table-responsive">
				<table class="ynecommerce_full_table table table-striped table-bordered">
					<thead>
						<tr>
							<th class="ynauction-paddingright">
								{phrase var='bidder_name'}
								<span class="ynauction_column_sort_up_down">
									<a class="up" href="{$orgLink}sortfield_name/sorttype_asc" {if $sSort== 'name_asc'}class='sort_active'{/if}></a>
									<a class="down" href="{$orgLink}sortfield_name/sorttype_desc" {if $sSort== 'name_desc'}class='sort_active'{/if}></a>
								</span>
							</th>
							<th class="ynauction-paddingright">
								{phrase var='bid_amount'}
								<span class="ynauction_column_sort_up_down">
									<a class="up" href="{$orgLink}sortfield_amount/sorttype_asc" {if $sSort== 'amount_asc'}class='sort_active'{/if}></a>
									<a class="down" href="{$orgLink}sortfield_amount/sorttype_desc" {if $sSort== 'amount_desc'}class='sort_active'{/if}></a>
								</span>
							</th>
							<th class="ynauction-paddingright">
								{phrase var='bid_time'}
								<span class="ynauction_column_sort_up_down">
									<a class="up" href="{$orgLink}sortfield_time/sorttype_asc" {if $sSort== 'time_asc'}class='sort_active'{/if}></a>
									<a class="down" href="{$orgLink}sortfield_time/sorttype_desc" {if $sSort== 'time_desc'}class='sort_active'{/if}></a>
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$aBidHistory key=iKey item=aBid}
						<tr id="js_row{$aBid.auctionbid_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
							<td>{$aBid|user}</td>
							<td class="price">
								{$aBid.sSymbolCurrency}{$aBid.auctionbid_price|number_format:2}
							</td>
							<td>{$aBid.auctionbid_creation_datetime}</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		 {pager}
		 {else}
		 <div class="p_4">
			  {phrase var='no_bid_has_been_submited'}
		 </div>
		 {/if}
	</div>
{literal}
<script type="text/javascript">
	 ;
	 $Behavior.ynauction_load_chart = function () {
		ynauction.initChart();
	 }
	 ;

</script>
{/literal}
