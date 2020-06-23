<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/9/17
 * Time: 14:14
 */
namespace Apps\YNC_Affiliate\Installation\Data;

use Phpfox;
class YncAffiliatev401p1
{
    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        if(!$this->database()->isField(Phpfox::getT('yncaffiliate_requests'),'request_method'))
        {
            $this->database()->query("ALTER TABLE  `".Phpfox::getT('yncaffiliate_requests')."` ADD COLUMN `request_method` varchar(75);");
        }
        if(!$this->database()->isField(Phpfox::getT('yncaffiliate_requests'),'request_method_title'))
        {
            $this->database()->query("ALTER TABLE  `".Phpfox::getT('yncaffiliate_requests')."` ADD COLUMN `request_method_title` varchar(150);");
        }
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('yncaffiliate_rules'))
            ->where('module_id = \'user\' AND rule_name = \'purchase_activity_points\'')
            ->execute('getSlaveField');
        if(!$iCnt)
        {
            $iRuleId = $this->database()->insert(Phpfox::getT('yncaffiliate_rules'),[
                'module_id' => 'user',
                'rule_title' => 'Purchase Activity Points',
                'rule_name' => 'purchase_activity_points'
            ]);
            $aUserGroups = $this->database()->select('ugroup.*')
                ->from(Phpfox::getT('user_group'), 'ugroup')
                ->order('user_group_id ASC')
                ->execute('getSlaveRows');
            $aRuleMapId = [];
            if($aUserGroups){
                foreach ($aUserGroups as $aUserGroup)
                {
                    $aRuleMapId[] = $this->database()->insert(Phpfox::getT('yncaffiliate_rulemaps'),
                        [
                            'rule_id' => $iRuleId,
                            'user_group_id' => $aUserGroup['user_group_id']
                        ]);

                }
                foreach ($aRuleMapId as $iMapId)
                {
                    for ($iLevel = 1; $iLevel <= 5; $iLevel++)
                    {
                        $this->database()->insert(Phpfox::getT('yncaffiliate_rulemap_details'),
                            [
                                'rulemap_id' => $iMapId,
                                'rule_level' => $iLevel,
                                'rule_value' => 0,
                            ]);
                    }
                }
            }
        }
    }
}