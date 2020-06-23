{if $isSlider}
    <div class="p-fevent-slider-container dont-unbind-children {if count($events) > 1}owl-carousel multi-item{else}single-item{/if}">
        {foreach from=$events name=event item=aItem}
            {template file='fevent.block.event-item-slider'}
        {/foreach}
    </div>
{else}
    {module name='ynccore.mode_view'}
    {if $dataSource == 'sponsored' && $isSideLocation}
        <div class="p-item-flag-block">
            <div class="sticky-label-icon sticky-sponsored-icon" title="{_p var='sponsored'}">
                <span class="ico ico-sponsor"></span>
                <span class="flag-style-arrow"></span>
            </div>
        </div>
    {/if}
    <div class="p-listing-container p-fevent-listing-container col-4 p-mode-view has-list-one-column {$additionalClass}"
         data-mode-view="{$sModeViewDefault}" data-block-id="{$id}" data-source="{$dataSource}">
        {foreach from=$events name=event item=aItem}
            {template file='fevent.block.event-item'}
        {/foreach}
    <div class="fevent-map" style="width: 100%; height:400px;"></div>
    </div>
{/if}
