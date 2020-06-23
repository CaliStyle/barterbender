<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:40
 */

namespace Apps\YNC_Affiliate\Controller\Admin;
use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');

class ManageRequestController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $iPageSize = 10;
        $aVals = $aDate = array();
        $aConds = array();

        $bIsSearch = false;
        $aSearch = $this->request()->get('search');
        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));
        $iPage = $this->request()->getInt('page',1);
        $aSearch = $this->request()->get('search');
        if ($aSearch)
        {
            $aVals['affiliate_name'] = $aSearch['affiliate_name'];
            $aVals['status']         = $aSearch['status'];
            $aVals['fromdate']       = $aSearch['fromdate'];
            $aVals['todate']         = $aSearch['todate'];
            $bIsSearch = true;
        }
        else
        {
            $aVals = array(
                'affiliate_name' => '',
                'status'         => '',
                'fromdate'       => '',
                'todate'         => '',
            );
        }
        if ($aVals['affiliate_name'])
        {
            $aConds[] = "AND u.full_name like '%{$aVals['affiliate_name']}%'";
        }
        if ($aVals['status'] != '')
        {
            $aConds[] = "AND yr.request_status = '".$aVals['status']."'";
        }
        if($aVals['fromdate'])
        {
            $iFromTime = strtotime($aVals['fromdate']);
            $aConds[] = "AND yr.time_stamp >= {$iFromTime}";
        }
        if($aVals['todate'])
        {
            $iToTime = strtotime($aVals['todate'])+23*60*60+59*60+59;
            $aConds[] = "AND yr.time_stamp <= {$iToTime}";
        }
        list($iCnt,$aRequests) = Phpfox::getService('yncaffiliate.request')->getRequest($aConds,'yr.request_id DESC',$iPage,$iPageSize);

        if($iId = $this->request()->getInt('delete'))
        {
            $aRequest = Phpfox::getService('yncaffiliate.request')->get($iId);
            if(!$aRequest || $aRequest['request_status'] != 'waiting' || !Phpfox::isAdmin())
            {
                $this->url()->send('admincp.yncaffiliate.manage-request',_p('you_do_not_have_permission_to_cancel_this_request'));
            }
            else{
                if(Phpfox::getService('yncaffiliate.request.process')->delete($iId))
                {
                    $this->url()->send('admincp.yncaffiliate.manage-request',_p('request_has_been_deleted_successfully'));
                }
                else{
                    $this->url()->send('admincp.yncaffiliate.manage-request',_p('something_went_wrong_please_try_again'));
                }
            }
        }

        $this->template()->assign([
            'aForms' => $aVals,
            'aItems' => $aRequests,
            'bIsSearch'   => $bIsSearch,
        ]);
        Phpfox::getLib('pager')->set([
            'page'  => $this->request()->get('page', 1),
            'size'  => $iPageSize,
            'count' => $iCnt,
            'popup' => true,
        ]);
        $this->template()
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('admincp.app',['id' => 'YNC_Affiliate']))
            ->setBreadCrumb(_p('Manage Request'));
    }
}