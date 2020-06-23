<?php
namespace Apps\P_AdvMarketplace\Block;

use Phpfox;
use Phpfox_Component;

class ReviewerListing extends Phpfox_Component
{
    public function process()
    {
        $aListing = $this->getParam('aListing');
        if(empty($aListing)) {
            $listingId = $this->getParam('listing_id');
            if(!($aListing = Phpfox::getService('advancedmarketplace')->getListing($listingId)))
            {
                return false;
            }
        }
        $page = !empty($this->request()->get('page')) ? $this->request()->get('page') : 1;
        $limit = 10;
        list($reviewers, $count) = Phpfox::getService('advancedmarketplace.rate')->getReviewers($aListing['listing_id'], $page, $limit);


        // Set params for pagination
        $canContinuePaging =  true;
        if(count($reviewers) < $limit || ($page * $limit ) >= $count) {
            $canContinuePaging = false;
        }

        if($canContinuePaging) {
            $aParamsPager = array(
                'page' => $page,
                'size' => $limit,
                'count' => $count,
                'paging_mode' => 'loadmore',
                //configure for ajax paging
                'ajax_paging' => [
                    // block for paging content
                    'block' => 'advancedmarketplace.reviewer-listing',
                    // container to replace content
                    'container' => '.js_reviewer_listing',
                    'params' => [
                        'listing_id' => $aListing['listing_id']
                    ]
                ]
            );
        }

        Phpfox::getLib('pager')->set($aParamsPager);
        $this->template()->assign([
            'reviewers' => $reviewers,
            'listing_id' => $aListing['listing_id'],
            'canContinuePaging' => $canContinuePaging
        ]);
        return 'block';
    }
}