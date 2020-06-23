<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/1/16
 * Time: 09:20
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Detail_Reviews extends Phpfox_Component
{
    public function process()
    {
        $aProduct = $this->getParam('aProduct');

        if($aVals = $this->request()->getArray('rating'))
        {
            if($iReviewId = Phpfox::getService('ynsocialstore.product.reviews')->add(Phpfox::getUserId(),$aVals))
            {
                $sLink = $this->url()->permalink('ynsocialstore.product', $aProduct['product_id'], $aProduct['name']).'tab_reviews';
                $this->url()->send($sLink,_p('thank_you_for_your_review'));
            }

        }
        $iPage = $this->request()->get('page') ? $this->request()->get('page') : 0;
        $iSize = 5;
        list($iCnt, $aReviews) = Phpfox::getService('ynsocialstore.product.reviews')->getReviewsByProductId($aProduct['product_id'], $iPage, $iSize);
        $iLimit = 150;
        foreach ($aReviews as $key_review => $aReview) {
            $aReviews[$key_review]['time'] = date('g:i A',$aReview['timestamp']);
            $aReviews[$key_review]['date'] = date('j F, Y',$aReview['timestamp']);
            if (strlen($aReview['content']) > $iLimit) {
                $aReviews[$key_review]['showmore'] = true;
                $aReviews[$key_review]['less'] = substr($aReviews[$key_review]['content'], 0, $iLimit);
            } else {
                $aReviews[$key_review]['showmore'] = false;
            }
        }
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iSize, 'count' => $iCnt));
        $aReviewedByUser = Phpfox::getService('ynsocialstore.product.reviews')->getExistingReview($aProduct['product_id'], Phpfox::getUserId());

        $this->template()->assign([
                                      'aProduct' => $aProduct,
                                      'aReviews' => $aReviews,
                                      'aReviewedByUser' => $aReviewedByUser,
                                      'iPage' => (int)$iPage + 1,
                                      'iSize' => $iSize,
                                      'canEditReview' => Phpfox::getParam('ynsocialstore.allow_user_edit_review_products') ? 1 : 0,
                                      'canDeleteOwnReview' => Phpfox::getParam('ynsocialstore.allow_user_delete_their_review_products') ? 1 : 0,
                                      'canDeleteOtherReview' => Phpfox::getParam('ynsocialstore.allow_owner_store_delete_review_on_their_products') ? 1 : 0
                                  ]);

    }
}