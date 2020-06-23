<div class="ynauction-detail-bidhistory">
	{if $iPage == 1}
    <div class="ynauction_trix_header">
        <span class="section_title">
            <i class="fa fa-th-list"></i>
            {phrase var='bid_history'}
        </span>
    </div>
	<input type="hidden" id='ynauction_product_id' name='ynauction_product_id' value="{$aAuction.product_id}" />
	{/if}
    {if count($aBidHistory)}
    	{if $iPage == 1}
        <div class="detail_bid_history_stats">
            <div class="stat_bidder">
                <div class="stat_content">
                <div class="stat_label">
                    {phrase var='bidder'}(s)
                </div>
                    <div class="stat_value">
                        {$iCountBidder}
                    </div>
                </div>
            </div>
            <div class="stat_counter">
                <div class="stat_content">
                    <div class="stat_label">
                        {phrase var='bid_s'}
                    </div>
                    <div class="stat_value">
                        {$iCountBid}
                    </div>
                </div>
            </div>
        </div>

        <button id="refresh-bidder" type="submit" class="btn btn-sm btn-success" onclick="refreshBidder();">{phrase var='refresh'}</button>
        <input id="refresh-auction-id" type="hidden" value="{$aAuction.product_id}">
        <div id="refresh-bidder-loading" style="display: none;">
			{img theme="ajax/small.gif"}
		</div>
		{/if}

        <div class="table-responsive">
            <table class="ynecommerce_full_table table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="ynauction-paddingright">{phrase var='bidder_name'}
                            <span class="ynauction_column_sort_up_down">
                                <a class="up" href="{$orgLink}sortfield_name/sorttype_asc" {if $sSort == 'name_asc'}class='sort_active'{/if}></a>
                                <a class="down"  href="{$orgLink}sortfield_name/sorttype_desc" {if $sSort == 'name_desc'}class='sort_active'{/if}></a>
                            </span>
                    </th>
                    <th class="ynauction-paddingright">{phrase var='bid_amount'}

                        <span class="ynauction_column_sort_up_down">
                            <a class="up" href="{$orgLink}sortfield_amount/sorttype_asc" {if $sSort == 'amount_asc'}class='sort_active'{/if}></a>
                            <a class="down" href="{$orgLink}sortfield_amount/sorttype_desc" {if $sSort == 'amount_desc'}class='sort_active'{/if}></a>
                        </span>
                    </th>
                    <th class="ynauction-paddingright">{phrase var='bid_time'}
                        <span class="ynauction_column_sort_up_down">
                            <a class="up" href="{$orgLink}sortfield_time/sorttype_asc" {if $sSort == 'time_asc'}class='sort_active'{/if}></a>
                            <a class="down"  href="{$orgLink}sortfield_time/sorttype_desc" {if $sSort == 'time_desc'}class='sort_active'{/if}></a>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
        {foreach from=$aBidHistory key=iKey item=aBid}
            <tr id="js_row{$aBid.auctionbid_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                <td>{$aBid|user}</td>
                <td class="price">{$aBid.sSymbolCurrency}{$aBid.auctionbid_price|number_format:2}</td>
                <td>{$aBid.auctionbid_creation_datetime}</td>
            </tr>
        {/foreach}
            </tbody>
        </table>
        </div>
        {pager}
        {elseif $iPage <=1 }
        <div class="p_4">
            {phrase var='no_bid_has_been_submited'}
        </div>
        {/if}
</div>

{literal}
<script type="application/javascript">
	function refreshBidder()
	{
		$('#refresh-bidder').hide();
		$('#refresh-bidder-loading').show();
		var id = $('#refresh-auction-id').val();
		$.ajaxCall('auction.refreshBidder', 'url=' + window.location.href  + '&id=' + id );
	}
</script>
{/literal}
