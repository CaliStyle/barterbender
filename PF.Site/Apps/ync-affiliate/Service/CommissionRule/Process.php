<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 23/01/2017
 * Time: 15:01
 */

namespace Apps\YNC_Affiliate\Service\CommissionRule;


use Phpfox;

class Process extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sRuleTable = Phpfox::getT('yncaffiliate_rules');
    }

    public function updateRuleStatus($iRuleId, $iState)
    {
        return db()->update(Phpfox::getT('yncaffiliate_rulemaps'), [
            'is_active' => (int) $iState,
        ], 'rulemap_id=' . (int) $iRuleId);

    }
    public function addRulesForUserGroup($iUserGroupId, $iRuleId = null)
    {
        $aRules = [];
        if((int)$iRuleId)
        {
            $aRules = db()->select('*')
                ->from($this->_sRuleTable)
                ->where('rule_id ='.$iRuleId)
                ->execute('getRows');
        }
        else{
            $aRules = db()->select('*')
                ->from($this->_sRuleTable)
                ->execute('getRows');
        }
        if($aRules)
        {
            foreach ($aRules as $aRule)
            {
                db()->insert(Phpfox::getT('yncaffiliate_rulemaps'),
                    [
                        'rule_id' => $aRule['rule_id'],
                        'user_group_id' => $iUserGroupId
                    ]);
            }
        }
    }
    public function updateRuleForGroup($aVals,$iRuleMapId)
    {
        if(!(int)$iRuleMapId)
            return false;
        for($i = 1; $i<6; $i++)
        {
            if(isset($aVals['level_'.$i]))
            {
                db()->update(Phpfox::getT('yncaffiliate_rulemap_details'),['rule_value' => empty($aVals['level_'.$i]['rule_value']) ? 0 : $aVals['level_'.$i]['rule_value']],'rulemapdetail_id = '.$aVals['level_'.$i]['rulemapdetail_id']);
            }
        }
        if(isset($aVals['is_active'])){
            db()->update(Phpfox::getT('yncaffiliate_rulemaps'),['is_active' => 1],'rulemap_id ='.$iRuleMapId);
        }
        else{
            db()->update(Phpfox::getT('yncaffiliate_rulemaps'),['is_active' => 0],'rulemap_id ='.$iRuleMapId);
        }
        return true;
    }
    public function clearRuleDetail($iRuleMapId)
    {
        if(!(int)$iRuleMapId)
            return false;
        db()->update(Phpfox::getT('yncaffiliate_rulemap_details'),['rule_value' => 0],'rulemap_id ='.(int)$iRuleMapId);
        db()->update(Phpfox::getT('yncaffiliate_rulemaps'),['is_active' => 1],'rulemap_id ='.$iRuleMapId);
        return true;
    }
}