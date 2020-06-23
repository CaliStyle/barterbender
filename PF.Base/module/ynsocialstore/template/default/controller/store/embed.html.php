<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/14/16
 * Time: 18:55
 */
?>
{foreach from=$aFiles item=file}
{if !empty($file)}
<script type="text/javascript" src="{$file}"></script>
{/if}
{/foreach}
<link rel="stylesheet" type="text/css" href="{$sCorePath}module/ynsocialstore/static/css/default/default/embed.css" />

<div class="ynstore-store-logo-info">
    <div class="ynstore-store-logo-block">
        <a href="javascript:void(0)">
    		{if $aStore.logo_path}
                <img src="{img server_id=$aStore.server_id path='core.url_pic' file='ynsocialstore/'.$aStore.logo_path suffix='_480_square' return_url='true'}" alt="">
    	    {else}
    	    	<img src="{$sCorePath}module/ynsocialstore/static/image/store_default.png}" alt="">
    	    {/if}
        </a>
    </div>

    <div class="ynstore-title">
        {$aStore.name|clean}
    </div>

    {if !empty($aStore.address.0.latitude) && !empty($aStore.address.0.longitude)}
    <div class="ynstore-location">
        {_p var='ynsocialstore.location'}:
        <a href="//maps.google.com/maps?daddr={$aStore.address.0.latitude},{$aStore.address.0.longitude}" target="_blank">{$aStore.address.0.address}</a>
    </div>
    {/if}

    <div class="ynstore-ratings-reviews-block">
        <div class="ynstore-rating yn-rating yn-rating-normal">
            {for $i = 0; $i < 5; $i++}
                {if $i < (int)$aStore.rating}
                    <i class="ico ico-star" aria-hidden="true"></i>
                {elseif ((round($aStore.rating) - $aStore.rating) > 0) && ($aStore.rating - $i) > 0}
                    <i class="ico ico-star-half-o" aria-hidden="true"></i>
                {else}
                    <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
                {/if}
            {/for}
        </div>
        <span class="ynstore-review-count">
            {$aStore.total_review} {_p var='ynsocialstore.reviews'}
        </span>
    </div>

    <div class="ynstore-description">
        {$aStore.short_description|clean|shorten:150:'ynsocialstore.view_more':true}
    </div>

    {if !empty($aStore.address.0.latitude) && !empty($aStore.address.0.longitude)}
    <a class="btn ynstore-btn-fw"  href="//maps.google.com/maps?daddr={$aStore.address.0.latitude},{$aStore.address.0.longitude}" target="_blank">
        <i class="ico ico-compass mr-1"></i>
        {_p var='ynsocialstore.get_directions'}</a>
    {/if}
    <a class="btn ynstore-btn-fw" href="{permalink module='ynsocialstore.store' id=$aStore.store_id title=$aStore.name}aboutus">{_p var='ynsocialstore.view_more_abouts_us'}</a>
</div>
