<?php

defined('PHPFOX') or exit('NO DICE!');

class Jobposting_Component_Controller_Payment extends Phpfox_Component
{
    public function process()
    {
        $transaction_id = $this->request()->get('transactionid');
        $sUrl = $this->request()->get('sUrl');
        $aTransaction = Phpfox::getService('jobposting.transaction')->get($transaction_id);
        $sUrl = base64_decode($sUrl);

        if (count($aTransaction)) {

            $this->setParam('gateway_data', array(
                    'item_number' => 'jobposting|' . $aTransaction['transaction_id'],
                    'currency_code' => $aTransaction['currency'],
                    'amount' => $aTransaction['amount'],
                    'item_name' => 'jobposting|' . $aTransaction['transaction_id'],
                    'return' => Phpfox::getService('jobposting.helper')->getStaticPath() . 'module/jobposting/static/php/paymentcb.php?location=' . $sUrl,
                    'recurring' => '',
                    'recurring_cost' => '',
                    'alternative_cost' => '',
                    'alternative_recurring_cost' => ''
                )
            );
        }

        $this->template()->setTitle(_p('review_and_confirm_purchase'))
            ->setBreadcrumb(_p('jobposting'), $this->url()->makeUrl('jobposting'))
            ->setBreadcrumb(_p('review_and_confirm_purchase'), null, false)
            ;


    }
}