<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ynfeed-share-list">
    <ul class="dropdown-menu ynfeed-dropdown-share-list">
        <li>
            <a href="javascript:void(0);" class="ynfeed-share-share"><span class="ico ico-share-o"></span>{_p var='share'}...</a>
        </li>
        <li role="separator" class="divider"></li>
        {foreach from=$aShareServices key=sProvider item=aService name=fkey}
            <li{if $phpfox.iteration.fkey > 3} style="display: none;"{/if}>
                <a href="javascript:void(0);" class="ynfeed-share" data-surl="{$aFeed.feed_link}" data-provider="{$sProvider}" data-title="{$aFeed.feed_status|striptag}">
                    {if $sProvider == 'tumblr'}
                        <span class="ico">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 24 24"  xml:space="preserve">
								<path class="st0" d="M15.2,16.8v-1.6c-0.5,0.4-1,0.5-1.6,0.5c-0.2,0-0.5-0.1-0.8-0.2c-0.2-0.1-0.3-0.3-0.3-0.4
									c-0.1-0.2-0.1-0.6-0.1-1.2v-2.6h2.4V9.6h-2.4V7H11c-0.1,0.6-0.2,1.1-0.4,1.3C10.4,8.7,10.2,9,9.9,9.2c-0.3,0.3-0.7,0.5-1,0.6v1.5H10
									v3.6c0,0.4,0.1,0.8,0.2,1.1c0.1,0.2,0.3,0.5,0.5,0.7c0.2,0.2,0.5,0.4,0.9,0.5c0.5,0.1,0.9,0.2,1.2,0.2c0.4,0,0.8,0,1.2-0.1
									C14.4,17.1,14.8,17,15.2,16.8"/>
								<path class="st0" d="M12,2.5c5.2,0,9.5,4.3,9.5,9.5s-4.3,9.5-9.5,9.5S2.5,17.2,2.5,12S6.8,2.5,12,2.5 M12,0.5
									C5.7,0.5,0.5,5.7,0.5,12c0,6.3,5.2,11.5,11.5,11.5c6.3,0,11.5-5.2,11.5-11.5C23.5,5.7,18.3,0.5,12,0.5L12,0.5z"/>
								<path d="M12,3.1"/>
								</svg></span>{$aService.label}
                    {else}
                    <span class="ico {$aService.icon}"></span>{$aService.label}
                    {/if}
                </a>
            </li>
            {if $phpfox.iteration.fkey == 3}
                <li>
                    <a class="ynfeed-share-more">{_p var='more...'}</a>
                </li>
            {/if}
        {/foreach}
    </ul>
</div>