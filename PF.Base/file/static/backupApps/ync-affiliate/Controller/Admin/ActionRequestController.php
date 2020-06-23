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
class ActionRequestController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $iRequestId = $this->request()->getInt('rid');
        $sStatus = $this->request()->get('status');
        if(!$iRequestId || !in_array($sStatus,['denied']))
        {
            $this->url()->send('admincp.yncaffiliate.manage-request',_p('invalid_params'));
            return false;
        }
        if($sStatus == 'denied')
        {
            $this->template()->setTitle(_p('deny_request'))
                    ->setBreadCrumb(_p('deny_request'));
        }
        if($aVals = $this->request()->getArray('val'))
        {
            if(Phpfox::getService('yncaffiliate.request.process')->updateStatus($aVals['request_id'],$aVals['status'],$aVals['response']))
            {
                $this->url()->send('admincp.yncaffiliate.manage-request', _p('request_has_been_denied_successfully'));
            }
        }
        $this->template()->assign([
                'sStatus' => $sStatus,
                'iRequestId' => $iRequestId,
        ]);
    }
}