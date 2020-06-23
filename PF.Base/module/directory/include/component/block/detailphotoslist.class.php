<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailphotoslist extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$sType = 'photos';
		$req6 = $this->request()->get('req6'); 
		if($req6 == 'albums'){
			$aExtra['order'] = 'pa.album_id DESC';
			$hidden_select = 'albums';
		} else {
			$aExtra['order'] = 'photo.photo_id DESC';
			$hidden_select = 'photos';
		}

		$aCore = $this->request()->get('core');
		$iItemPerPage = 5;
		$iPage = 1;
		$aConds = array(' 1=1 ');

		$sOrderingField = '';
		$sOrderingType = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdPhoto();
		if($aVals = $this->getParam('aQueryParam')) {
			$hidden_select = $aVals['hidden_select'];
			if($hidden_select == 'albums'){
				if(isset($aVals['keyword']) && $aVals['keyword']) {
					$sKeywordParse = Phpfox::getLib('parse.input')->clean($aVals['keyword']);
					$aConds[] = 'pa.name like \'%' . $sKeywordParse . '%\' ';
				}

				if(isset($aVals['filterinbusiness_when']) && $aVals['filterinbusiness_when']) {
					$iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
					$field = 'pa.time_stamp';
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
						default:
							break;			
					}
				}

				if(isset($aVals['filterinbusiness_sort']) && $aVals['filterinbusiness_sort']) {
					switch ($aVals['filterinbusiness_sort']){
						case 'latest': 
							$aExtra['order'] = "pa.album_id DESC";
							break;
						case 'most_viewed': 
							$aExtra['order'] = "pa.album_id DESC";
							break;
						case 'most_liked': 
							$aExtra['order'] = "pa.total_like DESC";
							break;
						case 'most_discussed': 
							$aExtra['order'] = "pa.total_comment DESC";
							break;
					}					
				}				

			} else {
				if(isset($aVals['keyword']) && $aVals['keyword']) {
					$sKeywordParse = Phpfox::getLib('parse.input')->clean($aVals['keyword']);
					$aConds[] = 'photo.title like \'%' . $sKeywordParse . '%\' ';
				}

				if(isset($aVals['filterinbusiness_when']) && $aVals['filterinbusiness_when']) {
					$iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
					$field = 'photo.time_stamp';
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
						default:
							break;			
					}
				}

				if(isset($aVals['filterinbusiness_sort']) && $aVals['filterinbusiness_sort']) {
					switch ($aVals['filterinbusiness_sort']){
						case 'latest': 
							$aExtra['order'] = "photo.photo_id DESC";
							break;
						case 'most_viewed': 
							$aExtra['order'] = "photo.total_view DESC";
							break;
						case 'most_liked': 
							$aExtra['order'] = "photo.total_like DESC";
							break;
						case 'most_discussed': 
							$aExtra['order'] = "photo.total_comment DESC";
							break;
					}					
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
		$aExtra['page'] = $iPage; // without count, page is offset

		$aBusiness = $aYnDirectoryDetail['aBusiness'];
		if(isset($aBusiness['business_id']) == false){
			$hidden_businessid = (int)$aVals['hidden_businessid'];
			$aBusiness = Phpfox::getService('directory')->getBusinessById($hidden_businessid);
		}
		$aAlbums = array();
		$iCountAlbums = 0;
		$aPhotos = array();
		$iCountPhotos = 0;
		if($hidden_select == 'albums'){
			list($aAlbums, $iCountAlbums) = Phpfox::getService('directory')->getAlbumByBusinessId($aBusiness['business_id'], $aConds, $aExtra, true);				
			foreach ($aAlbums as $iKey => $aRow)
			{	
				$aAlbums[$iKey]['link'] = Phpfox::permalink($sModuleId . '.album', $aRow['album_id'], $aRow['name']);
			}
			$iCountPhotos = Phpfox::getService('directory')->getPhotoByBusinessId($aBusiness['business_id'], array(' 1=1 '), array(), false);				

			$this->setParam('aPagingParams', array(
				'total_all_result' => $iCountAlbums,
				'total_result' => count($aAlbums),
				'page' => $iPage,
				'limit' => $iItemPerPage
			));

		} else {
			list($aPhotos, $iCountPhotos) = Phpfox::getService('directory')->getPhotoByBusinessId($aBusiness['business_id'], $aConds, $aExtra, true);				
			foreach ($aPhotos as $iKey => $aRow)
			{				
				$aPhotos[$iKey]['link'] = Phpfox::permalink($sModuleId, $aRow['photo_id'], $aRow['title']);
				$aPhotos[$iKey]['destination'] = Phpfox::getService($sModuleId)->getPhotoUrl($aRow);				
			}			
			$iCountAlbums = Phpfox::getService('directory')->getAlbumByBusinessId($aBusiness['business_id'], array(' 1=1 '), array(), false);				

			$this->setParam('aPagingParams', array(
				'total_all_result' => $iCountPhotos,
				'total_result' => count($aPhotos),
				'page' => $iPage,
				'limit' => $iItemPerPage
			));

		}
		
		$sLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']) . "{$sType}/";
		$this->template()->assign(array(
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'aPhotos' => $aPhotos, 
				'aAlbums' => $aAlbums, 
				'iCountPhotos' => $iCountPhotos, 
				'iCountAlbums' => $iCountAlbums, 
				'sLink' => $sLink, 
				'hidden_select' => $hidden_select, 
				'iPhotosPerRow' => 3, 
			)
		);
	}

}

?>
