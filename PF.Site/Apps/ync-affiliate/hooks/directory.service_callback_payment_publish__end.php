 <?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/2/17
 * Time: 17:38
 */

if(Phpfox::isModule('yncaffiliate') && $aInvoice)
{
    $aPackage = $aInvoice['invoice_data']['aPackage'];
    if(is_object($aPackage))
    {
        $iPublishFee = $aPackage->fee;
    }
    else{
        $iPublishFee = $aPackage['fee'];
    }
    $aPurchase = [
        'amount' => $iPublishFee,
        'currency_id' => $aInvoice['currency_id'],
    ];

    if($iPublishFee)
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aInvoice['user_id'],$aPurchase,'buy_business_package','directory');

}
?>