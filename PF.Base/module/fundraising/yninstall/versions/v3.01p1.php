<?php
	
defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 * @by MinhTA
 *  
 */
function ynfr_install301p1() {
	$oDatabase = Phpfox::getLib('database') ;

    if (Phpfox::isModule('socialpublishers'))
    {
        $aRow = $oDatabase->select('*')
            ->from(Phpfox::getT('socialpublishers_modules'))
            ->where('product_id = "younet_fundraising" AND module_id = "fundraising"')
            ->execute('getRow');

        if(!isset($aRow) && !$aRow)
        {
            $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
                array(
                    'product_id' => 'younet_fundraising',
                    'module_id' => 'fundraising',
                    'title' => 'fundraising.fundraisings',
                    'is_active' => 1,
                    'facebook' => 1,
                    'twitter' => 1,
                    'linkedin' => 1,
                )
            );
        }
    }
}

ynfr_install301p1();

?>
