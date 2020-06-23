<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:36
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class CommissionRuleController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        $iUserGroupId = $this->request()->get('group');
        $aUserGroup = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getUserGroupList(true);
        $sError = '';
        if ($iUserGroupId) {
            $bAllowSignup = Phpfox::getService('user.group.setting')->getGroupParam($iUserGroupId,
                'ynaf_can_register_affiliate');
            if (!$bAllowSignup) {
                $sError = _p('you_can_not_view_commission_rules_of_this_user_group');
            }
        }
        else {
            $iUserGroupId = (count($aUserGroup)) ? reset($aUserGroup)['user_group_id'] : 0;
        }
        if ($aVals = $this->request()->get('val')) {
            $iUserGroupId = (int)$aVals['group_user'];
        }


        $aItems = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getRuleByUserGroup($iUserGroupId);
        $this->template()->assign([
            'aUserGroup'   => $aUserGroup,
            'aItems'       => $aItems,
            'iUserGroupId' => $iUserGroupId,
            'sError'       => $sError
        ]);
        $this->template()
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('admincp.app',['id' => 'YNC_Affiliate']))
            ->setBreadCrumb(_p('commission_rules'));
    }
}