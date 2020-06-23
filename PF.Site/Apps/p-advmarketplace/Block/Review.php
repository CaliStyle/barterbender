<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;

class Review extends Phpfox_Component
{
    public function process()
    {
        $aListing = ($this->getParam("aListing"));
        $aRating = ($this->getParam("aRating"));
        $iCount = ($this->getParam("iCount"));
        $iPage = ($this->getParam("iPage", "0"));
        $iSize = ($this->getParam("iSize"));

        $aParam = array(
            'ajax' => 'advancedmarketplace.reviewpaging',
            'page' => $iPage,
            'size' => $iSize,
            'count' => $iCount,
            'aParams' => array(
                'lid' => $aListing['listing_id']
            )
        );
        Phpfox::getLib('pager')->set($aParam);

        $aReview = phpfox::getService('advancedmarketplace')->getExistingReview($aListing['listing_id'],
            phpfox::getUserId());
        if (!empty($aReview)) {
            $aListing['isReviewed'] = true;
        } else {
            $aListing['isReviewed'] = false;
        }

        $this->template()->assign(array(
            'corepath' => phpfox::getParam('core.path'),
            'aListing' => $aListing,
            'iCount' => $iCount,
            'aRating' => $aRating,
            'page' => $iPage,
            'sHeader' => '',
            "iCurrentUserId" => Phpfox::getUserId()
        ));
         return 'block';
    }
}
