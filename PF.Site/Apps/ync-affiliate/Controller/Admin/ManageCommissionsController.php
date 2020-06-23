<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:38
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ManageCommissionsController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $iPageSize = 10;
        $aVals = $aDate = array();
        $aConds = array();

        $bIsSearch = false;
        $aSearch = $this->request()->get('search');
        $aValsDate = $this->request()->get('val');
        if (isset($aValsDate)) {
            if(isset($aValsDate['from_month'])) {
                $aSearch['fromdate'] = $aValsDate['from_month'] . '/' . $aValsDate['from_day'] . '/' . $aValsDate['from_year'];
            }
            if(isset($aValsDate['to_month'])) {
                $aSearch['todate'] = $aValsDate['to_month'] . '/' . $aValsDate['to_day'] . '/' . $aValsDate['to_year'];
            }
        }
        $iPage = $this->request()->getInt('page',1);
        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        if ($aSearch)
        {
            $aVals['affiliate_name'] = $aSearch['affiliate_name'];
            $aVals['client_name'] = $aSearch['client_name'];
            $aVals['status']         = $aSearch['status'];
            $aVals['fromdate']       = $aSearch['fromdate'];
            $aVals['todate']         = $aSearch['todate'];
            $aVals['payment_type']   = $aSearch['payment_type'];
            $bIsSearch = true;
        }
        else
        {
            $aVals = array(
                'affiliate_name' => '',
                'client_name'    => '',
                'payment_type'   => '',
                'status'         => '',
                'fromdate'       => '',
                'todate'         => '',
            );
        }

        if ($aVals['affiliate_name'])
        {
            $aConds[] = "AND u1.full_name like '%{$aVals['affiliate_name']}%'";
        }
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
        if($aVals['fromdate'])
        {
            $iFromTime = strtotime($aVals['fromdate']);
            $aConds[] = "AND yc.time_stamp >= {$iFromTime}";
        }
        if($aVals['todate'])
        {
            $iToTime = strtotime($aVals['todate'])+23*60*60+59*60+59;
            $aConds[] = "AND yc.time_stamp <= {$iToTime}";
        }
        $aRules = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getActiveRules();
        list($iCount, $aList) = Phpfox::getService('yncaffiliate.commission')->getManageCommissions($aConds,$iPage,$iPageSize);
        if ($id = $this->request()->getInt('cid')) {
            $status = $this->request()->get('status');
            if (Phpfox::getService('yncaffiliate.commission.process')->updateStatus($id, $status)) {
                $this->url()->send('admincp.yncaffiliate.manage-commissions', _p('commission_updated_successfully'));
            }
        }
        if ($aIds = $this->request()->getArray('ycid')) {
            $sIds = implode(',',$aIds);
            $aVal = $this->request()->getArray('val');
            if(isset($aVal['approve_selected']))
            {
                if (Phpfox::getService('yncaffiliate.commission.process')->updateStatusMulti($sIds,['waiting','delaying'],'approved')) {
                    $this->url()->send('admincp.yncaffiliate.manage-commissions', _p('commission_updated_successfully'));
                }
            }
            elseif(isset($aVal['is_multi']) && $aVal['is_multi'] == 1)
            {
                $sReason = $aVal['reason'];
                if (Phpfox::getService('yncaffiliate.commission.process')->updateStatusMulti($sIds,['waiting','delaying'],'denied',$sReason)) {
                    $this->url()->send('admincp.yncaffiliate.manage-commissions', _p('commission_updated_successfully'));
                }
            }
        }
        $aVals['from_day'] = Phpfox::getTime('j');
        $aVals['from_month'] = Phpfox::getTime('n');
        $aVals['from_year'] = Phpfox::getTime('Y');
        $aVals['to_day'] = Phpfox::getTime('j');
        $aVals['to_month'] = Phpfox::getTime('n');
        $aVals['to_year'] = Phpfox::getTime('Y');
        $this->template()->assign([
            'aForms' => $aVals,
            'aItems' => $aList,
            'aRules' => $aRules,
            'bIsSearch'   => $bIsSearch,
        ]);
        Phpfox::getLib('pager')->set([
            'page'  => $this->request()->get('page', 1),
            'size'  => $iPageSize,
            'count' => $iCount,
            'popup' => true,
        ]);
        $this->template()
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('admincp.app',['id' => 'YNC_Affiliate']))
            ->setBreadCrumb(_p('Manage Commissions'));
    }
}