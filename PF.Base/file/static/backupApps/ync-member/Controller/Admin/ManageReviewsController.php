<?php

namespace Apps\YNC_Member\Controller\Admin;

use Phpfox;
use Phpfox_Component;

class ManageReviewsController extends Phpfox_Component
{
    public function process()
    {
        // Page Number & Limit Per Page
        $iPage = $this->getParam('page');
        $iPageSize = 10;
        $aVals = [
            'review_by' => '',
            'review_for'	=> '',
            'title' =>'',
            'from' =>'',
            'to' =>'',
        ];
        $aConds = [];

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set([
            'type' 	 => 'request',
            'search' => 'search',
        ]);
        $bIsSearch = false;

        $aSearch = $this->getParam('search');
        if($aSearch){
            $aVals['review_by'] = $aSearch['review_by'];
            $aVals['review_for'] = $aSearch['review_for'];
            $aVals['title'] = $aSearch['title'];
            $aVals['from'] = $aSearch['from'];
            $aVals['to'] = $aSearch['to'];
            $bIsSearch = true;
        }

        if(isset($aVals['review_by']) && trim($aVals['review_by']) != '')
        {
            $aConds[] = "AND reviewer.full_name LIKE '%{$aVals['review_by']}%'";
        }
        if(isset($aVals['review_for']) && trim($aVals['review_for']) != '')
        {
            $aConds[] = "AND reviewed.full_name LIKE '%{$aVals['review_for']}%'";
        }
        if(isset($aVals['title']) && trim($aVals['title']))
        {
            $aConds[] = "AND re.title like '%{$aVals['title']}%'";
        }
        if(isset($aVals['from']) && trim($aVals['from']) != '')
        {
            $iFrom = strtotime($aVals['from'] . '00:00:01');
            $aConds[] = "AND re.time_stamp >= $iFrom";
        }
        if(isset($aVals['to']) && trim($aVals['to']) != '')
        {
            $iTo = strtotime($aVals['to'] . '23:59:59');
            $aConds[] = "AND re.time_stamp <= $iTo";
        }

        list($iCount,$aList) = Phpfox::getService('ynmember.review.browse')->getForManage($aConds, $iPage, $iPageSize);

        // Set pager
        phpFox::getLib('pager')->set([
            'page'  => $iPage,
            'size'  => $iPageSize,
            'count' => $iCount,
            'ajax' => 'ynmember.changePageManageReviews',
            'popup'	=> true,
        ]);

        $corePath = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-member';

        $this -> template() -> setTitle(_p('Members'));
        $this -> template() -> assign([
            'aList' => $aList,
            'aForms' => $aVals,
            'corePath' => $corePath,
            'sUrl' => $this->url()->makeUrl('admincp.ynmember.managereviews'),
            'bIsSearch' => $bIsSearch,
        ]);
    }
}