<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 17/02/2017
 * Time: 18:44
 */
defined('PHPFOX') or exit('NO DICE!');

function ynsp_install402() {
    $oDatabase = Phpfox::getLib('database') ;

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "videochannel" AND module_id = "videochannel"')
        ->execute('getRow');

    if(empty($aRow)){
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'videochannel',
                'module_id' => 'videochannel',
                'title' => 'socialpublishers.publishers_videochannel',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );

    }
}

ynsp_install402();
