<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YNC_Feed\Block;

use Phpfox;
use Phpfox_Parse_Output;
use Core;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Request;
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: mini.class.php 4545 2012-07-20 10:40:35Z Raymond_Benc $
 */
class Mini extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iParentFeedId = (int) $this->getParam('parent_feed_id');
		$sParentModuleId = $this->getParam('parent_module_id');
		if (!$iParentFeedId)
		{
			return false;
		}

        //Get Real Module_id
        if (Phpfox::isModule($sParentModuleId) || Phpfox::isApps($sParentModuleId)){
            $sModule = $sParentModuleId;
        } else {
            $aModuleData = explode('_', $sParentModuleId);
            if (isset($aModuleData[0]) && Phpfox::isModule($aModuleData[0])){
                $sModule = $aModuleData[0];
            } else {
                return false;
            }
        }

		if (!Phpfox::hasCallback($sModule, 'canShareItemOnFeed'))
		{
			return false;
		}
        $aParentFeedItem = Phpfox::getService('ynfeed')->getParentFeedItem($sParentModuleId, $iParentFeedId);
        if (empty($aParentFeedItem)){
            $aParentFeedItem = [
                'feed_id' => $iParentFeedId,
                'item_id' => $iParentFeedId
            ];
        }

        $parentFeedId = $aParentFeedItem['feed_id'];
        if ($sParentModuleId == 'photo') {
            $aParentFeed = Phpfox::getService('ynfeed')->getActivityFeedPhoto($aParentFeedItem, null, true);
        } else {
		    $aParentFeed = Phpfox::callback($sParentModuleId . '.getActivityFeed', $aParentFeedItem , null, true);
        }

        if($aParentFeed) {
            if(!empty($aParentFeed['privacy']) && $aParentFeed['user_id'] != Phpfox::getUserId()) {
                $aParentFeed = [];
            }
            else {
                if(!isset($aParentFeed['feed_id'])) {
                    $aParentFeed['feed_id'] = $parentFeedId;
                }

                $aParentFeed['item_id'] = $iParentFeedId;
                $aParentFeed['type_id'] = $sParentModuleId;
                Phpfox::getService('ynfeed')->getExtraInfo($aParentFeed);
                if ($aParentFeed && !isset($aParentFeed['type_id']))
                {
                    $aParentFeed['type_id'] = $sParentModuleId;
                }

                if (isset($aParentFeed['privacy'])) {
                    $sIconClass = 'ico ';
                    switch ((int)$aParentFeed['privacy']) {
                        case 0:
                            $sIconClass .= 'ico-globe';
                            break;
                        case 1:
                            $sIconClass .= 'ico-user3-two';
                            break;
                        case 2:
                            $sIconClass .= 'ico-user-man-three';
                            break;
                        case 3:
                            $sIconClass .= 'ico-lock';
                            break;
                        case 4:
                            $sIconClass .= 'ico-gear-o';
                            break;
                    }
                    $aParentFeed['privacy_icon_class'] = $sIconClass;
                }

                // Retrieve user info
                $aParentFeed['user'] = array(
                    'user_id' => $aParentFeed['user_id'],
                    'profile_page_id' => $aParentFeed['profile_page_id'],
                    'server_id' => $aParentFeed['user_server_id'],
                    'user_name' => $aParentFeed['user_name'],
                    'full_name' => $aParentFeed['full_name'],
                    'gender' => $aParentFeed['gender'],
                    'user_image' => $aParentFeed['user_image'],
                    'is_invisible' => $aParentFeed['is_invisible'],
                    'user_group_id' => $aParentFeed['user_group_id'],
                    'language_id' => $aParentFeed['language_id'],
                    'birthday' => $aParentFeed['birthday'],
                    'country_iso' => $aParentFeed['country_iso'],
                );

                // check status background
                if (Phpfox::isAppActive('P_StatusBg')) {
                    $aParentFeed['status_background'] = Phpfox::getService('pstatusbg')->getFeedStatusBackground($aParentFeed['item_id'], $aParentFeed['type_id'], $aParentFeed['user_id']);
                }
            }
        }


		$this->template()->assign(array(
				'aParentFeed' => $aParentFeed
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('feed.component_block_mini_clean')) ? eval($sPlugin) : false);
	}	
}