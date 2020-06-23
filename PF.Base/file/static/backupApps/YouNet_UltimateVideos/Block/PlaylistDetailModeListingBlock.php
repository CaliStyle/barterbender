<?php

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

class PlaylistDetailModeListingBlock extends Phpfox_Component
{
	public function process()
    {
    	// Page Number & Limit Per Page
        $iPage = $this->getParam('page',0);
		$iPageSize = setting('ynuv_item_per_page_in_playlist_detail',9);
    	$iPlaylistId = $this->getParam('playlist_id',0);
        $iCurrent_mode = $this->getParam('current_mode',1);
    	$corePath = Phpfox::getParam('core.path_actual').'PF.Site/Apps/YouNet_UltimateVideos';
    	list($iCount,$aList) = Phpfox::getService('ultimatevideo.playlist')->getVideosListing($iPlaylistId, $iPage,$iPageSize);
    	// Set pager
		phpFox::getLib('pager')->set(array(
					'page'  => $iPage, 
					'size'  => $iPageSize, 
					'count' => $iCount,
					'ajax' 	=>'ultimatevideo.changePageVideosInPlaylist',
					'popup'	=> true,
		));
    	$this->template()->assign([
    			'aItems' => $aList,
    			'corePath' => $corePath,
    			'iPlaylistId' => $iPlaylistId,
                'bShowCommand' => true,
                'bIsSearch' => false,
                'bMultiViewMode' => true,
                'bIsPagesView' => false,
                'iCurrentMode' => $iCurrent_mode,
    		]);
    }
}