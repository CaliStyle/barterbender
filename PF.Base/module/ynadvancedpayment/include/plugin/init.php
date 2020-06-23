<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(Phpfox::isModule('ynadvancedpayment'))
{
    include __DIR__ .'/' .  "library/phpfox/gateway/api/ccbill.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/gopay.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/authorizenet.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/itransact.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/skrill.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/webmoney.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/stripe.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/bitpay.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/heidelpay.class.php";
    include __DIR__ .'/' .  "library/phpfox/gateway/api/braintree.class.php";


    Phpfox::configs()->merge(['services'=>[
        'gateway.api.ccbill' =>  \Phpfox_Gateway_Api_CCBill::class,
        'gateway.api.authorizenet' =>  \Phpfox_Gateway_Api_Authorizenet::class,
        'gateway.api.gopay' =>  \Phpfox_Gateway_Api_GoPay::class,
        'gateway.api.itransact' =>  \Phpfox_Gateway_Api_ITransact::class,
        'gateway.api.skrill' =>  \Phpfox_Gateway_Api_Skrill::class,
        'gateway.api.webmoney' =>  \Phpfox_Gateway_Api_WebMoney::class,
        'gateway.api.stripe' =>  \Phpfox_Gateway_Api_Stripe::class,
        'gateway.api.bitpay' =>  \Phpfox_Gateway_Api_BitPay::class,
        'gateway.api.heidelpay' =>  \Phpfox_Gateway_Api_HeidelPay::class,
        'gateway.api.braintree' =>  \Phpfox_Gateway_Api_Braintree::class,
    ]]);

}