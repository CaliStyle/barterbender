<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 12/16/16
 * Time: 14:03
 */
?>
<div class="fevent-feature__item">
    <a href="{permalink module='fevent' id=$aEvent.event_id title=$aEvent.title}" class="ynfevent-slider-photo">
        <span class="ynfevent-slider-thumb" style="background-image: url('{$aEvent.image_path}');"></span>
    </a>
    {if (int)$aEvent.isrepeat >= 0}
    <span class="fevent__repeat"><i class="fa fa-refresh"></i></span>
    {/if}

    <div class="fevent-feature__content">
        <div class="fevent-feature__timer text-center">
            <p class="fevent-feature__month mb-0 text-primary fz-12 text-uppercase">{$aEvent.M_start_time|shorten:3}</p>
            <p class="fevent-feature__day mb-0 text-primary fz-28">{$aEvent.date_start_time}</p>
            <p class="fevent-feature__clock mb-0">{$aEvent.short_start_time}</p>
        </div>
        <div class="fevent-feature__info">
            <a class="fevent-feature__title" href="{permalink module='fevent' id=$aEvent.event_id title=$aEvent.title}">{$aEvent.title}</a>
            <div class="fevent-feature__info__sub text-gray-dark fz-12 mt-1">
                <time><i class="ico ico-sandclock-end-o"></i>{_p var='fevent.end'}: {$aEvent.date_end_time}</time><span class="ml-2"><i class="ico ico-checkin-o"></i>{$aEvent.location}{if $aEvent.address} {$aEvent.address}{/if}{if $aEvent.city} - {$aEvent.city}{/if}</span>
            </div>
        </div>  
    </div>
</div>
