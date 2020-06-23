<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Feed extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iFeedId = $this->getParam('this_feed_id');
        if ($iFeedId) {
            $aFeed = Phpfox::getService('feed')->getFeed($iFeedId);
            if (!$aFeed || ($aFeed['type_id'] != 'advancedmarketplace')) {
                return false;
            }

            $aListing= Phpfox::getService('advancedmarketplace')->getListing($aFeed['item_id']);
            if($aListing['price'] != '0.00') {
                $aListing['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
            }
            if (empty($aListing)) {
                return false;
            }

            $this->template()->assign(compact('aListing'))->assign('isEmbed',false);
        }
        // else case: we set aListing to template in \AdvancedMarketplace_Service_Callback::getActivityFeed

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_feed_clean')) ? eval($sPlugin) : false);
    }
}
