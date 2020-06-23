<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/28/17
 * Time: 16:19
 */
namespace Apps\YNC_Affiliate\Controller\Admin;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');
class ActionCommissionController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $iCommissionId = $this->request()->getInt('cid');
        $sStatus = $this->request()->get('status');
        $bIsMultiple = ($sStatus) == 'multi_denied' ? 1 : 0;
        if(!$iCommissionId || !in_array($sStatus,['denied','reject','multi_denied']))
        {
            $this->url()->send('admincp.yncaffiliate.manage-commissions',_p('invalid_params'));
            return false;
        }
        if($sStatus == 'denied')
        {
            $this->template()->setTitle(_p('deny_commission'))
                    ->setBreadCrumb(_p('deny_commission'));
        }
        elseif($sStatus == 'reject')
        {
            $this->template()->setTitle(_p('reject_commission'))
                        ->setBreadCrumb(_p('reject_commission'));
            $sStatus = 'denied';
        }
        if($aVals = $this->request()->getArray('val'))
        {
            if(Phpfox::getService('yncaffiliate.commission.process')->updateStatus($aVals['commission_id'],$aVals['status'],$aVals['reason']))
            {
                $this->url()->send('admincp.yncaffiliate.manage-commissions', _p('commission_updated_successfully'));
            }
        }
        $this->template()->assign([
                'sStatus' => $sStatus,
                'iCommissionId' => $iCommissionId,
                'bIsMultiple' => $bIsMultiple
        ]);
    }
}