<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/18/16
 * Time: 11:14
 */
class Ynsocialstore_Service_Reviews extends Phpfox_Service
{
    /**
     * Reviews constructor.
     */
    public function __construct()
    {
        $this->_sTable =  Phpfox::getT('ynstore_store_review');
    }

    /**
     * @param $iUserId
     * @param $iStoreId
     *
     * @return int | null
     */
    public function findId($iUserId, $iStoreId)
    {
        return (int) $this->database()->select('review_id')
            ->from($this->_sTable)
            ->where(strtr('user_id=:user and store_id=:store',[
                ':user'=> intval($iUserId),
                ':store'=> intval($iStoreId),
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
        $id = $this->findId($iUserId, $aVals['store_id']);

        if(!$id){
            $id = $this->database()->insert($this->_sTable, [
                'user_id'=>intval($iUserId),
                'store_id'=> intval($aVals['store_id']),
                'rating' => isset($aVals['rating']) ? $aVals['rating'] : 0,
                'content' => empty($aVals['content']) ? '' : $aVals['content'],
                'time_stamp'=> time(),
            ]);
            $iOwner = Phpfox::getService('ynsocialstore')->getFieldsStoreById('user_id',$aVals['store_id'],'getField');
            Phpfox::getService("notification.process")->add("ynsocialstore_reviewstore",$aVals['store_id'], $iOwner, Phpfox::getUserId());
            $this->database()->updateCounter('ynstore_store', 'total_review', 'store_id', $aVals['store_id']);
        }
        else{
            if(!Phpfox::getParam('ynsocialstore.allow_user_edit_review_stores'))
            {
                return Phpfox_Error::set(_p('You do not have permission to edit your review.'));
            }
            $this->database()->update($this->_sTable,[
                        'time_stamp' => time(),
                        'rating' => isset($aVals['rating']) ? $aVals['rating'] : 0,
                        'content' => empty($aVals['content']) ? '' : $aVals['content'],
                                     ],'review_id ='.$id);
        }
        $this->database()->update(Phpfox::getT('ynstore_store'),[
            'rating'=> (float)$this->getAVGRatingOfStore($aVals['store_id']),
        ], 'store_id='. intval($aVals['store_id']));

        return $id;
    }

    /**
     * @param int $iUserId
     * @param int $iStoreId
     * @return bool
     */
    public function isReview($iUserId, $iStoreId)
    {
        return $this->findId($iUserId, $iStoreId) !=0;
    }

    /**
     * @param $iStoreId
     * @param int $iPage
     * @param int $iLimit
     * @return array
     */
    public function getReviewsByStoreId($iStoreId, $iPage = 0, $iLimit = 1)
    {

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'st')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->where('st.store_id =' . (int)$iStoreId)
            ->execute('getSlaveField');

        $aStoreReviews = $this->database()->select('st.*,' . Phpfox::getUserField())
            ->from($this->_sTable, 'st')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->where('st.store_id =' . (int)$iStoreId)
            ->order('field(st.user_id,'.Phpfox::getUserId().') DESC, st.review_id ASC')
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');


        foreach ($aStoreReviews as $key => $aReview) {

            $total_score_around = (int)$aStoreReviews[$key]['rating'];
            $aStoreReviews[$key]['total_score_text'] = '';
            for ($i = 1; $i <= $total_score_around; $i++) {
                $aStoreReviews[$key]['total_score_text'] .= '<i class="ico ico-star yn-rating" ><a title="'.$i.'"></a></i>';
            }
            for ($i = 1; $i <= (5 - $total_score_around); $i++) {
                $aStoreReviews[$key]['total_score_text'] .= '<i class="ico ico-star yn-rating-disable" ><a title=""></a></i>';
            }

        }


        return array($iCnt, $aStoreReviews);
    }

    /**
     * @param $iReviewId
     * @return mixed
     */
    public function getReviewsById($iReviewId)
    {

        $aReview = $this->database()->select('sr.*')
            ->from($this->_sTable, 'sr')
            ->where('sr.review_id =' . (int)$iReviewId)
            ->execute('getSlaveRow');

        return $aReview;
    }

    /**
     * @param $iStoreId
     * @return bool
     */
    public function getAVGRatingOfStore($iStoreId)
    {
        if(!$iStoreId)
            return false;
        $iAvg = $this->database()->select("AVG(rating)")
            ->from($this->_sTable, 'sr')
            ->where('sr.store_id = '.(int)$iStoreId)
            ->execute("getSlaveField");

        return $iAvg;
    }
    public function getExistingReview($iStoreId, $iUserId)
    {

        $aReview = $this->database()->select('sr.*')
            ->from($this->_sTable, 'sr')
            ->where('sr.store_id =' . (int)$iStoreId . ' AND sr.user_id =' . (int)$iUserId)
            ->execute('getSlaveRow');

        return $aReview;
    }
    public function deleteReview($iReviewId,$iStoreId)
    {
        $this->database()->delete($this->_sTable,'review_id='. intval($iReviewId));
        $this->database()->update(Phpfox::getT('ynstore_store'),[
            'rating'=> (float)$this->getAVGRatingOfStore($iStoreId),
        ], 'store_id='. intval($iStoreId));
        $this->database()->updateCounter('ynstore_store', 'total_review', 'store_id', $iStoreId,true);
        return true;
    }
}