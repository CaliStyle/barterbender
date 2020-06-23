<?php
defined('PHPFOX') or exit('NO DICE!');

function ynpt_install301() {
    $oDatabase = Phpfox::getLib('database') ;

    if (Phpfox::isModule('socialpublishers'))
    {
        $oDatabase->insert(Phpfox::getT('socialpublishers_modules'),
            array(
                'product_id' => 'petition',
                'module_id' => 'petition',
                'title' => 'petition.publishers_petition',
                'is_active' => 1,
                'facebook' => 1,
                'twitter' => 1,
                'linkedin' => 1,
            )
        );
    }
}

ynpt_install301();

?>
