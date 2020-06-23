<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_detailvideolist extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnAuctionDetail  = $this->getParam('aYnAuctionDetail');
		
		//check auth
		if (Phpfox::isModule('privacy'))
		{
			$aAuction = $aYnAuctionDetail['aAuction'];
			Phpfox::getService('privacy')->check('auction', $aAuction['product_id'], $aAuction['user_id'], $aAuction['privacy_video'], $aAuction['is_friend']);
		}
		
		$sType = 'videos';

		$aCore = $this->request()->get('core');
		$iItemPerPage = 5;
		$iPage = 1;
		$aConds = array(' 1=1 ');
		$aExtra['order'] = 'm.time_stamp DESC';

		$sOrderingField = '';
		$sOrderingType = '';
		$sModuleId = Phpfox::getService('auction.helper')->getModuleIdVideo();
		$hidden_select = '';

		if($aVals = $this->getParam('aQueryParam')) {
			$hidden_select = $aVals['hidden_select'];
			if(isset($aVals['keyword']) && $aVals['keyword']) {
				$sKeywordParse = Phpfox::getLib('parse.input')->clean($aVals['keyword']);
				$aConds[] = 'm.title like \'%' . $sKeywordParse . '%\' ';
			}

			if(isset($aVals['filterinauction_when']) && $aVals['filterinauction_when']) {
				$iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
				$field = 'm.time_stamp';
				switch ($aVals['filterinauction_when'])
				{
					case 'today':					
						$iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));											
						$aConds[] = '  (' . $field . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND ' . $field . ' < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')';
						break;
					case 'this_week':
						$aConds[] = '  ' . $field . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart()) . '\'';
						$aConds[] = '  ' . $field . ' <= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd()) . '\'';
						break;
					case 'this_month':
						$aConds[] = '  ' .$field . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'';
						$iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'), Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
						$aConds[] = '  ' . $field . ' <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'';
						break;		
					// case 'upcoming':
					// 	break;
					default:							
						break;			
				}
			}

			if(isset($aVals['filterinauction_sort']) && $aVals['filterinauction_sort']) {
				switch ($aVals['filterinauction_sort']){
					case 'latest': 
						$aExtra['order'] = "m.video_id DESC";
						break;
					case 'most_viewed': 
						$aExtra['order'] = "m.total_view DESC";
						break;
					case 'most_liked': 
						$aExtra['order'] = "m.total_like DESC";
						break;
					case 'most_discussed': 
						$aExtra['order'] = "m.total_comment DESC";
						break;
				}					
			}				

			if(isset($aVals['filterinauction_show']) && $aVals['filterinauction_show']) {
				$iItemPerPage = (int)$aVals['filterinauction_show'];
			}

			if(isset($aVals['page']) && $aVals['page']) {
				$iPage = $aVals['page'];
			}			
		}

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = ($iPage - 1) * $iItemPerPage; // without count, page is offset

		$aAuction = $aYnAuctionDetail['aAuction'];
		if(isset($aAuction['product_id']) == false){
			$hidden_productid = (int)$aVals['hidden_productid'];
			$aAuction = Phpfox::getService('auction')->getQuickAuctionByProductId($hidden_productid);
		}

		$aVideos = array();
		$iCountVideos = 0;
		list($aVideos, $iCountVideos) = Phpfox::getService('ecommerce')->getVideoByProductId($aAuction['product_id'], $aConds, $aExtra, true);				
		foreach ($aVideos as $iKey => $aRow)
		{	
			$aVideos[$iKey]['link'] = Phpfox::permalink($sModuleId, $aRow['video_id'], $aRow['title']);
		}

		$this->setParam('aPagingParams', array(
			'total_all_result' => $iCountVideos,
			'total_result' => count($aVideos),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));
		
		$sLink = Phpfox::getLib('url')->permalink('auction.detail', $aAuction['product_id'], $aAuction['name']) . "{$sType}/";
		$this->template()->assign(array(
				'aYnAuctionDetail' => $aYnAuctionDetail, 
				'aVideos' => $aVideos, 
				'iCountVideos' => $iCountVideos, 
				'sLink' => $sLink, 
				'hidden_select' => $hidden_select, 
			)
		);
	}

}

?>
