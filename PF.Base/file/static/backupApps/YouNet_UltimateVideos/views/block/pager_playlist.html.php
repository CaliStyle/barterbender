{if isset($aPager) && $aPager.totalPages > 1}
    <div class="ultimatevideo_pagination pager_outer clearfix">
        <ul class="pagination pull-right">
            {if isset($aPager.firstUrl)}
                <li class="first"><a {if $sAjax}href="#"
                                     onclick="var mode = UltimateVideo.currentModeViewInPlaylistDetail();$.ajaxCall('{$sAjax}', 'page={$aPager.firstAjaxUrl}&playlist={$iPlaylistId}&mode='+mode); $Core.addUrlPager(this); return false;"
                                     {else}href="{$aPager.firstUrl}"{/if}><i class="fa fa-angle-double-left fa-lg"
                                                                             aria-hidden="true"></i></a></li>{/if}
            {if isset($aPager.prevUrl)}
                <li><a {if $sAjax}href="#"
                       onclick="var mode = UltimateVideo.currentModeViewInPlaylistDetail();$.ajaxCall('{$sAjax}', 'page={$aPager.prevAjaxUrl}&playlist={$iPlaylistId}&mode='+mode); $Core.addUrlPager(this); return false;"
                       {else}href="{$aPager.prevUrl}"{/if}><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                </li>{/if}
            {foreach from=$aPager.urls key=sLink name=pager item=sPage}
                <li {if !isset($aPager.firstUrl) && $phpfox.iteration.pager == 1} class="first"{/if}><a
                            {if $sAjax}href="#"
                            onclick="{if $sLink}var mode = UltimateVideo.currentModeViewInPlaylistDetail();$.ajaxCall('{$sAjax}', 'page={$sPage}&playlist={$iPlaylistId}&mode='+mode); $Core.addUrlPager(this);{/if} return false;{else}href="
                            {if $sLink}{$sLink}{else}javascript:void(0);{/if}{/if}
                    "{if $aPager.current == $sPage} class="active"{/if}>{$sPage}</a></li>
            {/foreach}
            {if isset($aPager.nextUrl)}
                <li><a {if $sAjax}href="#"
                       onclick="var mode = UltimateVideo.currentModeViewInPlaylistDetail();$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}&playlist={$iPlaylistId}&mode='+mode); $Core.addUrlPager(this); return false;"
                       {else}href="{$aPager.nextUrl}"{/if}><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                </li>{/if}
            {if isset($aPager.lastUrl)}
                <li><a {if $sAjax}href="#"
                       onclick="var mode = UltimateVideo.currentModeViewInPlaylistDetail();$.ajaxCall('{$sAjax}', 'page={$aPager.lastAjaxUrl}&playlist={$iPlaylistId}&mode='+mode); $Core.addUrlPager(this); return false;"
                       {else}href="{$aPager.lastUrl}"{/if}><i class="fa fa-angle-double-right fa-lg"
                                                              aria-hidden="true"></i></a></li>{/if}
        </ul>
        <div class="clear"></div>
    </div>
{/if}
