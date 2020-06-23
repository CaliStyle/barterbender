{if !empty($aViewModes) && count($aViewModes) > 1}
    <div class="p-mode-view-container" data-mode-view-default="{$sModeViewDefault}" data-mode-view-id="{$sModeViewId}">
        {foreach from=$aViewModes item=aViewMode key=sMode name=viewmodes}
            <span class="p-mode-view-btn {$sMode}" data-mode="{$sMode}" {if $aViewMode.callback_js}data-callback-js="{$aViewMode.callback_js}"{/if} {if $aViewMode.callback_data}data-callback-data="{$aViewMode.callback_data}"{/if} title="{_p var=$aViewMode.title}">
                <i class="ico ico-{$aViewMode.icon}"></i>
            </span>
        {/foreach}
    </div>
{/if}