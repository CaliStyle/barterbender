{if isset($aPager) && $aPager.totalPages > 1}
    <div class="pager_outer">
        <ul class="pager">
            {if isset($aPager.firstUrl)}
                <li class="first"><a {if $sAjax}href="#"
                                     onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.firstAjaxUrl}&title={$aForms.title}&owner={$aForms.owner}&category={$aForms.category_id}{if isset($aForms.source)}&source={$aForms.source}{/if}&feature={$aForms.feature}&approve={$aForms.approve}'); $Core.addUrlPager(this); return false;"
                                     {else}href="{$aPager.firstUrl}"{/if}>{phrase var='core.first'}</a></li>{/if}
            {if isset($aPager.prevUrl)}
                <li><a {if $sAjax}href="#"
                       onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.prevAjaxUrl}&title={$aForms.title}&owner={$aForms.owner}&category={$aForms.category_id}{if isset($aForms.source)}&source={$aForms.source}{/if}&feature={$aForms.feature}&approve={$aForms.approve}'); $Core.addUrlPager(this); return false;"
                       {else}href="{$aPager.prevUrl}"{/if}>{phrase var='core.previous'}</a></li>{/if}
            {foreach from=$aPager.urls key=sLink name=pager item=sPage}
                <li {if !isset($aPager.firstUrl) && $phpfox.iteration.pager == 1} class="first"{/if}><a
                            {if $sAjax}href="#"
                            onclick="{if $sLink}$.ajaxCall('{$sAjax}', 'page={$sPage}&title={$aForms.title}&owner={$aForms.owner}&category={$aForms.category_id}{if isset($aForms.source)}&source={$aForms.source}{/if}&feature={$aForms.feature}&approve={$aForms.approve}'); $Core.addUrlPager(this);{/if} return false;{else}href="
                            {if $sLink}{$sLink}{else}javascript:void(0);{/if}{/if}
                    "{if $aPager.current == $sPage} class="active" style="background:#ddd;"{/if}>{$sPage}</a></li>
            {/foreach}
            {if isset($aPager.nextUrl)}
                <li><a {if $sAjax}href="#"
                       onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}&title={$aForms.title}&owner={$aForms.owner}&category={$aForms.category_id}{if isset($aForms.source)}&source={$aForms.source}{/if}&feature={$aForms.feature}&approve={$aForms.approve}'); $Core.addUrlPager(this); return false;"
                       {else}href="{$aPager.nextUrl}"{/if}>{phrase var='core.next'}</a></li>{/if}
            {if isset($aPager.lastUrl)}
                <li><a {if $sAjax}href="#"
                       onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.lastAjaxUrl}&title={$aForms.title}&owner={$aForms.owner}&category={$aForms.category_id}&{if isset($aForms.source)}&source={$aForms.source}{/if}&feature={$aForms.feature}&approve={$aForms.approve}'); $Core.addUrlPager(this); return false;"
                       {else}href="{$aPager.lastUrl}"{/if}>{phrase var='core.last'}</a></li>{/if}
        </ul>
        <div class="clear"></div>
    </div>
{/if}
