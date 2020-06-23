<?php
namespace Apps\YNC_Member\Installation\Data;

use Phpfox;

class YnMemberv402p3
{
    public function process()
    {
        $this->_removeRewrite();
    }

    private function _removeRewrite()
    {
        $check = db()
            ->select('rewrite_id')
            ->from(Phpfox::getT('rewrite'))
            ->where('url = \'ynmember\' AND replacement = \'members\'')
            ->execute('getSlaveField');

        if ($check) {
            db()->delete(Phpfox::getT('rewrite'), 'rewrite_id = ' . (int)$check);
        }

        $checkMenu = db()->select('is_active')
                        ->from(Phpfox::getT('menu'))
                        ->where('module_id = "ynmember" AND m_connection = "main" AND url_value = "ynmember"')
                        ->limit(1)
                        ->execute('getSlaveRow');
        if(empty($checkMenu)) {
            db()->insert(Phpfox::getT('menu'), [
                'm_connection' => 'main',
                'module_id' => 'ynmember',
                'var_name' => 'menu_ynmember',
                'is_active' => 1,
                'ordering' => 3,
                'url_value' => 'ynmember',
                'version_id' => '4.7.6',
                'mobile_icon' => 'users'
            ]);
        }

        if(empty($checkMenu) || $checkMenu['is_active']) {
            db()->update(Phpfox::getT('menu'), [
                'is_active' => 0
            ], 'm_connection = "main" AND module_id = "user" AND url_value = "user.browse"');
        }
    }
}