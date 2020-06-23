<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 20/01/2017
 * Time: 14:22
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{if !count($aItems)}
    {if !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {_p var='ynblog_no_bloggers_found'}
        </div>
    {/if}
{else}
    {if !PHPFOX_IS_AJAX}
        <div class="block p-block">
            <div class="content">
                <div class="p-listing-container p-advblog-blogger-container col-4" data-mode-view="grid">
    {/if}
        {foreach from=$aItems item=aItem}
            {template file='ynblog.block.entry_blogger'}
        {/foreach}
        {pager}

    {if !PHPFOX_IS_AJAX}
                </div>
            </div>
        </div>
    {/if}
{/if}
