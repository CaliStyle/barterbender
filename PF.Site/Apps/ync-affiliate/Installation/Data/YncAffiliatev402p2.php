<?php
namespace Apps\YNC_Affiliate\Installation\Data;

use Phpfox;

class YncAffiliatev402p2
{
    public function process()
    {
        $this->_updateRule();
    }

    private function _updateRule()
    {
        $rule = db()->select('rule_id, module_id')
            ->from(Phpfox::getT('yncaffiliate_rules'))
            ->where('rule_name = "purchase_activity_points"')
            ->execute('getSlaveRow');
        if(!empty($rule)) {
            if($rule['module_id'] == 'user') {
                db()->update(Phpfox::getT('yncaffiliate_rules'), [
                    'module_id' => 'activitypoint'
                ], 'rule_id = ' . (int)$rule['rule_id']);
            }

            $valid = true;
            if(Phpfox::isAppActive('Core_Activity_Points') && ($app = \Core\Lib::appInit('Core_Activity_Points'))) {
                $valid = $app->version >= '4.7.7';
            }
            if(!$valid) {
                db()->update(Phpfox::getT('yncaffiliate_rulemaps'), [
                    'is_active' => 0
                ], 'rule_id = ' . (int)$rule['rule_id']);
            }

        }
    }
}

