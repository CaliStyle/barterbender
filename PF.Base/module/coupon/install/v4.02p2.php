<?php
defined('PHPFOX') or exit('NO DICE!');

function ync_install402p2()
{
    $oDatabase = Phpfox::getLib('database');

    $iCnt = $oDatabase->select('COUNT(*)')
                    ->from(Phpfox::getT('user_group_setting'))
                    ->where('name = "can_rate_coupon" AND module_id = "coupon"')
                    ->execute('getSlaveField');

    if($iCnt)
    {
        $oDatabase->delete(Phpfox::getT('user_group_setting'),[
            'module_id' => 'coupon',
            'name' => 'can_rate_coupon'
        ]);
        $oDatabase->delete(Phpfox::getT('user_group_custom'),[
            'module_id' => 'coupon',
            'name' => 'can_rate_coupon'
        ]);
    }

    $iBlockId = $oDatabase->select('block_id')
        ->from(Phpfox::getT('block'))
        ->where('component = "search" AND module_id = "coupon" AND m_connection = "coupon.index"')
        ->execute('getSlaveField');

    if($iBlockId)
    {
        Phpfox::getService('admincp.block.process')->delete($iBlockId);
    }
}
ync_install402p2();