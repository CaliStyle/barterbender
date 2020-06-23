<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 18/01/2017
 * Time: 11:46
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;
class IndexController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $this->template()->setTitle(_p('commission_rules'))
            ->setBreadCrumb(_p('Affiliate'))
            ->setBreadCrumb(_p('commission_rules'),$this->url()->makeUrl('affiliate'));
        $aUserGroup = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getUserGroupList(true);
        $iUserGroupId = $this->request()->get('group');
        $sError = '';
        if($iUserGroupId) {
            $bAllowSignup = Phpfox::getService('user.group.setting')->getGroupParam($iUserGroupId, 'ynaf_can_register_affiliate');
            if (!$bAllowSignup) {
                $sError = _p('you_can_not_view_commission_rules_of_this_user_group');
            }
        }
        else{
            $iUserGroupId = (count($aUserGroup)) ? reset($aUserGroup)['user_group_id'] : 0;
        }
        if ($aVals = $this->request()->get('val')) {
            $iUserGroupId = (int)$aVals['group_user'];
        }

        $aItems = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getRuleByUserGroup($iUserGroupId,true);
        $this->template()->assign([
            'aUserGroup'   => $aUserGroup,
            'aItems'       => $aItems,
            'iUserGroupId' => $iUserGroupId,
            'sError'       => $sError
        ]);
    }
}