<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/7/16
 * Time: 3:18 PM
 */
?>
<div class="ynstore-store-logo-info">
    <div class="ynstore-store-logo-block profile_image">
        {if !$aStoreLogo.logo_path}
            <div class="ynstore-store-nologo">
                <img src="{param var='core.path'}module/ynsocialstore/static/image/store_default.png">
            </div>
        {else}
            <a class="" href="{permalink module='ynsocialstore.store' id=$aStoreLogo.store_id title=$aStoreLogo.name}">{img server_id=$aStoreLogo.server_id path='core.url_pic' file='ynsocialstore/'.$aStoreLogo.logo_path suffix='_480_square'}</a>
        {/if}
        {if Phpfox::getUserId() == $aStoreLogo.user_id}
        <form class="" method="post" enctype="multipart/form-data" action="#">
            <input title="{_p var='ynsocialstore.change_logo'}" id="ynstore-store-logo-upload" type="file" accept="image/*" class="ajax_upload" value="Upload" name="image" data-url="{url link='ynsocialstore.store.photo' store_id=$aStoreLogo.store_id}">
            <label for="ynstore-store-logo-upload" href="{url link='user.photo'}"><i class="ico ico-file-photo"></i> <span>{_p var='ynsocialstore.change_logo'}</span></label>
        </form>
        {/if}
    </div>

    <div class="ynstore-title">
        {$aStoreLogo.name|clean}
    </div>

    {if !empty($aStoreLogo.address) && !empty($aStoreLogo.address.latitude) && !empty($aStoreLogo.address.longitude)}
    <div class="ynstore-location">
        {_p var='ynsocialstore.location'}:

        <a href="https://maps.google.com/maps?daddr={$aStoreLogo.address.latitude},{$aStoreLogo.address.longitude}" target="_blank">{$aStoreLogo.address.address}</a>
    </div>
    {/if}

    <div class="ynstore-ratings-reviews-block">
        <div class="ynstore-rating yn-rating yn-rating-normal">
            <span class="rating">{$aStoreLogo.rating}</span>
            {for $i = 0; $i < 5; $i++}
                {if $i < (int)$aStoreLogo.rating}
                    <i class="ico ico-star" aria-hidden="true"></i>
                {elseif ((round($aStoreLogo.rating) - $aStoreLogo.rating) > 0) && ($aStoreLogo.rating - $i) > 0}
                    <i class="ico ico-star-half-o" aria-hidden="true"></i>
                {else}
                    <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
                {/if}
            {/for}
        </div>

        <a href="{permalink module='ynsocialstore.store' id=$aStoreLogo.store_id title=$aStoreLogo.name}reviews" class="ynstore-review-count">
            {$aStoreLogo.total_review}&nbsp;{if $aStoreLogo.total_review == 1}{_p var='ynsocialstore.review'}{else}{_p var='ynsocialstore.reviews'}{/if}
        </a>
    </div>

    <div class="ynstore-description">
        {$aStoreLogo.short_description|clean}
    </div>

    {if Phpfox::getUserId() != $aStoreLogo.user_id}
        <a class="btn ynstore-btn-fw" href="javascript:void(0)" onclick="$Core.composeMessage({l}user_id:{$aStoreLogo.user_id}{r}); return false;">
            <i class="ico ico-envelope-o mr-1"></i>
            {_p var='ynsocialstore.message_seller'}
        </a>
    {/if}
    {if !empty($aStoreLogo.address) && !empty($aStoreLogo.address.latitude) && !empty($aStoreLogo.address.longitude)}
    <a class="btn ynstore-btn-fw" href="https://maps.google.com/maps?daddr={$aStoreLogo.address.latitude},{$aStoreLogo.address.longitude}" target="_blank">
        <i class="ico ico-compass mr-1"></i>
        {_p var='ynsocialstore.get_directions'}
    </a>
    {/if}
</div>
