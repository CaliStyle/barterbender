<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 23/01/2017
 * Time: 11:40
 */

namespace Apps\YNC_Affiliate\Service\CommissionRule;

use Phpfox;

class CommissionRule extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sRuleTable          = Phpfox::getT('yncaffiliate_rules');
        $this->_sRuleMapTalble      = Phpfox::getT('yncaffiliate_rulemaps');
        $this->_sRuleMapDetailTable = Phpfox::getT('yncaffiliate_rulemap_details');
        $this->_sUserGroupTable     = Phpfox::getT('user_group');
        $this->_activeModule        = implode('\',\'',Phpfox::getLib('module')->getModules());
    }

    public function getUserGroupList($bIsAllow = false)
    {
        $aGroups =  db()->select('ugroup.*')
                ->from($this->_sUserGroupTable, 'ugroup')
                ->order('user_group_id ASC')
                ->execute('getSlaveRows');
        if($bIsAllow && count($aGroups))
        {
            foreach ($aGroups as $key => $aGroup)
            {
                if(!Phpfox::getService('user.group.setting')->getGroupParam($aGroup['user_group_id'], 'ynaf_can_register_affiliate'))
                {
                    unset($aGroups[$key]);
                }
            }
        }
        return $aGroups;
    }

    public function getRuleByUserGroup($iUserGroupId,$bActive = false)
    {
        $aRules = db()->select('r.*,rm.user_group_id, rm.rulemap_id,rm.is_active')
            ->from($this->_sRuleTable, 'r')
            ->join($this->_sRuleMapTalble, 'rm', 'rm.rule_id = r.rule_id')
            ->where('rm.user_group_id=' . (int) $iUserGroupId.' AND r.module_id IN(\''.$this->_activeModule.'\')'.($bActive ? ' AND rm.is_active = 1' : ''))
            ->execute('getSlaveRows');
        if(count($aRules)){
            foreach ($aRules as $rmkey => $rule)
            {

                $aRuleDetail = db()->select('rmd.*')
                    ->from($this->_sRuleMapDetailTable, 'rmd')
                    ->where('rmd.rulemap_id=' . (int) $rule['rulemap_id'])
                    ->execute('getSlaveRows');
                if(count($aRuleDetail)){
                    foreach ($aRuleDetail as $rmdKey => $ruleDetail)
                    {
                        $aRules[$rmkey]['level_'.$ruleDetail['rule_level']] = ($ruleDetail['rule_value'] > 0) ?  $ruleDetail['rule_value'].'%' : 'N/A';
                    }
                }
                else{
                    $aRules[$rmkey]['level_1'] = $aRules[$rmkey]['level_2'] = $aRules[$rmkey]['level_3'] =$aRules[$rmkey]['level_4'] = $aRules[$rmkey]['level_5'] = 'N/A';
                }
            }
        }
        else{
            $aAllRules = db()->select('*')
                ->from($this->_sRuleTable)
                ->execute('getRows');
            foreach ($aAllRules as $key => $aRule)
            {
                $aRuleMapId[] = db()->insert($this->_sRuleMapTalble,
                                    [
                                        'rule_id' => $aRule['rule_id'],
                                        'user_group_id' => $iUserGroupId
                                    ]);
            }
            foreach ($aRuleMapId as $aMapId)
            {
                for ($iLevel = 1; $iLevel <= 5; $iLevel++) {
                    db()->insert(Phpfox::getT('yncaffiliate_rulemap_details'),
                        [
                            'rulemap_id' => $aMapId,
                            'rule_level' => $iLevel,
                            'rule_value' => 0,
                        ]);
                }
            }
            return $this->getRuleByUserGroup($iUserGroupId);
        }

        return $aRules;
    }

    public function getCommissionRuleDetail($iRuleMapId)
    {
        $aRuleDetail = db()->select('rulemapdetail_id, rule_level, rule_value')
                            ->from($this->_sRuleMapDetailTable)
                            ->where('rulemap_id ='.$iRuleMapId)
                            ->execute('getRows');
        return $aRuleDetail;
    }
    public function getRuleDetail($iRuleId,$sFields = '*')
    {
        return db()->select($sFields)
                ->from($this->_sRuleTable)
                ->where('rule_id ='.(int)$iRuleId)
                ->execute('getRow');
    }
    public function getRuleByName($sName,$sFields = '*')
    {
        return db()->select($sFields)
            ->from($this->_sRuleTable)
            ->where('rule_name = \''.$sName.'\'')
            ->execute('getRow');
    }

    public function getRuleMapDetail($iRuleMapDetailId = null,$sFields = '*',$sType = 'getSlaveField',$iUserId = null,$iRuleId = 0,$iLevel = 0)
    {
        if($iRuleMapDetailId)
        {
            return db()->select($sFields)
                ->from($this->_sRuleMapDetailTable)
                ->where('rulemapdetail_id ='.(int)$iRuleMapDetailId)
                ->execute($sType);
        }
        else{
            $aUserGroup = Phpfox::getService('user')->getUser($iUserId,'u.user_group_id');
            if($aUserGroup)
            {
                $iRuleMapId = $this->getRuleMap(null,'rulemap_id','getSlaveField',$iRuleId,$aUserGroup['user_group_id'],true);
                if($iRuleMapId)
                {
                    return db()->select($sFields)
                                ->from($this->_sRuleMapDetailTable)
                                ->where('rulemap_id = '.(int)$iRuleMapId. ' AND rule_level = '.$iLevel)
                                ->execute($sType);
                }
                else{
                    return ;
                }
            }
        }
    }
    public function getRuleMap($iRuleMapId = null,$sFields = '*',$sType = 'getSlaveField',$iRuleId = null,$iGroupId = null,$bActive = false)
    {
        if($iRuleMapId)
        {
            return db()->select($sFields)
                ->from($this->_sRuleMapTalble)
                ->where('rulemap_id ='.(int)$iRuleMapId. ($bActive ? ' AND is_active = 1' : ''))
                ->execute($sType);
        }
        else{
            return db()->select($sFields)
                        ->from($this->_sRuleMapTalble)
                        ->where('rule_id = '.(int)$iRuleId.' AND user_group_id = '.(int)$iGroupId. ($bActive ? ' AND is_active = 1' : ''))
                        ->execute($sType);
        }
    }
    public function getActiveRules()
    {
        $aRules = db()->select('r.*')
            ->from($this->_sRuleTable, 'r')
            ->where('r.module_id IN(\''.$this->_activeModule.'\')')
            ->execute('getSlaveRows');
        return $aRules;
    }

}