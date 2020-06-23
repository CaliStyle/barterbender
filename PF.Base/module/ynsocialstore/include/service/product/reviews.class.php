<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/18/16
 * Time: 11:14
 */
class Ynsocialstore_Service_Product_Reviews extends Phpfox_Service
{
    /**
     * Reviews constructor.
     */
    public function __construct()
    {
        $this->_sTable =  Phpfox::getT('ecommerce_review');
    }

    /**
     * @param $iUserId
     * @param $iStoreId
     *
     * @return int | null
     */
    public function findId($iUserId, $iProductId)
    {
        return (int) $this->database()->select('review_id')
            ->from($this->_sTable)
            ->where(strtr('user_id=:user and product_id=:product',[
                ':user'=> intval($iUserId),
                ':product'=> intval($iProductId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * @param $iUserId
     * @param $iStoreId
     * @return bool
     */
    public function add($iUserId,$aVals)
    {
        $id = $this->findId($iUserId, $aVals['product_id']);

        if(!$id){
            $id = $this->database()->insert($this->_sTable, [
                'user_id'=>intval($iUserId),
                'product_id'=> intval($aVals['product_id']),
                'rating' => isset($aVals['rating']) ? $aVals['rating'] : 0,
                'content' => empty($aVals['content']) ? '' : $aVals['content'],
                'content_parsed' => empty($aVals['content']) ? '' : Phpfox::getLib('parse.input')->clean($aVals['content']),
                'title' => _p('review'),
                'timestamp'=> time(),
            ]);
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($aVals['product_id']);
            Phpfox::getService("notification.process")->add("ynsocialstore_reviewproduct",$aVals['product_id'], $aProduct['user_id'], Phpfox::getUserId());
            $this->database()->updateCounter('ecommerce_product', 'total_review', 'product_id', $aVals['product_id']);
            $this->database()->updateCounter('ecommerce_product_ynstore', 'total_rating', 'product_id', $aVals['product_id']);
        }
        else{
            if(!Phpfox::getParam('ynsocialstore.allow_user_edit_review_products'))
            {
                return Phpfox_Error::set(_p('You do not have permission to edit your review.'));
            }
            $this->database()->update($this->_sTable,[
                'timestamp' => time(),
                'rating' => isset($aVals['rating']) ? $aVals['rating'] : 0,
                'content' => empty($aVals['content']) ? '' : $aVals['content'],
                'content_parsed' => empty($aVals['content']) ? '' : Phpfox::getLib('parse.input')->clean($aVals['content']),
            ],'review_id ='.$id);
        }
        $this->database()->update(Phpfox::getT('ecommerce_product_ynstore'),[
            'rating'=> (float)$this->getAVGRatingOfProduct($aVals['product_id']),
        ], 'product_id='. intval($aVals['product_id']));

        return $id;
    }

    /**
     * @param int $iUserId
     * @param int $iStoreId
     * @return bool
     */
    public function isReview($iUserId, $iProductId)
    {
        return $this->findId($iUserId, $iProductId) !=0;
    }

    /**
     * @param $iStoreId
     * @param int $iPage
     * @param int $iLimit
     * @return array
     */
    public function getReviewsByProductId($iProductId, $iPage = 0, $iLimit = 1)
    {

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'er')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = er.user_id')
            ->where('er.product_id =' . (int)$iProductId)
            ->execute('getSlaveRows');
        $iUserId = Phpfox::getUserId();
        $sOrder = !empty($iUserId) ? 'field(er.user_id,'.$iUserId.') DESC, er.review_id ASC' : 'er.review_id ASC';
        $aProductReviews = $this->database()->select('er.*,' . Phpfox::getUserField())
            ->from($this->_sTable, 'er')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = er.user_id')
            ->where('er.product_id =' . (int)$iProductId)
            ->order($sOrder)
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');


        foreach ($aProductReviews as $key => $aReview) {

            $total_score_around = (int)$aProductReviews[$key]['rating'];
            $aProductReviews[$key]['total_score_text'] = '';
            for ($i = 1; $i <= $total_score_around; $i++) {
                $aProductReviews[$key]['total_score_text'] .= '<i class="ico ico-star yn-rating" ><a title="'.$i.'"></a></i>';
            }
            for ($i = 1; $i <= (5 - $total_score_around); $i++) {
                $aProductReviews[$key]['total_score_text'] .= '<i class="ico ico-star yn-rating-disable" ><a title=""></a></i>';
            }

        }


        return array($iCnt, $aProductReviews);
    }

    /**
     * @param $iReviewId
     * @return mixed
     */
    public function getReviewsById($iReviewId)
    {

        $aReview = $this->database()->select('er.*')
            ->from($this->_sTable, 'er')
            ->where('er.review_id =' . (int)$iReviewId)
            ->execute('getSlaveRow');

        return $aReview;
    }

    /**
     * @param $iStoreId
     * @return bool
     */
    public function getAVGRatingOfProduct($iStoreId)
    {
        if(!$iStoreId)
            return false;
        $iAvg = $this->database()->select("AVG(rating)")
            ->from($this->_sTable, 'er')
            ->where('er.product_id = '.(int)$iStoreId)
            ->execute("getSlaveField");

        return $iAvg;
    }
    public function getExistingReview($iProductId, $iUserId)
    {

        $aReview = $this->database()->select('er.*')
            ->from($this->_sTable, 'er')
            ->where('er.product_id =' . (int)$iProductId . ' AND er.user_id =' . (int)$iUserId)
            ->execute('getSlaveRow');

        return $aReview;
    }
    public function deleteReview($iReviewId,$iProductId)
    {
        $this->database()->delete($this->_sTable,'review_id='. intval($iReviewId));
        $this->database()->updateCounter('ecommerce_product', 'total_review', 'product_id', $iProductId,true);
        $this->database()->updateCounter('ecommerce_product_ynstore', 'total_rating', 'product_id', $iProductId,true);
        $this->database()->update(Phpfox::getT('ecommerce_product_ynstore'),[
            'rating'=> (float)$this->getAVGRatingOfProduct($iProductId),
        ], 'product_id='. intval($iProductId));
        return true;
    }
}