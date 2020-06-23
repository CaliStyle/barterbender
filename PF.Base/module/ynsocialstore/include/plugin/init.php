<?php
// THIS HOOK USE FOR CHECKING HOSTING SERVICE

if (!function_exists('ynsocialstore_format_price')) {
    function ynsocialstore_format_price($price, $currency_id) {
        return Phpfox::getService('core.currency')->getCurrency($price, $currency_id);
    }
}