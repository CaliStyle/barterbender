<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if !PHPFOX_IS_AJAX}
{template file='ynblog.block.adv_search'}
{/if}
{if !$bIsInHomePage}
    {if !count($aItems)}
        {if !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {_p var='no_blogs_found'}
        </div>
        {/if}
    {else}
        {if !PHPFOX_IS_AJAX}
        <div id="js_block_border_apps_ynblog_controller_index" class="{if isset($sView)}ynadvblog-page-{$sView}{/if} p-profile-listing-container-theme">
            <div class="block p-block">
                <div class="content">
                    {module name='ynccore.mode_view'}
                    <div class="p-listing-container p-advblog-listing-container col-4 casual-col-3 p-mode-view" data-mode-view="{$sModeViewDefault}">
                        {/if}
                        {foreach from=$aItems name=ynblog item=aItem}
                        {template file='ynblog.block.entry'}
                        {/foreach}
                        {pager}

                        {if !PHPFOX_IS_AJAX && $bShowModerator}
                        {moderation}
                        {/if}
                        {if !PHPFOX_IS_AJAX}
                    </div>
                </div>
            </div>
        </div>
        {/if}
    {/if}
{/if}

{literal}
<script type="text/javascript">
    $Behavior.ynblog_index = function () {
        if ($('[data-cmd="core.search_items"]').length) {
            $('[data-cmd="core.search_items"]').attr('title', oTranslations['search']);
        }
    }
</script>
{/literal}