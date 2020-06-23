<?php
namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;

class DetailPayment extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        if(!Phpfox::isAppActive('Core_Activity_Points')) {
            return false;
        }

        $invoiceId = $this->getParam('invoice_id');
        if(!($invoice = Phpfox::getService('advancedmarketplace')->getInvoice($invoiceId))) {
            return \Phpfox_Error::display(_p('advancedmarketplace_invalid_invoice'));
        }
        $totalPaid = $invoice['price'];
        $currencyId = $invoice['currency_id'];
        $pointConversions = Phpfox::getParam('activitypoint.activity_points_conversion_rate');
        if(empty($pointConversions[$currencyId]) || !is_numeric($pointConversions[$currencyId])) {
            return false;
        }

        $pointPaid = ceil($pointConversions[$currencyId] != 0 ? ($totalPaid / $pointConversions[$currencyId]) : 0);
        $currentPoints = Phpfox::getService('advancedmarketplace')->getUserCurrentPoints();
        $remainPoints = $currentPoints - $pointPaid;
        $this->template()->assign([
            'pointPaid' => $pointPaid,
            'currentPoints' => $currentPoints,
            'remainPoints' => $remainPoints
        ]);
    }
}