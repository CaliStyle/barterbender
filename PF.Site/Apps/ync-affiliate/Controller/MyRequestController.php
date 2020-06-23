<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:30
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;

class MyRequestController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $iUserId = Phpfox::getUserId();
        $iIsAffiliate =  Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate($iUserId);
        if($iIsAffiliate != 'approved' && $iIsAffiliate != 'inactive')
        {
            $this->url()->send('affiliate',_p('you_do_not_have_permission_to_view_this_page'));
        }
        $this->template()->setTitle(_p('my_requests'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('affiliate'))
            ->setBreadCrumb(_p('my_requests'),$this->url()->makeUrl('affiliate.my-request'));
        $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sCurrencySymbol = Phpfox::getService('core.currency')->getSymbol($sDefaultCurrency);
        $iPage = $this->request()->getInt('page',1);
        $iPageSize = 10;
        $aConds = $aVals = [];
        $sSort = '';
        $oSearch = Phpfox::getLib('search')->set(array(
            'type'   => 'request',
            'search' => 'search',
        ));
        $iId = $this->request()->get('id');
        if((int)$iId)
        {
            $aConds[] = ' AND yr.request_id ='.$iId;
        }
        $aConds[] = "AND yr.user_id = ".$iUserId;

        $sFromDate = $this->request()->get('js_from__datepicker');
        $sToDate = $this->request()->get('js_to__datepicker');

        if($sFromDate)
        {
            $iFromTime = strtotime($sFromDate);
            $aConds[] = "AND yr.time_stamp >= {$iFromTime}";
        }
        if($sToDate)
        {
            $iToTime = strtotime($sToDate)+23*60*60+59*60+59;
            $aConds[] = "AND yr.time_stamp <= {$iToTime}";
        }
        if(!$sFromDate && !$sToDate){
            $aVals['from_day'] = $aVals['to_day'] = Phpfox::getTime('j');
            $aVals['from_month'] = $aVals['to_month'] = Phpfox::getTime('n');
            $aVals['from_year']  = $aVals['to_year'] = Phpfox::getTime('Y');
        }
        else{
            $aFromDate = explode('/', $sFromDate);
            $aVals['from_month'] = $aFromDate[0];
            $aVals['from_day'] = $aFromDate[1];
            $aVals['from_year'] = $aFromDate[2];

            $aToDate = explode('/', $sToDate);
            $aVals['to_month'] = $aToDate[0];
            $aVals['to_day'] = $aToDate[1];
            $aVals['to_year'] = $aToDate[2];
        }
        if($this->request()->get('sortfield') !='' ){
            $sSortField = $this->request()->get('sortfield');
            Phpfox::getLib('session')->set('yncaffiliate_myrequest_sortfield',$sSortField);
        }
        $sSortField = Phpfox::getLib('session')->get('yncaffiliate_myrequest_sortfield');
        if($this->request()->get('sorttype') !='' ){
            $sSortType = $this->request()->get('sorttype');
            Phpfox::getLib('session')->set('yncaffiliate_myrequest_sorttype',$sSortType);
        }
        $sSortType = Phpfox::getLib('session')->get('yncaffiliate_myrequest_sorttype');
        $sSortFieldDB = 'yr.time_stamp';
        switch ($sSortField) {
            case 'request-date':
                $sSortFieldDB = 'yr.time_stamp';
                break;
            case 'amount':
                $sSortFieldDB = 'yr.request_amount';
                break;
            case 'point':
                $sSortFieldDB = 'yr.request_points';
                break;
            case 'response-date':
                $sSortFieldDB = 'yr.modify_time';
                break;
            default:
                break;
        }
        $sSort = $sSortFieldDB.' '.$sSortType;
        list($iCnt, $aRequests) = Phpfox::getService('yncaffiliate.request')->getRequest($aConds,$sSort,$iPage,$iPageSize);
        if($iId = $this->request()->getInt('delete'))
        {
            $aRequest = Phpfox::getService('yncaffiliate.request')->get($iId);
            if(!$aRequest || $aRequest['request_status'] != 'waiting' || $aRequest['user_id'] != (int)$iUserId)
            {
                $this->url()->send('affiliate.my-request',_p('you_do_not_have_permission_to_cancel_this_request'));
            }
            else{
                if(Phpfox::getService('yncaffiliate.request.process')->delete($iId))
                {
                    $this->url()->send('affiliate.my-request',_p('your_request_has_been_canceled_successfully'));
                }
                else{
                    $this->url()->send('affiliate.my-request',_p('something_went_wrong_please_try_again'));
                }
            }
        }
        //get points
        $iTotalEarningPoints = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints($iUserId,'approved');
        $iTotalRecievedPoints = Phpfox::getService('yncaffiliate.request')->getTotalRequestPoints('\'completed\'',$iUserId);
        $iTotalPendingPoints = Phpfox::getService('yncaffiliate.request')->getTotalRequestPoints('\'pending\',\'waiting\'',$iUserId);
        if($iTotalEarningPoints >= ($iTotalRecievedPoints + $iTotalPendingPoints))
        {
            $iTotalAvailablePoints = $iTotalEarningPoints - $iTotalRecievedPoints - $iTotalPendingPoints;
        }
        else{
            $iTotalAvailablePoints = 0;
        }
        //get amount

        $sCustomBaseLink = $this->url()->makeUrl('affiliate.my-request');
        $sCustomBaseLink = preg_replace('/\?page=(.?)/i', '', $sCustomBaseLink);
        $sCustomBaseLink = preg_replace('/\&page=(.?)/i', '', $sCustomBaseLink);
        $sCustomBaseLink = str_replace('sortfield_' . $this->request()->get('sortfield') . '/', '', $sCustomBaseLink);
        $sCustomBaseLink = str_replace('sorttype_' . $this->request()->get('sorttype') . '/', '', $sCustomBaseLink);
        $sCustomBaseLink = str_replace('?sortfield=' . $this->request()->get('sortfield') , '', $sCustomBaseLink);
        $sCustomBaseLink = str_replace('&sorttype=' . $this->request()->get('sorttype'), '', $sCustomBaseLink);
        $sCustomBaseLink = htmlspecialchars($sCustomBaseLink);
        $iMinRequestPoints = setting('ynaf_minimum_request_points','1');
        $iMaxRequestPoints = setting('ynaf_maximum_request_points','10');

        $fConvertValue = Phpfox::getService('yncaffiliate.commission.process')->convertPointToRealMoney(1,$sDefaultCurrency);
        $this->template()->assign([
                'iTotalEarning' => $iTotalEarningPoints,
                'iTotalRecieved' => $iTotalRecievedPoints,
                'iTotalPending' => $iTotalPendingPoints,
                'iTotalAvailable' => $iTotalAvailablePoints,
                'iMinRequestAmount' => $iMinRequestPoints,
                'iMaxRequestAmount' => $iMaxRequestPoints,
                'sCurrencySymbol' => $sCurrencySymbol,
                'sDefaultCurrency' => $sDefaultCurrency,
                'fConvertValue' => $fConvertValue,
                'aRequests'     => $aRequests,
                'aAccount'      => Phpfox::getService('yncaffiliate.affiliate.affiliate')->getDetail($iUserId),
                'iPage'     => $iPage,
                'aForms' =>  $aVals,
                'sCustomBaseLink' => $sCustomBaseLink,
                'sSortField' => $sSortField,
                'sSortType' => $sSortType,
                'iCount'    => $iCnt
            ]);
        \Phpfox_Pager::instance()->set([
            'page'  => $iPage,
            'size'  => $iPageSize,
            'count' => $iCnt,
        ]);
    }
}