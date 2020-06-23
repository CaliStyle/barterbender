<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:34
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AffiliateClientController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        $this->template()
            ->setBreadCrumb(_p('affiliate_s_client'));
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
        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        if ($aSearch)
        {
            $aVals['affiliate_name'] = $aSearch['affiliate_name'];
            $aVals['fromdate']       = $aSearch['fromdate'];
            $aVals['todate']         = $aSearch['todate'];
            $bIsSearch = true;
        }
        else
        {
            $aVals = array(
                'affiliate_name' => '',
                'fromdate'       => '',
                'todate'         => '',
            );
        }
        $aSearch['from_day'] = $aSearch['to_day'] = Phpfox::getTime('j');
        $aSearch['from_month'] = $aSearch['to_month'] = Phpfox::getTime('n');
        $aSearch['from_year']  = $aSearch['to_year'] = Phpfox::getTime('Y');
        if ($aVals['affiliate_name'])
        {
            $aConds[] = "AND u1.full_name like '%{$aVals['affiliate_name']}%'";
        }

        if($aVals['fromdate'])
        {
            $iFromTime = strtotime($aVals['fromdate']);
            $aConds[] = "AND ass.time_stamp >= {$iFromTime}";
        }
        if($aVals['todate'])
        {
            $iToTime = strtotime($aVals['todate'])+23*60*60+59*60+59;
            $aConds[] = "AND ass.time_stamp <= {$iToTime}";
        }
        list($iCount, $aList) = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getAffiliateClient($aConds, $this->request()->get('page', 1), $iPageSize);
//        die(d($aList));
        Phpfox_Pager::instance()->set([
            'page'  => $this->request()->get('page', 1),
            'size'  => $iPageSize,
            'count' => $iCount,
            'popup' => true,
        ]);
        $aVals['from_day'] = Phpfox::getTime('j');
        $aVals['from_month'] = Phpfox::getTime('n');
        $aVals['from_year'] = Phpfox::getTime('Y');
        $aVals['to_day'] = Phpfox::getTime('j');
        $aVals['to_month'] = Phpfox::getTime('n');
        $aVals['to_year'] = Phpfox::getTime('Y');
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-affiliate';
        $this->template()->assign(array(
            'aItems'      => $aList,
            'aForms'      => $aVals,
            'corePath'    => $corePath,
            'bIsSearch'   => $bIsSearch,
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncaffiliate.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}