<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/26/16
 * Time: 8:56 AM
 */
?>
{if isset($aItem.ship_payment_info)}
<div class="ynstore-ship_payment_info">
    <div class="ynstore-title">
        {_p var='ynsocialstore.shipping_and_payments'}
    </div>
    <span class="ynstore-content">
        {$aItem.ship_payment_info|clean|shorten:100|'...'}
        <a class="" href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}shipandpayment">+ {_p var='ynsocialstore.view_more'}</a>
    </span>
</div>
{/if}

{if isset($aItem.buyer_protection)}
<div class="ynstore-ship_payment_info">
    <div class="ynstore-title">
        {_p var='ynsocialstore.buyer_protection'}
    </div>
    <span class="ynstore-content">
        {$aItem.buyer_protection|clean|shorten:100|'...'}
        <a class="" href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}buyerprotection">+ {_p var='ynsocialstore.view_more'}</a>
    </span>
</div>
{/if}

{if isset($aItem.return_policy)}
<div class="ynstore-ship_payment_info">
    <div class="ynstore-title">
        {_p var='ynsocialstore.return_policy'}
    </div>
    <span class="ynstore-content">
        {$aItem.return_policy|clean|shorten:100|'...'}
        <a class="" href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}policy">+ {_p var='ynsocialstore.view_more'}</a>
    </span>
</div>
{/if}

