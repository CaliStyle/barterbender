<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:36
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
use Phpfox_Plugin;
class StatisticsController extends \Phpfox_Component
{
    public function process()
    {
        $this->template()->setTitle(_p('statistics'))
            ->setBreadCrumb(_p('Module Statistics'));
        $iTotalAffiliate = Phpfox::getService('yncaffiliate.affiliate.affiliate')->countAffiliates();
        $iTotalClients = Phpfox::getService('yncaffiliate.affiliate.affiliate')->countClients();
        $iTotalComPoint = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints(null,null,'denied');
        $iTotalRequested = Phpfox::getService('yncaffiliate.request')->getTotalRequestPoints('\'pending\',\'waiting\',\'completed\'');
        $aActiveRule = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getActiveRules();
        $aRuleComDetail = [];
        if(count($aActiveRule))
        {
            foreach($aActiveRule as $aRule)
            {
                $iTotal = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints(null,null,'denied',$aRule['rule_id']);
                if($iTotal)
                {
                    $aRuleComDetail[] = [
                        'rule_title' => $aRule['rule_title'],
                        'total_points' => $iTotal,
                    ];
                }
            }
        }
        $this->template()->assign([
            'iTotalAffiliate' => $iTotalAffiliate,
            'iTotalClients' => $iTotalClients,
            'iTotalComPoint' => (float)$iTotalComPoint,
            'iTotalRequested' => (float)$iTotalRequested,
            'aRuleComDetail' => $aRuleComDetail
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncaffiliate.component_controller_admincp_statistics_clean')) ? eval($sPlugin) : false);
    }
}