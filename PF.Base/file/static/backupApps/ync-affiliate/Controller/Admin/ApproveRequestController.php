<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/7/17
 * Time: 17:40
 */
namespace Apps\YNC_Affiliate\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
class ApproveRequestController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        Phpfox::isAdmin(true);
        $iRequestId = $this->request()->get('rid');
        $aRequest = Phpfox::getService('yncaffiliate.request')->get($iRequestId);
        $bIsPayment = false;
        if(!$aRequest)
        {
            return $this->url()->send('admincp.yncaffiliate.manage-request',_p('something_went_wrong_please_try_again'));
        }
        if ($this->request()->get('process')) {
            $bIsPayment = true;
            $aVal = $this->request()->getArray('val');
            $message = $aVal['response'];
            Phpfox::getService('yncaffiliate.request.process')->updateRequest($iRequestId, $message);
            $sUrl = $sUrl = $this->url()->makeUrl('admincp.yncaffiliate.manage-request', array('payment' => 'done'));
            $aUserGateways = Phpfox::getService('api.gateway')->getUserGateways($aRequest['user_id']);
            $aActiveGateways = Phpfox::getService('api.gateway')->getActive(); // http://www.phpfox.com/tracker/view/15060/
            $aPurchaseDetails = array(
                'item_number' => 'yncaffiliate|request_' . $iRequestId,
                'currency_code' => $aRequest['request_currency'],
                'amount' => $aRequest['request_amount'],
                'item_name' => 'Money Request',
                'return' => Phpfox::getParam('core.path_actual') . 'Apps/ync-affiliate/thankyou.php?sLocation=' . $sUrl,
                'recurring' => '',
                'recurring_cost' => '',
                'alternative_cost' => '',
                'alternative_recurring_cost' => ''
            );
            $bHaveMethod = !empty($aRequest['request_method']) ? true : false;
            if($aRequest['request_method'] == 'activitypoints')
            {
                foreach ($aActiveGateways as $aActiveGateway) {
                    $aPurchaseDetails['fail_' . $aActiveGateway['gateway_id']] = true;
                }
            }
            elseif (is_array($aUserGateways) && count($aUserGateways)) {
                if($bHaveMethod){
                    $aPurchaseDetails['no_purchase_with_points'];
                }
                foreach ($aUserGateways as $sGateway => $aData) {
                    if($sGateway != $aRequest['request_method'] && $bHaveMethod)
                    {
                        $aPurchaseDetails['fail_' . $sGateway] = true;
                        continue;
                    }
                    if (is_array($aData['gateway'])) {
                        foreach ($aData['gateway'] as $sKey => $mValue) {
                            $aPurchaseDetails['setting'][$sKey] = $mValue;
                        }
                    } else {
                        $aPurchaseDetails['fail_' . $sGateway] = true;
                    }

                    if (empty($aActiveGateways)) {
                        continue;
                    }
                    $bActive = false;
                    foreach ($aActiveGateways as $aActiveGateway) {
                        if ($sGateway == $aActiveGateway['gateway_id']) {
                            $bActive = true;
                        }
                    }
                    if (!$bActive) {
                        $aPurchaseDetails['fail_' . $aActiveGateway['gateway_id']] = true;
                    }
                }
            }
            $this->setParam('gateway_data', $aPurchaseDetails);
            $this->template()->setTitle(_p('review_and_confirm_payment'))
                ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('Affiliate'), $this->url()->makeUrl('admincp.app').'?id=YNC_Affiliate')
                ->setBreadcrumb(_p('review_and_confirm_payment'), null, true)
                ->assign(array(
                        'confirm' => false,
                        'bIsPayment' => $bIsPayment
                    )
                );
            return;
        }
        $this->template()->setTitle(_p('approve_request'))
            ->setBreadCrumb(_p('approve_request'));
        $this->template()->assign([
            'iRequestId' => $iRequestId,
            'bIsPayment' => $bIsPayment,
            'aRequest' => $aRequest,
            'aUser' => Phpfox::getService('user')->get($aRequest['user_id'])
        ]);
    }
}