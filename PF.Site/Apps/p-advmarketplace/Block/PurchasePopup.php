<?php
namespace Apps\P_AdvMarketplace\Block;

use Phpfox;
use Phpfox_Component;

class PurchasePopup extends Phpfox_Component
{
    public function process()
    {
        $listingId = (int)$this->getParam('listing_id');
        $invoiceId = (int)$this->getParam('invoice_id');

        if(!($aListing = Phpfox::getService('advancedmarketplace')->getListing($listingId))) {
            return \Phpfox_Error::set(_p('advancedmarketplace_listing_not_found'));
        }

        if($aListing['user_id'] == Phpfox::getUserId()) {
            return \Phpfox_Error::set(_p('advancedmarketplace_you_can_not_purchase_yourself'));
        }

        if($aListing['view_id'] == 2 || $aListing['is_expired']) {
            $this->template()->assign([
                'warning' =>  true,
            ]);
            return \Phpfox_Error::display(_p('advancedmarketplace_this_listing_is_not_available_at_this_moment_please_contact_seller_for_more_information'));
        }
        else {
            if(empty($invoiceId)) {
                if($aListing['is_sell']) {
                    $invoiceId = Phpfox::getService('advancedmarketplace.process')->addInvoice($listingId, $aListing['currency_id'], $aListing['price']);
                }
            }

            $invoice = Phpfox::getService('advancedmarketplace')->getInvoice($invoiceId);

            if(empty($invoiceId) || empty($invoice)) {
                return \Phpfox_Error::set(_p('advancedmarketplace_you_can_not_buy_this_listing'));
            }

            $paymentData = [
                'item_number' => 'advancedmarketplace|' . $invoiceId,
                'currency_code' => $invoice['currency_id'],
                'amount' => $invoice['price'],
                'item_name' => _p($aListing['title']),
                'return' => $this->url()->makeUrl('advancedmarketplace.invoice', array('payment' => 'done')),
                'recurring' => '',
                'recurring_cost' => '',
                'alternative_cost' => '',
                'alternative_recurring_cost' => '',
                'no_purchase_with_points' => true
            ];

            $userGateways = Phpfox::getService('api.gateway')->getUserGateways($aListing['user_id']);
            foreach($userGateways as $gatewayName => $userGateway) {
                if(!empty($userGateway['gateway'])) {
                    foreach ($userGateway['gateway'] as $sKey => $mValue) {
                        $paymentData['setting'][$sKey] = $mValue;
                    }
                }
                else {
                    $paymentData['fail_' . $gatewayName] = true;
                }
            }
            $paymentGateways = Phpfox::getService('api.gateway')->get($paymentData);

            $availablePaymentGateways = [];
            $allowedPaymentMethods = !empty($aListing['payment_methods']) ? unserialize($aListing['payment_methods']) : null;
            foreach($paymentGateways as $key => $paymentGateway) {
                if(in_array($paymentGateway['gateway_id'],$allowedPaymentMethods) || empty($allowedPaymentMethods)) {
                    $availablePaymentGateways[] = [
                        'gateway_id' => $paymentGateway['gateway_id'],
                        'title' => $paymentGateway['title']
                    ];
                }
                else {
                    unset($paymentGateways[$key]);
                }
            }

            $allowActivityPointMethod = in_array('activitypoints', $allowedPaymentMethods);
            if($allowActivityPointMethod) {
                $gateway = Phpfox::getService('advancedmarketplace.helper')->buildActivityPointPaymentMethod($paymentData);
                if(!empty($gateway)) {
                    $availablePaymentGateways[] = $paymentGateways[] = Phpfox::getService('advancedmarketplace.helper')->buildActivityPointPaymentMethod($paymentData);
                }
            }

            if(!empty($availablePaymentGateways)) {
                $availablePaymentGateways[0]['is_default'] = 1;
            }

            $enoughActivityPoint = true;
            $pointConversions = Phpfox::getParam('activitypoint.activity_points_conversion_rate');
            if(!in_array('activitypoints',array_column($availablePaymentGateways,'gateway_id')) &&
                $allowActivityPointMethod &&
                Phpfox::isAppActive('Core_Activity_Points') &&
                Phpfox::getUserParam('activitypoint.can_purchase_with_activity_points') &&
                isset($pointConversions[$invoice['currency_id']]) && is_numeric($pointConversions[$invoice['currency_id']])) {
                $enoughActivityPoint = false;
                $availablePaymentGateways[] = [
                    'gateway_id' => 'activitypoints',
                    'title' => _p('activity_points'),
                    'disabled' => 1
                ];
            }

            $acceptOnlyPaypal = false;
            if(count($availablePaymentGateways) == 1 && $availablePaymentGateways[0]['gateway_id'] == 'paypal') {
                $acceptOnlyPaypal = true;
                $availablePaymentGateways[0]['hide'] = 1;
            }

            if($invoice['price'] == '0.00') {
                $listingPrice = _p('free');
            }
            else {
                $listingPrice = Phpfox::getService('core.currency')->getCurrency($invoice['price'], $invoice['currency_id']);
            }

            $bIsThickBox = false;
            $this->template()->assign([
                'aGateways' => $paymentGateways,
                'aGatewayData' => $paymentData,
                'bIsThickBox' => $bIsThickBox,
                'aListing' => $aListing,
                'availablePaymentGateways' => $availablePaymentGateways,
                'listingPrice' => $listingPrice,
                'acceptOnlyPaypal' => $acceptOnlyPaypal,
                'enoughActivityPoint' => $enoughActivityPoint,
                'invoiceId' => $invoiceId
            ]);
        }

        return 'block';
    }
}