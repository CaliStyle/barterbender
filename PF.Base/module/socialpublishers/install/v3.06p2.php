<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsp_install306p2() {
	$oDatabase = Phpfox::getLib('database') ;
    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "younet_directory" AND module_id = "directory"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow){
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_directory',
                'module_id' => 'directory',
                'title' => 'socialpublishers.publishers_directory',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );

    }
    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "ultimatevideo" AND module_id = "ultimatevideo"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow){
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'ultimatevideo',
                'module_id' => 'ultimatevideo',
                'title' => 'socialpublishers.publishers_ultimatevideo',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );

    }       
}

ynsp_install306p2();

?>
