{if count($aOngoing)>0}
    <div class="ync-mode-view-container fevent-block-ongoing-js">
        <span class="ync-mode-view-btn grid" data-mode="grid" title="{_p var='grid_view'}"><i class="ico ico-th"></i></span>
        <span class="ync-mode-view-btn list" data-mode="list" title="{_p var='list_view'}">
            <i class="ico ico-list icon-normal" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list.svg)"></i>
            <i class="ico ico-list icon-hover" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list_dark.svg)"></i>
        </span>
        <span class="ync-mode-view-btn map" data-mode="map" data-callback-js="fevent.showMapView('ongoing');" title="{_p var='map_view'}"><i class="ico ico-map"></i></span>
    </div>
    <div class="item-container ynfevent-content-item-list ync-listing-container col-2 full-border ync-view-modes-js" data-mode-view="" data-mode-view-default="grid">
    	{foreach from=$aOngoing  name=event item=aItem}
            <article class="ync-item">{template file='fevent.block.event-item'}</article>
    	{/foreach}
        <div id="fevent_ongoing_map" class="fevent-map" style="width: 100%; height:400px;"></div>
    </div>
    {if $iTotal>$iLimit}
        <div class="ync-viewmore">
            <a href="{url link='fevent' when='ongoing' view='all'}" class="item-viewmore">{_p var='core.view_more'}</a>
        </div>
    {/if}
{else}
    <p class="help-block">{_p var='fevent.no_events_found'}</p>
{/if}