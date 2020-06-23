<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsp_install305() {
	$oDatabase = Phpfox::getLib('database') ;
   $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "younet_contest" AND module_id = "contest"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_contest',
                'module_id' => 'contest',
                'title' => 'socialpublishers.publishers_contest',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
		
		 $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_jobposting',
                'module_id' => 'jobposting',
                'title' => 'socialpublishers.publishers_jobposting',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
		
		 $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_coupon',
                'module_id' => 'coupon',
                'title' => 'socialpublishers.publishers_coupon',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
		
		$oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_fundraising',
                'module_id' => 'fundraising',
                'title' => 'socialpublishers.publishers_fundraising',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
		
		 $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_document',
                'module_id' => 'document',
                'title' => 'socialpublishers.publishers_document',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
                 
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_resume',
                'module_id' => 'resume',
                'title' => 'socialpublishers.publishers_resume',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }
}
ynsp_install305();
?>
