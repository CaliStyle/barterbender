<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailreviews extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {

        $aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
        $aBusiness = $aYnDirectoryDetail['aBusiness'];

        $iPage = $this->request()->get('page') ? $this->request()->get('page') : 0;
        $iSize = 12;
        list($iCnt, $aReviews) = Phpfox::getService('directory')->getReviewsByBusinessId($aBusiness['business_id'], $iPage, $iSize);

        $iLimit = 200;
        foreach ($aReviews as $key_review => $aReview) {

            $aReviews[$key_review]['timestamp'] = phpfox::getTime('F j, Y', $aReviews[$key_review]['timestamp'] );
            if (strlen($aReview['content']) > $iLimit) {
                $aReviews[$key_review]['showmore'] = true;
                $aReviews[$key_review]['less'] = substr($aReviews[$key_review]['content'], 0, $iLimit);
            } else {
                $aReviews[$key_review]['showmore'] = false;
            }
        }
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iSize, 'count' => $iCnt));

        $aReviewedByUser = Phpfox::getService('directory')->getExistingReview($aBusiness['business_id'], Phpfox::getUserId());
        $this->template()->assign(array(
                'aYnDirectoryDetail' => $aYnDirectoryDetail,
                'aBusiness' => $aBusiness,
                'aReviews' => $aReviews,
                'aReviewedByUser' => $aReviewedByUser,
                'iPage' => $iPage
            )
        );

    }

}

?>
