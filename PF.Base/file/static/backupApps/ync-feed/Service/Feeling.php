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

class Feeling extends \Phpfox_Service
{
    protected $_sTable;
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynfeed_feeling');
    }

    public function getFromCache() {
        return get_from_cache(['ynfeed.feelings'],function() {
            $aFeelings = db()->select('*')->from($this->_sTable)->order('title')->execute('getSlaveRows');
            shuffle($aFeelings);
            return array_map(function ($row){
                $row['title_translated'] = _p($row['title']);
                $row['image_url'] = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-feed/assets/images/feelings/'.$row['image'];
                $row['image'] = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-feed/assets/images/feelings/'.$row['image'];
                return $row;
            },$aFeelings);
        }, 1);
    }

    public function getFeelingIcons() {
        return get_from_cache(['ynfeed.feeling_icons'],function() {
            $aFeelings = db()->select('code, image')->from($this->_sTable)->order('code')->group('code')->execute('getSlaveRows');
            return array_map(function ($row){
                $row['image'] = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-feed/assets/images/feelings/'.$row['image'];
                return $row;
            },$aFeelings);
        }, 1);
    }

    public function hasFeelingId($iFeelingId) {
        if(db()->select('feeling_id')->from($this->_sTable)->where('feeling_id = ' . (int) $iFeelingId)->execute('getField'))
            return true;
        return false;
    }

    public function getFeelingById($iFeelingId) {
        $aFeeling = db()->select('*')->from($this->_sTable)->where('feeling_id = '. (int) $iFeelingId)->execute('getSlaveRow');
        if(isset($aFeeling['image']) && isset($aFeeling['title'])) {
            $aFeeling['title_translated'] = _p($aFeeling['title']);
            $aFeeling['image'] = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-feed/assets/images/feelings/'.$aFeeling['image'];
        }
        return $aFeeling;

    }

    public function getFeelingFromExtraInfo($aExtraInfo) {
        $aFeeling = [];
        if(isset($aExtraInfo['feeling_id']) && (int) $aExtraInfo['feeling_id']) {
            $aFeeling = $this->getFeelingById($aExtraInfo['feeling_id']);
        }
        if(isset($aExtraInfo['params'])) {
            $aParams = json_decode($aExtraInfo['params'], true);
            if(isset($aParams['custom_feeling_text']) && !empty($aParams['custom_feeling_text'])) {
                $aFeeling['title'] = $aParams['custom_feeling_text'];
                $aFeeling['title_translated'] = $aParams['custom_feeling_text'];
                $aFeeling['feeling_id'] = -1;
            }
            if(isset($aParams['custom_feeling_image']) && !empty($aParams['custom_feeling_image'])) {
                $aFeeling['image'] = $aParams['custom_feeling_image'];
                $aFeeling['feeling_id'] = -1;
            }
        }
        return $aFeeling;
    }
}