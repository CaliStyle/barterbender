<?php

namespace Apps\YNC_Affiliate\Installation\Data;

use Phpfox;

class YncAffiliatev401p2
{
    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('yncaffiliate_rules'))
            ->where('module_id = \'blog\' AND rule_name = \'sponsor_blog\'')
            ->execute('getSlaveField');
        if (!$iCnt) {
            $iRuleId = $this->database()->insert(Phpfox::getT('yncaffiliate_rules'), [
                'module_id' => 'blog',
                'rule_title' => 'Sponsor Blog',
                'rule_name' => 'sponsor_blog'
            ]);
            $aUserGroups = $this->database()->select('ugroup.*')
                ->from(Phpfox::getT('user_group'), 'ugroup')
                ->order('user_group_id ASC')
                ->execute('getSlaveRows');
            $aRuleMapId = [];
            if ($aUserGroups) {
                foreach ($aUserGroups as $aUserGroup) {
                    $aRuleMapId[] = $this->database()->insert(Phpfox::getT('yncaffiliate_rulemaps'),
                        [
                            'rule_id' => $iRuleId,
                            'user_group_id' => $aUserGroup['user_group_id']
                        ]);

                }
                foreach ($aRuleMapId as $iMapId) {
                    for ($iLevel = 1; $iLevel <= 5; $iLevel++) {
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