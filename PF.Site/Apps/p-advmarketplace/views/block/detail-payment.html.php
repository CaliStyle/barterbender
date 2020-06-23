<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="detail-activitypoint-payment">
    <div class="p-flex-wrapper mb-1">
        <span class="mr-1">{_p var='advancedmarketplace_your_current_activity_points'}: </span>
        <span class="p-ml-auto fw-bold">{$currentPoints}</span>
    </div>
    <div class="p-flex-wrapper mb-1">
        <span class="mr-1">{_p var='advancedmarketplace_payment_cost'}: </span>
        <span class="p-ml-auto fw-bold">{$pointPaid}</span>
    </div>
    <div class="p-flex-wrapper mb-2">
        <span class="mr-1">{_p var='advancedmarketplace_remaining_points'}: </span>
        <span class="p-ml-auto fw-bold">{$remainPoints}</span>
    </div>
    <div class="p-advmarketplace-action-wrapper-popup">
        <button class="btn btn-default btn-sm" onclick="js_box_remove(this); return false;">{_p var='cancel}</button>
        <button class="btn btn-primary btn-sm" onclick="appAdvMarketplace.processPurchasement(this);" data-no-confirm="1">{_p var='submit'}</button>
    </div>
</div>
