 <?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/2/17
 * Time: 17:38
 */

if(Phpfox::isModule('yncaffiliate') && $aPackage && $aInvoice)
{
    $aPurchase = [
        'amount' => $aPackage['fee'],
        'currency_id' => $aInvoice['currency_id'],
    ];
    if($aPackage['fee'] > 0)
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aInvoice['user_id'],$aPurchase,'buy_store_package','ynsocialstore');

}
?>