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

class LinkTrackingController extends \Phpfox_Component
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
        $aVals = [];
        $iPageSize = 10;
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));
        $sFromDate = $this->request()->get('js_start_time__datepicker');
        $sToDate = $this->request()->get('js_end_time__datepicker');
        if($sFromDate)
        {
            $iFromTime = strtotime($sFromDate);
            $aConds[] = "AND al.last_click >= {$iFromTime}";
        }
        if($sToDate)
        {
            $iToTime = strtotime($sToDate)+23*60*60+59*60+59;
            $aConds[] = "AND al.last_click <= {$iToTime}";
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
        $this->template()->setTitle(_p('link_tracking'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('affiliate'))
            ->setBreadCrumb(_p('link_tracking'),$this->url()->makeUrl('affiliate.link-tracking'));
        list($iCount,$aTrackings) = Phpfox::getService('yncaffiliate.link')->getLinkTracking(Phpfox::getUserId(),$iPage,$iPageSize,$aConds);
        $this->template()->assign([
            'aTrackings' => $aTrackings,
            'sFromDate' => $sFromDate,
            'sToDate' => $sToDate,
            'aForms' => $aVals,
            'iCount' => $iCount,
            'iPage' => $iPage
        ]);
        \Phpfox_Pager::instance()->set([
            'page'  => $iPage,
            'size'  => $iPageSize,
            'count' => $iCount,
        ]);
    }
}