<?php
/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 2/24/17
 * Time: 6:45 PM
 */

namespace Apps\YNC_Member\Service\Review;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;

class Browse extends \Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynmember_review');
    }

    public function query()
    {
        if($this->search()->get('form_flag') != 1 && !$this->request()->get('sort') && !$this->request()->get('when') && !$this->request()->get('search')) {
            $this->database()->group('re.item_id');
        } else {
            $this->database()->select('COUNT(CASE WHEN useful.positive = 1 THEN 1 ELSE NULL END) as total_yes, COUNT(CASE WHEN useful.positive = 0 THEN 1 ELSE NULL END) as total_no, ')
                ->leftJoin(Phpfox::getT('user'), 'reviewer', 're.user_id = reviewer.user_id')
                ->leftJoin(Phpfox::getT('ynmember_review_useful'), 'useful', 're.review_id = useful.review_id');
            $this->database()->group('re.review_id');
        }
    }

    public function getQueryJoins()
    {
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynmember_review\' AND l.item_id = re.review_id AND l.user_id = ' . Phpfox::getUserId())
                ->leftJoin(Phpfox::getT('user'), 'reviewer', 're.user_id = reviewer.user_id');
        }
    }

    public function getReviewsOfUser($iUserId, $iPage, $iPageSize)
    {
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynmember_review\' AND l.item_id = re.review_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRows = $this->database()->select('re.*, COUNT(CASE WHEN useful.positive = 1 THEN 1 ELSE NULL END) as total_yes, COUNT(CASE WHEN useful.positive = 0 THEN 1 ELSE NULL END) as total_no')
            ->from($this->_sTable, 're')
            ->leftJoin(Phpfox::getT('ynmember_review_useful'), 'useful', 're.review_id = useful.review_id')
            ->where('re.item_id = ' . (int)$iUserId)
            ->group('re.review_id')
            ->limit($iPage, $iPageSize)
            ->executeRows();

        $this->processRows($aRows);

        return $aRows;
    }

    public function getForManage($aConds = [], $iPage = 0, $iLimit = NULL)
    {
        $sWhere = '1=1';
        $aRows= [];

        if (count($aConds) > 0) {
            $sCond = implode(' ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = $this->database()
            ->select("COUNT(re.review_id)")
            ->from($this->_sTable, 're')
            ->leftJoin(Phpfox::getT("user"), 'reviewer', 'reviewer.user_id = re.user_id')
            ->leftJoin(Phpfox::getT("user"), 'reviewed', 'reviewed.user_id = re.item_id')
            ->where($sWhere)
            ->executeField();

        if($iCount){
            $aRows = $this->database()
                ->select("re.*, reviewer.full_name as review_by, reviewed.full_name as review_for ")
                ->from($this->_sTable, 're')
                ->leftJoin(Phpfox::getT("user"), 'reviewer', 'reviewer.user_id = re.user_id')
                ->leftJoin(Phpfox::getT("user"), 'reviewed', 'reviewed.user_id = re.item_id')
                ->where($sWhere)
                ->order('re.time_stamp DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->executeRows();
        }
        return [$iCount, $aRows];
    }

    public function processRows(&$aRows)
    {
        foreach ($aRows as $key => $aRow) {
            Phpfox::getService('ynmember.review')->processRow($aRows[$key]);
        }
    }
}