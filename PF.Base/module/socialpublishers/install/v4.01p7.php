<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsp_install401p7() {
	$oDatabase = Phpfox::getLib('database') ;
    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "ynsocialstore" AND module_id = "ynsocialstore"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow){
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'ynsocialstore',
                'module_id' => 'ynsocialstore',
                'title' => 'socialpublishers.publishers_ynsocialstore',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );

    }
}

ynsp_install401p7();

?>