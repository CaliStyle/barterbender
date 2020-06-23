<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:32
 */

namespace Apps\YNC_Affiliate\Block;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
class CommissionRuleBlock extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $iGroupId = $this->getParam('group',1);
        $aItems = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getRuleByUserGroup($iGroupId,true);

        $iMaxLevel = setting('ynaf_number_commission_levels');

        foreach ($aItems as $key => $aItem) {
            for ($i = 1; $i <= 5; $i++) {
                if ($i>$iMaxLevel) {
                    unset($aItems[$key]['level_' . $i]);
                }
            }
        }

        $labels = array();
        for ($i = 1; $i <= $iMaxLevel; $i++) {
            $labels[] = _p('Level ' . $i);
        }

        $this->template()->assign([
            'aItems' => $aItems,
            'labels' => $labels,
            'iMaxLevel' => $iMaxLevel
        ]);
        return 'block';
    }
}