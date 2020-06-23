<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 1, 2020, 8:35 pm */ ?>
<?php echo '
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
'; ?>

<div id="ynauction_detail" class="main_break">
<?php if ('activities' == $this->_aVars['sView']): ?>
	<div class="ynauction_trix_header" id="ynauction_trix_header_activity">
		<div class="section_title">
			<i class="fa fa-th-list"></i>
<?php echo _p('activity_feed'); ?>
		</div>
	</div>
<?php Phpfox::getBlock('feed.display', array()); ?>
<?php endif; ?>
	
<?php if ('overview' == $this->_aVars['sView']): ?>
<?php Phpfox::getBlock('auction.detailoverview', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php Phpfox::getBlock('auction.other-auctions-from-this-seller', array()); ?>
<?php Phpfox::getBlock('auction.auctions-you-may-like', array()); ?>
<?php elseif ('shipping' == $this->_aVars['sView']): ?>
<?php Phpfox::getBlock('auction.detailshipping', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php Phpfox::getBlock('auction.other-auctions-from-this-seller', array()); ?>
<?php Phpfox::getBlock('auction.auctions-you-may-like', array()); ?>
<?php elseif ('bidhistory' == $this->_aVars['sView']): ?>
		<div id="auction-detail-history">
<?php Phpfox::getBlock('auction.detailbidhistory', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
		</div>
<?php elseif ('offerhistory' == $this->_aVars['sView']): ?>
<?php Phpfox::getBlock('auction.detailofferhistory', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php elseif ('chart' == $this->_aVars['sView']): ?>
<?php Phpfox::getBlock('auction.chart', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php elseif ('activities' == $this->_aVars['sView']): ?>
<?php Phpfox::getBlock('auction.detailactivities', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php elseif ('photos' == $this->_aVars['sView']): ?>
		<div class="ynauction_trix_header">
			<div class="section_title">
				<i class="fa fa-photo"></i>
<?php echo _p('photos'); ?> <?php if (isset ( $this->_aVars['iCountPhotos'] )): ?>(<?php echo $this->_aVars['iCountPhotos']; ?>)<?php endif; ?>
			</div>
		</div>
<?php Phpfox::getBlock('auction.detailphotos', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php elseif ('videos' == $this->_aVars['sView']): ?>
	<div class="ynauction_trix_header">
		<div class="section_title">
			<i class="fa fa-film"></i>
<?php echo _p('videos'); ?> <?php if (isset ( $this->_aVars['iCountVideos'] )): ?>(<?php echo $this->_aVars['iCountVideos']; ?>)<?php endif; ?>
		</div>
	</div>
<?php Phpfox::getBlock('auction.detailvideos', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php else: ?>
<?php Phpfox::getBlock('auction.detailoverview', array('aYnAuctionDetail' => $this->_aVars['aYnAuctionDetail'])); ?>
<?php Phpfox::getBlock('auction.other-auctions-from-this-seller', array()); ?>
<?php Phpfox::getBlock('auction.auctions-you-may-like', array()); ?>
<?php endif; ?>
</div>
