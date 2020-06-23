<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 17/02/2017
 * Time: 18:44
 */
defined('PHPFOX') or exit('NO DICE!');

function ynsp_install401p8() {
    $oDatabase = Phpfox::getLib('database') ;

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "ynblog" AND module_id = "ynblog"')
        ->execute('getRow');

    if(empty($aRow)){
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'ynblog',
                'module_id' => 'ynblog',
                'title' => 'socialpublishers.publishers_ynblog',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );

    }
    //Remove module socialstream
    db()->delete(':module','module_id = \'socialstream\'');
}

ynsp_install401p8();
?>