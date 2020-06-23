<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class ListingstatisticsController extends Phpfox_Component
{
    public function process()
    {
        $aListingStatistics = phpfox::getService('advancedmarketplace')->getListingStatistics();

        $this->template()->assign(array('aListingStatistics' => $aListingStatistics))
            ->setTitle('Listing Statistics')
            ->setBreadcrumb('Listing Statistics', $this->url()->makeUrl('admincp.marketplace.listingstatistics'));
    }
}
