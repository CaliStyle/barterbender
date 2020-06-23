<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Adapter extends Phpfox_service {

	/**
	 * [adaptDataOfItemInAddEntryList adapt data from phpfox to be compatible with universal inteface used in add entry entry.class.php service getDataOfAddEntryTemplate]
	 * @param  [integer] $iItemTypeId [type id]
	 * @param  [integer] $aItems      [array of items]
	 * @return [array]              [array of transformed data]
	 */
	public function adaptDataOfItemInAddEntryList($iItemTypeId, $aItems, $iSourceId = 2)
	{
		$sItemTypeName = Phpfox::getService('contest.constant')->getContestTypeNameByTypeId($iItemTypeId);
		$aAdaptedItems = array();
        $aUserImage = Phpfox::getService('contest.helper')->getUserImage(Phpfox::getUserId());
        $sUserImage = isset($aUserImage['user_image']) ? 'user/' . sprintf($aUserImage['user_image'], '_200_square') : '';
        $iUserServerId = isset($aUserImage['server_id']) ? $aUserImage['server_id'] : 0;
		
        foreach($aItems as $aItem)
		{
			$aTempItem = $aItem;
			switch ($sItemTypeName)
            {
				case 'video':
				        switch ($iSourceId) {
				            // Case ultimate video
                            case 1:
                                $sImagePath = sprintf($aItem['image_path'], '_250');
                                break;
                            // Case video channel
                            case 2:
                                $sImagePath = sprintf($aItem['image_path'], '_120');
                                break;
                            // Case core video
                            case 3:
                                if(!empty($aItem['image_path']) && strpos($aItem['image_path'], 'video/') !== 0) {
                                    $sImagePath = sprintf('video' . DIRECTORY_SEPARATOR . $aItem['image_path'], '');
                                } else {
                                    $sImagePath = sprintf($aItem['image_path'], '_500');
                                }
                                break;
                        }
						$iItemId = $aItem['video_id'];
					break;
				case 'photo':
						$sImagePath = 'photo/' . sprintf($aItem['destination'], '_500');
						$iItemId = $aItem['photo_id'];
					break;
				case 'blog':
                    if($iSourceId == 2) {
                        $sImagePath = null;
                        if(!empty($aItem['image_path'])) {
                            $dirPath = Phpfox::getParam('core.dir_pic');
                            if(file_exists($dirPath . 'ynadvancedblog' . PHPFOX_DS . sprintf($aItem['image_path'], '_240'))) {
                                $sImagePath = 'ynadvancedblog/' . sprintf($aItem['image_path'], '_240');
                            }
                            elseif(file_exists($dirPath . 'ynadvancedblog' . PHPFOX_DS . sprintf($aItem['image_path'], '_grid'))) {
                                $sImagePath = 'ynadvancedblog/' . sprintf($aItem['image_path'], '_grid');
                            }
                            elseif(file_exists($dirPath . 'ynadvancedblog' . PHPFOX_DS . sprintf($aItem['image_path'], ''))) {
                                $sImagePath = 'ynadvancedblog/' . sprintf($aItem['image_path'], '');
                            }
                        }
                        $aTempItem['server_id'] = $iUserServerId;
                        $iItemId = $aItem['blog_id'];
                    } else {
                        if (empty($aItem['image_path'])) {
                            $sImagePath = $sUserImage;
                            $aTempItem['server_id'] = $iUserServerId;
                        } else {
                            $sImagePath = $aItem['image_path'];
                            $aTempItem['server_id'] = $aItem['server_id'];
                        }
                        $iItemId = $aItem['blog_id'];
                    }
					break;
				case 'music':
                    if (empty($aItem['image_path'])) {
                        $sImagePath = $sUserImage;
                        $aTempItem['server_id'] = $iUserServerId;
                    } else {
                        $sImagePath = $aItem['image_path'];
                        $aTempItem['server_id'] = $aItem['image_server_id'];
                    }
                    unset($aTempItem['image_server_id']);
                    $iItemId = $aItem['song_id'];
					break;
				default:
						$sTitle = '';
						$sImagePath = '';
						$iItemId = '';
					break;
			}
            
            if($sImagePath != ''){
				$aTempItem['image_path'] = $sImagePath;
            }
            else{
            	$aTempItem['user_server_id'] = $iUserServerId;
            	$aTempItem['user_image'] = $sImagePath;
            	$aTempItem['user_name'] = $aUserImage['user_name'];
            	$aTempItem['full_name'] = $aUserImage['full_name'];

            }
			$aTempItem['item_id'] = $iItemId;
			$aTempItem['item_type'] = $sItemTypeName;

			$aAdaptedItems[] = $aTempItem;
		}

		return $aAdaptedItems;
	}
}