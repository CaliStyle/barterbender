{if !empty($aItems)}
    {if $sDataSource == 'featured' && $bIsSideLocation}
        <div class="p-item-flag-block">
            <div class="sticky-label-icon sticky-featured-icon" title="Featured">
                <span class="ico ico-diamond"></span>
                <span class="flag-style-arrow"></span>
            </div>
        </div>
    {/if}
    {module name='ynccore.mode_view'}
    <div class="p-listing-container {if !($sDataSource == 'featured')}col-4{/if} {$sCustomContainerClassName}" {if !($sDataSource == 'featured')} data-mode-view="{$sModeViewDefault}"{/if}>
        {foreach from=$aItems name=video item=aPitem}
            {template file='ultimatevideo.block.entry_playlist'}
        {/foreach}
    </div>
{/if}