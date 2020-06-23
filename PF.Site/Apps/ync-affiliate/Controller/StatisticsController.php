<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:28
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
class StatisticsController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $iIsAffiliate =  Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate(Phpfox::getUserId());
        if($iIsAffiliate != 'approved' && $iIsAffiliate != 'inactive')
        {
            $this->url()->send('affiliate',_p('you_do_not_have_permission_to_view_this_page'));
        }
        $this->template()->setTitle(_p('statistics'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('affiliate'))
            ->setBreadCrumb(_p('statistics'),$this->url()->makeUrl('affiliate.statistics'));
        $iUserId = $this->request()->getInt('id',0);
        if(!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }
        //get total number
        $iTotalPayment = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id = '.$iUserId);
        $iTotalApproved = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id ='.$iUserId.' and status = \'approved\'');
        $iTotalDelaying = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id ='.$iUserId.' and status = \'delaying\'');
        $iTotalWaiting = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id ='.$iUserId.' and status = \'waiting\'');
        $iTotalDenied = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id ='.$iUserId.' and status = \'denied\'');

        //get total point
        $iTotalEarningPoints = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints($iUserId,'approved');
        $iTotalRecievedPoints = Phpfox::getService('yncaffiliate.request')->getTotalRequestPoints('\'completed\'',$iUserId);
        $iTotalPendingPoints = Phpfox::getService('yncaffiliate.request')->getTotalRequestPoints('\'pending\',\'waiting\'',$iUserId);
        $iComApprovedPoint = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints($iUserId,'approved');
        $iComDelayingPoint = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints($iUserId,'delaying');
        $iComWaitingPoint = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints($iUserId,'waiting');
        $iComDeniedPoint = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints($iUserId,'denied');
        if($iTotalEarningPoints >= ($iTotalRecievedPoints + $iTotalPendingPoints))
        {
            $iTotalAvailablePoints = $iTotalEarningPoints - $iTotalRecievedPoints - $iTotalPendingPoints;
        }
        else{
            $iTotalAvailablePoints = 0;
        }
        $aVals = [];
        $aVals['from_day'] = Phpfox::getTime('j');
        $aVals['from_month'] = Phpfox::getTime('n');
        $aVals['from_year'] = Phpfox::getTime('Y');
        $aVals['to_day'] = Phpfox::getTime('j');
        $aVals['to_month'] = Phpfox::getTime('n');
        $aVals['to_year'] = Phpfox::getTime('Y');
        $this->template()->assign([
            'iTotalPayment' => $iTotalPayment,
            'iTotalCommissionPoint' => $iTotalEarningPoints,
            'iTotalAvailablePoint' => $iTotalAvailablePoints,
            'iTotalApproved' => $iTotalApproved,
            'iTotalDelaying' => $iTotalDelaying,
            'iTotalWaiting' => $iTotalWaiting,
            'iComApprovedPoint' => $iComApprovedPoint,
            'iComDelayingPoint' => $iComDelayingPoint,
            'iComWaitingPoint' => $iComWaitingPoint,
            'iComDeniedPoint' => $iComDeniedPoint,
            'iTotalDenied' => $iTotalDenied,
            'iUserId' => $iUserId,
            'aForms' => $aVals
        ]);
    }
}