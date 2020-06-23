<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="auctions_ending_today" class="">
    <ul>
        {foreach from=$aAuctionsEndingToday item=aAuction}
            <li>
                <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}">
                    <i class="fa fa-bookmark"></i>
                    {$aAuction.name|clean|shorten:25:'...'|split:25}
                </a>
                <div class="auction_date_time">
                    {phrase var='end'}: {$aAuction.end_time|date:'core.conver_time_to_string'}
                </div>
            </li>
        {/foreach}
        {if $bShowViewMoreEndingTodayAuctions}
	        <div class="auction_view_more">
	            <a href="{url link='auction' view='endtoday'}" class="btn btn-sm btn-success" title="{phrase var='view_more'}">
                    {phrase var='view_more'}
                </a>
	        </div>
	    {/if}
    </ul>
</div>