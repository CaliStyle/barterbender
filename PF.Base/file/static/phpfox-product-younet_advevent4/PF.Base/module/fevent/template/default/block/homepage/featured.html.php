{if !empty($aFeatured)}
    <div id="fevent-feature" class="fevent-feature owl-carousel owl-theme dont-unbind-children">
        {foreach from=$aFeatured item=aEvent name=af}
            <div class="item">
                <div class="fevent-feature__item">
                    <div class="ynfevent-slider-photo">
                        <span class="ynfevent-slider-thumb" style="background-image: url('{$aEvent.image_path}');"></span>
                    </div>

                    <div class="fevent-feature__content">
                        <time class="fevent-feature__timer text-center">
                            <p class="fevent-feature__month mb-0 text-primary fz-12 text-uppercase">{$aEvent.M_start_time|shorten:3}</p>
                            <p class="fevent-feature__day mb-0 text-primary fz-28">{$aEvent.d_start_time}</p>
                            <p class="fevent-feature__clock mb-0">{$aEvent.short_start_time}</p>
                            {if $aEvent.isrepeat >= 0}<span class="ync-label-status solid gray text-uppercase">{_p var='fevent.repeat'}</span>{/if}
                        </time>
                        <div class="fevent-feature__info">
                            <a class="fevent-feature__title fw-bold" href="{permalink module='fevent' id=$aEvent.event_id title=$aEvent.title}">{$aEvent.title}</a>
                            <div class="fevent-feature__author fz-12 mt-h1">
                                <span class="fevent-feature__author__owner text-gray-dark">{_p var='fevent.by'} {$aEvent|user}</span><time class="pl-1">{_p var='fevent.end'}: {$aEvent.convert_end_time}</time>
                            </div>
                            <div class="fevent-feature__info__sub fz-12 mt-1 {if $aEvent.isrepeat >= 0}repeat{/if}">
                                <span>{$aEvent.location}{if $aEvent.address} {$aEvent.address}{/if}{if $aEvent.city} - {$aEvent.city}{/if}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
{/if}
