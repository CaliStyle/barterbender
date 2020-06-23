<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Payment extends Phpfox_Component {
    public function process(){

        $transaction_id = $this->request()->get('transactionid');

        list($iCnt,$aTransaction) = Phpfox::getService('contest.transaction')->searchTransactions( array('transaction.transaction_id ='.(int)$transaction_id));

        $sUrl = urlencode(Phpfox::getLib('url')->permalink('contest', $aTransaction[0]['contest_id'], $aTransaction[0]['contest_name']));

        if($iCnt){

        $this->setParam('gateway_data', array(
                        'item_number' => 'contest|' . $aTransaction[0]['transaction_id'],
                        'currency_code' => $aTransaction[0]['currency'],
                        'amount' => $aTransaction[0]['amount'],
                        'item_name' =>'contest|' . $aTransaction[0]['transaction_id'],
                        'return' => Phpfox::getService('contest.helper')->getStaticPath() . 'module/contest/static/thankyou.php?sLocation=' . $sUrl,
                        'recurring' => '',
                        'recurring_cost' => '',
                        'alternative_cost' => '',
                        'alternative_recurring_cost' => ''                  
                    )
                ); 
        }
        

         $this->template()->setTitle(_p('contest.review_and_confirm_purchase'))
                ->setBreadcrumb(_p('contest.contest'), $this->url()->makeUrl('contest'))
                ->setBreadcrumb(_p('contest.review_and_confirm_purchase'), null, false);
        Phpfox::getService('contest.helper')->buildMenu();
        

    }
}