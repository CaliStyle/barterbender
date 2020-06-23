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
        $this->template()->assign([
            'aItems' => $aItems
        ]);
        return 'block';
    }
}