<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/18/16
 * Time: 11:05
 */
class Ynsocialstore_Component_Block_Store_Detailreviews extends Phpfox_Component
{
    public function process()
    {
        if($aVals = $this->request()->getArray('rating'))
        {
            if($iReviewId = Phpfox::getService('ynsocialstore.reviews')->add(Phpfox::getUserId(),$aVals))
            {
                $this->url()->send('current',_p('thank_you_for_your_review'));
            }

        }
        $iPage = $this->request()->get('page') ? $this->request()->get('page') : 0;
        $iSize = 10;
        $aStore = $this->getParam('aStore');
        list($iCnt, $aReviews) = Phpfox::getService('ynsocialstore.reviews')->getReviewsByStoreId($aStore['store_id'], $iPage, $iSize);
        $iLimit = 150;
        foreach ($aReviews as $key_review => $aReview) {
            $aReviews[$key_review]['time'] = date('g:i A',$aReview['time_stamp']);
            $aReviews[$key_review]['date'] = date('j F, Y',$aReview['time_stamp']);
            if (strlen($aReview['content']) > $iLimit) {
                $aReviews[$key_review]['showmore'] = true;
                $aReviews[$key_review]['less'] = substr($aReviews[$key_review]['content'], 0, $iLimit);
            } else {
                $aReviews[$key_review]['showmore'] = false;
            }
        }
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iSize, 'count' => $iCnt));
        $aReviewedByUser = Phpfox::getService('ynsocialstore.reviews')->getExistingReview($aStore['store_id'], Phpfox::getUserId());

        $this->template()->assign([
                'aStore' => $aStore,
                'aReviews' => $aReviews,
                'aReviewedByUser' => $aReviewedByUser,
                'iPage' => $iPage,
                'canEditReview' => Phpfox::getParam('ynsocialstore.allow_user_edit_review_stores') ? 1 : 0,
                'canDeleteOwnReview' => Phpfox::getParam('ynsocialstore.allow_user_delete_their_review_stores') ? 1 : 0,
                'canDeleteOtherReview' => Phpfox::getParam('ynsocialstore.allow_owner_store_delete_review_on_their_stores') ? 1 : 0
                                  ]);

    }
}