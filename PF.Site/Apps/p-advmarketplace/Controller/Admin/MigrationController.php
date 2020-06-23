<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class MigrationController extends Phpfox_Component
{
    public function process()
    {
        $sRequest = $this->request()->get('isclone');
        if ($sRequest == 1) {
            phpfox::getService('advancedmarketplace.process')->migrateMarketplaceData();
        }
        $sMigrateUrl = $this->url()->makeUrl('admincp.advancedmarketplace.migration.isclone_1');
        $this->template()->assign(array(
            'sMigrateUrl' => $sMigrateUrl
        ));
        $this->template()
            ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('advancedmarketplace'),
                $this->url()->makeUrl('admincp.app', ['id' => '__module_advancedmarketplace']))
            ->setBreadcrumb(_p('advancedmarketplace.migration'),
                $this->url()->makeUrl('admincp.advancedmarketplace.migration'));

    }
}
