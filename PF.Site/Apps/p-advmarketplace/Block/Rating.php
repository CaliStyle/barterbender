<?php
namespace Apps\P_AdvMarketplace\Block;

use Phpfox;
use Phpfox_Component;

/**
 * Class Rating
 * @package Apps\P_AdvMarketplace\Block
 */
class Rating extends Phpfox_Component
{
    public function process() {
        if(!defined('PHPFOX_ADVANCEDMARKETPLACE_DETAIL') || (defined('PHPFOX_ADVANCEDMARKETPLACE_DETAIL') && !PHPFOX_ADVANCEDMARKETPLACE_DETAIL)) {
            return false;
        }

        define('PHPFOX_ADVANCEDMARKETPLACE_RATING', true);

        $listing = $this->getParam('aListing');
        if(empty($listing)) {
            return false;
        }
        Phpfox::getService('advancedmarketplace.rate')->getReviewPermission($listing);
        $this->template()->assign([
           'aListing' => $listing
        ]);

        return 'block';
    }
}