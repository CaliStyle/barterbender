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
        <div id="js_block_border_apps_ynblog_controller_index" {if isset($sView)}class="ynadvblog-page-{$sView}"{/if}>
            <div class="ynadvblog-view-modes-block yn-viewmode-grid">
                <div class="block">
                    <div class="title"></div>
                    <div class="content">
                        <div class="yn-view-modes">
                            <span data-mode="big" class="yn-view-mode yn_casual"><i class="fa fa-align-left" aria-hidden="true"></i></span>
                            <span data-mode="list" class="yn-view-mode"><i class="fa fa-th-list" aria-hidden="true"></i></span>
                            <span data-mode="grid" class="yn-view-mode"><i class="fa fa-th" aria-hidden="true"></i></span>
                        </div>
                        <ul class="ynadvblog_items">
                            {/if}
                                {foreach from=$aItems name=ynblog item=aItem}
                                    <li class="ynadvblog_item {$phpfox.iteration.ynblog|ynblog_mode_view_blog_format}">
                                        {template file='ynblog.block.entry'}
                                     </li>
                                {/foreach}
                                {pager}

                                {if !PHPFOX_IS_AJAX && $bShowModerator}
                                    {moderation}
                                {/if}
                            {if !PHPFOX_IS_AJAX}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        {literal}
            <script type="text/javascript">
                $Behavior.initViewModeRecent = function(){
                    ynadvancedblog.initModeView('js_block_border_apps_ynblog_controller_index', 'grid');
                }
            </script>
        {/literal}
        {/if}
    {/if}
{/if}