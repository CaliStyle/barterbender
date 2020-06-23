<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailmusicslist extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$sType = 'musics';

		$aCore = $this->request()->get('core');
		$iItemPerPage = 5;
		$iPage = 1;
		$aConds = array(' 1=1 ');
		$aExtra['order'] = 'm.time_stamp DESC';

		$sOrderingField = '';
		$sOrderingType = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdMusic();
		$hidden_select = '';

		if($aVals = $this->getParam('aQueryParam')) {
			$hidden_select = $aVals['hidden_select'];
			if(isset($aVals['keyword']) && $aVals['keyword']) {
				$sKeywordParse = Phpfox::getLib('parse.input')->clean($aVals['keyword']);
				$aConds[] = 'm.title like \'%' . $sKeywordParse . '%\' ';
			}

			if(isset($aVals['filterinbusiness_when']) && $aVals['filterinbusiness_when']) {
				$iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
				$field = 'm.time_stamp';
				switch ($aVals['filterinbusiness_when'])
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

			if(isset($aVals['filterinbusiness_sort']) && $aVals['filterinbusiness_sort']) {
				switch ($aVals['filterinbusiness_sort']){
					case 'latest': 
						$aExtra['order'] = "m.song_id DESC";
						break;
					case 'most_viewed': 
						$aExtra['order'] = "m.total_play DESC";
						break;
					case 'most_liked': 
						$aExtra['order'] = "m.total_like DESC";
						break;
					case 'most_discussed': 
						$aExtra['order'] = "m.total_comment DESC";
						break;
				}					
			}				

			if(isset($aVals['filterinbusiness_show']) && $aVals['filterinbusiness_show']) {
				$iItemPerPage = (int)$aVals['filterinbusiness_show'];
			}

			if(isset($aVals['page']) && $aVals['page']) {
				$iPage = $aVals['page'];
			}			
		}

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = $iPage;

		$aBusiness = $aYnDirectoryDetail['aBusiness'];
		if(isset($aBusiness['business_id']) == false){
			$hidden_businessid = (int)$aVals['hidden_businessid'];
			$aBusiness = Phpfox::getService('directory')->getBusinessById($hidden_businessid);
		}
		$aMusics = array();
		$iCountMusics = 0;
		list($aMusics, $iCountMusics) = Phpfox::getService('directory')->getMusicByBusinessId($aBusiness['business_id'], $aConds, $aExtra, true);				
		foreach ($aMusics as $iKey => $aSong)
		{	
			if(Phpfox::getService('directory.helper')->isAdvMusic()){

			} else {
				$aMusics[$iKey]['song_path'] = Phpfox::getService('music')->getSongPath($aSong['song_path'], $aSong['server_id']);

				// $aMusics[$iKey]['aFeed'] = array(			
				// 	'feed_display' => 'mini',	
				// 	'comment_type_id' => 'music_song',
				// 	'privacy' => $aSong['privacy'],
				// 	'comment_privacy' => $aSong['privacy_comment'],
				// 	'like_type_id' => 'music_song',				
				// 	'feed_is_liked' => (isset($aSong['is_liked']) ? $aSong['is_liked'] : false),
				// 	'feed_is_friend' => (isset($aSong['is_friend']) ? $aSong['is_friend'] : false),
				// 	'item_id' => $aSong['song_id'],
				// 	'user_id' => $aSong['user_id'],
				// 	'total_comment' => $aSong['total_comment'],
				// 	'feed_total_like' => $aSong['total_like'],
				// 	'total_like' => $aSong['total_like'],
				// 	'feed_link' => Phpfox::getLib('url')->permalink('music', $aSong['song_id'], $aSong['title']),
				// 	'feed_title' => $aSong['title'],
				// 	'type_id' => 'music_song'
				// );				
			}
		}

		$this->setParam('aPagingParams', array(
			'total_all_result' => $iCountMusics,
			'total_result' => count($aMusics),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));
		
		$sLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']) . "{$sType}/";
		$this->template()->assign(array(
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'aMusics' => $aMusics, 
				'iCountMusics' => $iCountMusics, 
				'sLink' => $sLink, 
				'hidden_select' => $hidden_select,
                'sIconPath' => Phpfox::getParam('core.path') . 'module/directory/static/image/play_button.png',
			)
		);
	}

}

?>
