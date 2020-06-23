<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Owner extends Phpfox_Component
{
    public function process()
    {
        $aListing = $this->getParam('aListing');

        $aListing['total_listing'] = Phpfox::getService('advancedmarketplace')->getTotalListings($aListing['user_id'], false);

        $aFollower = Phpfox::getService('advancedmarketplace')->getMyFollowerByUserId($aListing['user_id']);

        $isFollow = false;

        if (!empty($aFollower) && Phpfox::isUser()) {
            $isFollow = true;
        }

        Phpfox::getService('advancedmarketplace')->getMoreContactUserInfomation($aListing);

        $this->template()->assign(array(
            'isFollow' => $isFollow,
            'aItem' => $aListing,
            'sHeader' => _p('advancedmarketplace.seller'),
            'iFollower' => Phpfox::getUserId(),
            'sideBlock' => true,
            'showClearListing' => false,
            'sCustomClassName' => 'p-block',
        ));

        return 'block';
    }
}
