<?php

namespace Apps\YNC_WebPush\Installation\Data;

defined('PHPFOX') or exit('NO DICE!');

class v401
{
    public function __construct()
    {

    }

    public function process()
    {
        $iCnt = db()->select('COUNT(*)')
                    ->from(':cron')
                    ->where('module_id = "yncwebpush" AND php_code = "Phpfox::getService(\'yncwebpush.token.process\')->cronCheckExpiredToken();"')
                    ->execute('getField');
        if (!$iCnt) {
            db()->insert(':cron',[
                'module_id' => 'yncwebpush',
                'product_id' => 'phpfox',
                'type_id' => 3,
                'every' => 1,
                'is_active' => 1,
                'php_code' => 'Phpfox::getService(\'yncwebpush.token.process\')->cronCheckExpiredToken();'
            ]);
        }

    }
}