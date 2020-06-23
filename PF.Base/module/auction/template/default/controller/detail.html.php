{literal}
<style>
	.feed_sort_order{
		display: none !important;
	}
	#js_main_feed_holder{
		display: none;
	}
	#js_feed_content{
		display: none;
	}
	#js_block_border_feed_display .title{
		display: none;
	}
	.ym-feed-header{
		display: none;	
	}
	div.activity_feed_content_info img{
  	 	display: none;
    }
   
   div.activity_feed_content_info span:nth-of-type(2){
    	display: none;
    }
</style>
{/literal}
<div id="ynauction_detail" class="main_break">
	{if 'activities' == $sView}
	<div class="ynauction_trix_header" id="ynauction_trix_header_activity">
		<div class="section_title">
			<i class="fa fa-th-list"></i>
			{phrase var='activity_feed'}
		</div>
	</div>
	{module name='feed.display'}
	{/if}
	
	{if 'overview' == $sView}
		{module name='auction.detailoverview' aYnAuctionDetail=$aYnAuctionDetail}
		{module name='auction.other-auctions-from-this-seller'}
    	{module name='auction.auctions-you-may-like'}
	{elseif 'shipping' == $sView}
		{module name='auction.detailshipping' aYnAuctionDetail=$aYnAuctionDetail}
		{module name='auction.other-auctions-from-this-seller'}
    	{module name='auction.auctions-you-may-like'}
	{elseif 'bidhistory' == $sView}
		<div id="auction-detail-history">
			{module name='auction.detailbidhistory' aYnAuctionDetail=$aYnAuctionDetail}
		</div>
	{elseif 'offerhistory' == $sView}
		{module name='auction.detailofferhistory' aYnAuctionDetail=$aYnAuctionDetail}
	{elseif 'chart' == $sView}
		{module name='auction.chart' aYnAuctionDetail=$aYnAuctionDetail}
	{elseif 'activities' == $sView}
		{module name='auction.detailactivities' aYnAuctionDetail=$aYnAuctionDetail}
	{elseif 'photos' == $sView}
		<div class="ynauction_trix_header">
			<div class="section_title">
				<i class="fa fa-photo"></i>
				{phrase var='photos'} {if isset($iCountPhotos)}({$iCountPhotos}){/if}
			</div>
		</div>
		{module name='auction.detailphotos' aYnAuctionDetail=$aYnAuctionDetail}
	{elseif 'videos' == $sView}
	<div class="ynauction_trix_header">
		<div class="section_title">
			<i class="fa fa-film"></i>
			{phrase var='videos'} {if isset($iCountVideos)}({$iCountVideos}){/if}
		</div>
	</div>
		{module name='auction.detailvideos' aYnAuctionDetail=$aYnAuctionDetail}
	{else}
		{module name='auction.detailoverview' aYnAuctionDetail=$aYnAuctionDetail}	
		{module name='auction.other-auctions-from-this-seller'}
    	{module name='auction.auctions-you-may-like'}
	{/if}
</div>