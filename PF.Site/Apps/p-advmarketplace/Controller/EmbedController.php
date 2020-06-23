<?php

namespace Apps\P_AdvMarketplace\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class EmbedController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $listingId = $this->request()->getInt('req3');

        if (!$listingId) {
            exit(_p('invalid_param'));
        }

        $listing = Phpfox::getService('advancedmarketplace')->getListing($listingId);

        if (!$listing) {
            exit(_p('unable_to_view_this_item_due_to_privacy_settings'));
        }

        $listing['listing_price'] = Phpfox::getService('core.currency')->getCurrency($listing['price'], $listing['currency_id']);

        $this->template()->setTitle(Phpfox::getLib('locale')->convert($listing['title']));

        $this->template()->assign(array(
            'aListing' => $listing,
            'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/p-advmarketplace/',
            'isEmbed' => true
        ));
        Phpfox::getLib('module')->getControllerTemplate();
        die;
    }
}
