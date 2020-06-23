<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 5:51 PM
 */
?>

<div class="ynstore-store-logo-info">
    <div class="ynstore-store-logo-block">
        {if !$aItem.store_logo}
        <div class="ynstore-store-nologo">
            <img src="{param var='core.path'}module/ynsocialstore/static/image/store_default.png">
        </div>
        {else}
        <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">{img server_id=$aItem.store_server_id path='core.url_pic' file='ynsocialstore/'.$aItem.store_logo suffix='_480_square'}</a>
        {/if}
    </div>

    <div class="ynstore-title">
        <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">{$aItem.store_name|clean}</a>
    </div>

    <div class="ynstore-ratings-reviews-block">
        <div class="ynstore-rating yn-rating yn-rating-normal">
            {for $i = 0; $i < 5; $i++}
            {if $i < (int)$aItem.store_rating}
            <i class="ico ico-star" aria-hidden="true"></i>
            {elseif ((round($aItem.store_rating) - $aItem.store_rating) > 0) && ($aItem.store_rating - $i) > 0}
            <i class="ico ico-star-half-o" aria-hidden="true"></i>
            {else}
            <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
            {/if}
            {/for}
        </div>
    </div>

    {if isset($aItem.location.address)}
    <div class="ynstore-location">
        {$aItem.location.address}
    </div>
    {/if}

    <div class="ynstore-phone">
        {_p var='ynsocialstore.phone'}: {$aItem.phone}
    </div>

    {if isset($aItem.fax)}
    <div class="ynstore-fax">
        {_p var='ynsocialstore.fax'}: {$aItem.fax}
    </div>
    {/if}

    <div class="ynstore-email">
        {_p var='ynsocialstore.email'}: <a href="mailto:{$aItem.email}">{$aItem.email}</a>
    </div>

    <div class="ynstore-store-actions">
        <div class="ynstore-store-actions-link">
            <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}products">
                <span class="ico ico-cubes-o"></span>
                {$aItem.total_products} {_p var='ynsocialstore.products'}
            </a>

            {if !empty($aItem.location.latitude) && !empty($aItem.location.longitude)}
            <a class="" href="https://maps.google.com/maps?daddr={$aItem.location.latitude},{$aItem.location.longitude}" target="_blank">
                <i class="ico ico-compass"></i>
                {_p var='ynsocialstore.get_directions'}
            </a>
            {/if}
        </div>

        {if Phpfox::getUserId() != $aItem.user_id}
        <a class="btn ynstore-btn-fw" href="javascript:void(0)" onclick="$Core.composeMessage({l}user_id:{$aItem.user_id}{r}); return false;">
            <i class="ico ico-envelope-o"></i>
            {_p var='ynsocialstore.message_seller'}
        </a>
        {/if}
    </div>
</div>
