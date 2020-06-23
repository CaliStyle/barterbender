<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:26
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;

class CommissionTrackingController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $iIsAffiliate =  Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate(Phpfox::getUserId());
        if($iIsAffiliate != 'approved' && $iIsAffiliate != 'inactive')
        {
            $this->url()->send('affiliate',_p('you_do_not_have_permission_to_view_this_page'));
        }
        $iPage = $this->request()->getInt('page',1);
        // Search Filter
        $aConds = [];
        $iPageSize = 10;
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));
        $aVals['client_name'] = $oSearch->get('client_name');
        $aVals['status']         = $oSearch->get('status');
        $aVals['payment_type']   = $oSearch->get('payment_type');
        $sFromDate = $this->request()->get('js_start_time__datepicker');
        $sToDate = $this->request()->get('js_end_time__datepicker');
        $iId = $this->request()->get('id');
        if((int)$iId)
        {
            $aConds[] = ' AND yc.commission_id ='.$iId;
        }
        $aConds[] = ' AND u1.user_id = '.(int)Phpfox::getUserId();
        if ($aVals['client_name'])
        {
            $aConds[] = "AND u2.full_name like '%{$aVals['client_name']}%'";
        }
        if ($aVals['payment_type'] != '')
        {
            $aConds[] = "AND yc.purchase_type = ".$aVals['payment_type'];
        }
        if ($aVals['status'] != '')
        {
            $aConds[] = "AND yc.status = '".$aVals['status']."'";

        }
        if($sFromDate)
        {
            $iFromTime = strtotime($sFromDate);
            $aConds[] = "AND yc.time_stamp >= {$iFromTime}";
        }
        if($sToDate)
        {
            $iToTime = strtotime($sToDate)+23*60*60+59*60+59;
            $aConds[] = "AND yc.time_stamp <= {$iToTime}";
        }
        if(!$sFromDate && !$sToDate){
            $aVals['start_time_day'] = $aVals['end_time_day'] = Phpfox::getTime('j');
            $aVals['start_time_month'] = $aVals['end_time_month'] = Phpfox::getTime('n');
            $aVals['start_time_year']  = $aVals['end_time_year'] = Phpfox::getTime('Y');
        }
        else{
            $aFromDate = explode('/', $sFromDate);
            $aVals['start_time_month'] = $aFromDate[0];
            $aVals['start_time_day'] = $aFromDate[1];
            $aVals['start_time_year'] = $aFromDate[2];

            $aToDate = explode('/', $sToDate);
            $aVals['start_time_month'] = $aToDate[0];
            $aVals['start_time_day'] = $aToDate[1];
            $aVals['start_time_year'] = $aToDate[2];
        }
        $this->template()->setTitle(_p('commissions_tracking'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('affiliate'))
            ->setBreadCrumb(_p('commissions_tracking'),$this->url()->makeUrl('affiliate.commission-tracking'));
        list($iCount,$aTrackings) = Phpfox::getService('yncaffiliate.commission')->getManageCommissions($aConds,$iPage,$iPageSize,true);
        $iTotalApproved = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id ='.Phpfox::getUserId().' and status = \'approved\'');
        $iTotalDelaying = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id ='.Phpfox::getUserId().' and status = \'delaying\'');
        $iTotalWaiting = Phpfox::getService('yncaffiliate.commission')->countCommission('user_id ='.Phpfox::getUserId().' and status = \'waiting\'');
        $this->template()->assign([
            'aTrackings' => $aTrackings,
            'iTotalApproved' => $iTotalApproved,
            'iTotalDelaying' => $iTotalDelaying,
            'iTotalWaiting' => $iTotalWaiting,
            'aRules' => Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getActiveRules(),
            'sFromDate' => $sFromDate,
            'sToDate' => $sToDate,
            'aForms'  => $aVals,
            'iPage' => $iPage
        ]);
        \Phpfox_Pager::instance()->set([
            'page'  => $iPage,
            'size'  => $iPageSize,
            'count' => $iCount,
        ]);
    }
}