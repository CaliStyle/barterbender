<?php
namespace Apps\YNC_Feed\Service\Directory;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Request;
use Core;
use Phpfox_Ajax;
use Phpfox_Url;
use Phpfox_Template;

defined('PHPFOX') or exit('NO DICE!');

class Directory extends \Directory_Service_Directory {
    public function getFromCache() {
        return get_from_cache(['ynfeed.business'],function() {
            list($iCnt,$aBusinesses) = Phpfox::getService('directory')->getBusiness('');
            return array_map(function ($row){
                if($row['logo_path'])
                    $row['business_image'] = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $row['server_id'],
                            'path' => 'core.url_pic',
                            'file' => $row['logo_path'],
                            'suffix' => '_100',
                            'return_url' => true
                        )
                    );
                else $row['business_image'] = Phpfox::getParam('core.path_actual') . 'PF.Base/module/directory/static/image/default_ava.png';
                return $row;
            },$aBusinesses);
        }, 1);
    }

    public function getCheckinsInfo($iBusinessId) {
        $aCheckins = $aRows = $this->database()
            ->select("cih.*, " . Phpfox::getUserField())
            ->from(Phpfox::getT("directory_checkinhere"), 'cih')
            ->join(Phpfox::getT("user"), 'u', 'cih.user_id =  u.user_id')
            ->where('cih.business_id = ' . (int)$iBusinessId)
            ->group('u.user_id')
            ->execute("getSlaveRows");

        $iTotalCheckins = count($aCheckins);
        $sCheckinsInfo = '';
        if($iTotalCheckins == 1){
            $phrase0 = '<a href="' . Phpfox_Url::instance()->makeUrl($aCheckins[0]['user_name']) . '">' . $aCheckins[0]['full_name'] . '</a>';
            $sCheckinsInfo = _p('someone_has_been_here', array('someone' => $phrase0));
        }else if($iTotalCheckins == 2){
            $phrase0 = '<a href="' . Phpfox_Url::instance()->makeUrl($aCheckins[0]['user_name']) . '">' . $aCheckins[0]['full_name'] . '</a>';
            $phrase1 = '<a href="' . Phpfox_Url::instance()->makeUrl($aCheckins[1]['user_name']) . '">' . $aCheckins[1]['full_name'] . '</a>';
            $sCheckinsInfo = _p('someone_and_someone_have_been_here', array('someone0' => $phrase0, 'someone1' => $phrase1));
        }else if($iTotalCheckins > 2) {
            /*Get others*/
            $sTooltip = '';
            $sTaggedExpandIds = '';
            for($i = 1; $i < $iTotalCheckins; $i++) {
                $sTooltip .= $aCheckins[$i]['full_name'] . '<br />';
                $sTaggedExpandIds .= $aCheckins[$i]['user_id'] . ',';
            }

            $phrase0 = '<a href="' . Phpfox_Url::instance()->makeUrl($aCheckins[0]['user_name']) . '">' . $aCheckins[0]['full_name'] . '</a>';
            $phrase1 = '<span class="ynfeed_popover ynfeed_expand_users" data-tagged="' . $sTaggedExpandIds . '" data-content="' . $sTooltip .'" rel="popover" data-placement="bottom" data-html="true">' . _p('number_others', array('number' => $iTotalCheckins - 1)) . '</span>';
            $sCheckinsInfo = _p('someone_and_someone_have_been_here', array('someone0' => $phrase0, 'someone1' => $phrase1));
        }
        return $sCheckinsInfo;

    }
}