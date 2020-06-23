<?php
namespace Apps\YNC_Feed\Block;

use Phpfox;
use Phpfox_Parse_Output;
use Core;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Request;
defined('PHPFOX') or exit('NO DICE!');

class ManageHidden extends Phpfox_Component {
    public function process() {
        if (!Phpfox::isUser()) {
            return false;
        }
        $bSearch = false;
        $iPage = $this->request()->get('page', 1);
        $sName = $this->request()->get('name');
        $sType = $this->request()->get('type');
        $sCond = '';
        if($sName != '' || $sType != '') {
            $bSearch = true;
            $sCond = " AND user.full_name LIKE '%" . $sName ."%'";
            if($sType == 'friend')
                $sCond .= ' AND user.profile_page_id = 0';
            elseif($sType == 'page')
                $sCond .= ' AND user.profile_page_id > 0';
        }

        list($iCnt, $aHiddens) = Phpfox::getService('ynfeed.hide')->getHiddenUsers(Phpfox::getUserId(), $sCond, $iPage, 12);
        if($iCnt)
            Phpfox::getLib('pager')->set(array('page' => $iPage, 'popup' => true, 'size' => 12, 'count' => $iCnt, 'ajax' => 'ynfeed.manageHidden', 'aParams' => ['name'=>$sName,'type'=>$sType]));
        $this->template()->assign([
            'iCnt' => $iCnt,
            'bSearch' => $bSearch,
            'iPage' => $iPage,
            'aHiddens' => $aHiddens
        ]);
        return 'block';
    }

    public function clean() {
    }
}