<?php
namespace Apps\YNC_Feed\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Request;
use Core;
use Phpfox_Ajax;
use Phpfox_Url;
use Phpfox_Template;
use Phpfox_Error;
use Phpfox_Database;

defined('PHPFOX') or exit('NO DICE!');

class Hide extends \Phpfox_Service
{
    protected $_sTable;
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynfeed_hide');
    }

    public function add($iUserId, $iResourceId, $sResourceType) {
        $success = false;
        if (!$this->isHidden($iUserId, $iResourceId, $sResourceType)) {
            if(($success = db()->insert($this->_sTable, [
                'user_id' => $iUserId,
                'hide_resource_id' => $iResourceId,
                'hide_resource_type' => $sResourceType
            ])) && Phpfox::VERSION >= '4.7.6' && ($coreFeedHideService = Phpfox::getService('feed.hide'))) {
                $coreFeedHideService->add($iUserId, $iResourceId, $sResourceType);
            }
        }
        return $success;
    }
    public function delete($iUserId, $iResourceId, $sResourceType) {
        if(($success = db()->delete($this->_sTable, "user_id = " . (int) $iUserId . ' AND hide_resource_id = ' . (int) $iResourceId . ' AND hide_resource_type = \'' . $sResourceType . '\'')) && Phpfox::VERSION >= '4.7.6' && ($coreFeedHideService = Phpfox::getService('feed.hide'))) {
            $coreFeedHideService->delete($iUserId, $iResourceId, $sResourceType);
        }
        return $success;
    }

    public function isHidden($iUserId, $iResourceId, $sResourceType) {
        if(db()->select('hide_id')->from($this->_sTable)->where('user_id = ' . (int) $iUserId . ' AND hide_resource_id = ' . (int) $iResourceId . ' AND hide_resource_type = \'' . $sResourceType . '\'')->execute('getField')) {
            return true;
        }
        return false;
    }

    public function getHide($iUserId = null) {
        if($iUserId == null)
            $iUserId = Phpfox::getUserId();
        $aHides = db()->select('*')->from($this->_sTable)->where('user_id = ' . (int) $iUserId)->execute('getSlaveRows');
        $aHideIds = [];
        foreach($aHides as $aHide) {
            $sType = $aHide['hide_resource_type'];
            (array_key_exists($sType, $aHideIds)) ?  array_push($aHideIds[$sType], $aHide['hide_resource_id']) : $aHideIds[$sType] = [$aHide['hide_resource_id']];
        }
        return $aHideIds;
    }

    public function getHiddenUsers($iUserId = null, $sExtraCond = '', $iPage = 1,$iLimit = 10) {
        if($iUserId == null) {
            $iUserId = Phpfox::getUserId();
        }

        $mergeCore = Phpfox::VERSION >= '4.7.6' && ($coreFeedHideService = Phpfox::getService('feed.hide'));
        if($mergeCore) {
            $this->database()->select('hide.hide_id, hide.hide_resource_id, hide.hide_resource_type, ' . Phpfox::getUserField('user'))
                ->join(Phpfox::getT('user'), 'user', 'user.user_id = hide.hide_resource_id')
                ->from($this->_sTable, 'hide')
                ->where('hide.user_id = ' . (int) $iUserId . ' AND hide.hide_resource_type = \'user\' ' . $sExtraCond)
                ->union();
            $this->database()->select('hide.hide_id, hide.item_id AS hide_resource_id, hide.type_id AS hide_resource_type, ' . Phpfox::getUserField('user'))
                ->join(Phpfox::getT('user'), 'user', 'user.user_id = hide.item_id')
                ->from(':feed_hide', 'hide')
                ->where('hide.user_id = ' . (int) $iUserId . ' AND hide.type_id = \'user\' ' . $sExtraCond)
                ->union()
                ->unionFrom('hide');
            $aHides = db()->select('hide.*')
                  ->group('hide.user_id')
                 ->limit($iPage, $iLimit)
                 ->forCount()
                 ->execute('getSlaveRows');
        }
        else {
            $aHides = $this->database()->select('hide.*, ' . Phpfox::getUserField('user'))
                ->join(Phpfox::getT('user'), 'user', 'user.user_id = hide.hide_resource_id')
                ->from($this->_sTable, 'hide')
                ->where('hide.user_id = ' . (int) $iUserId . ' AND hide.hide_resource_type = \'user\' ' . $sExtraCond)
                ->limit($iPage, $iLimit)
                ->forCount()
                ->execute('getSlaveRows');
        }

        $iCnt = $this->database()->getCount();

        foreach($aHides as $iKey=>$aHide) {
            $aHides[$iKey]['user_image'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aHide,
                    'suffix' => '_50_square',
                    'max_height' => 32,
                    'max_width' => 32,
                    'return_url' => true
                )
            );
            $aHides[$iKey]['user_image_actual'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aHide,
                    'suffix' => '_50_square',
                    'max_height' => 32,
                    'max_width' => 32
                )
            );
            $aHides[$iKey]['user_profile'] = ($aHide['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aHide['profile_page_id'], '', $aHide['user_name']) : Phpfox_Url::instance()->makeUrl($aHide['user_name']));
        }
        return array($iCnt, $aHides);
    }

    public function multiDelete($aHideIds, $iUserId = null) {
        if($iUserId == null)
            $iUserId = Phpfox::getUserId();
        $success = false;
        if(count($aHideIds) && $iUserId) {
            if(($success = db()->delete($this->_sTable, 'user_id = ' . (int)$iUserId . ' AND hide_id IN (' . implode(',',$aHideIds) . ')')) && Phpfox::VERSION >= '4.7.6' && ($coreFeedHideService = Phpfox::getService('feed.hide'))) {
                $coreFeedHideService->multiDelete($aHideIds, $iUserId);
            }
        }
        return $success;
    }
}