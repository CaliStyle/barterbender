<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsp_install302p7() {
    $oDatabase = Phpfox::getLib('database') ;

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "advancedphoto" AND module_id = "advancedphoto"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'advancedphoto',
                'module_id' => 'advancedphoto',
                'title' => 'advancedphoto.advancedphoto',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "advanced_marketplace" AND module_id = "advancedmarketplace"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'advanced_marketplace',
                'module_id' => 'advancedmarketplace',
                'title' => 'advancedmarketplace.advancedmarketplace',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "younet_petition" AND module_id = "petition"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younet_petition',
                'module_id' => 'petition',
                'title' => 'petition.petition',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "younetevent" AND module_id = "fevent"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younetevent',
                'module_id' => 'fevent',
                'title' => 'fevent.module_fevent',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "younetkaraoke" AND module_id = "karaoke"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'younetkaraoke',
                'module_id' => 'karaoke',
                'title' => 'karaoke.karaoke',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "musicsharing" AND module_id = "musicsharing"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'musicsharing',
                'module_id' => 'musicsharing',
                'title' => 'musicsharing.music_sharing',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }

    $aRow = $oDatabase->select('*')
        ->from(Phpfox::getT('socialpublishers_modules'))
        ->where('product_id = "musicstore" AND module_id = "musicstore"')
        ->execute('getRow');

    if(empty($aRow) && !$aRow)
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'musicstore',
                'module_id' => 'musicstore',
                'title' => 'musicstore.music_store',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }

}

ynsp_install302p7();
?>
