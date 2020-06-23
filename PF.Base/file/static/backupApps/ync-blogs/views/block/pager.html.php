<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/01/2017
 * Time: 14:59
 */
?>
{if isset($aPager) && $aPager.totalPages > 1}
<div class="pager_outer">
    <ul class="pager">
        {if isset($aPager.firstUrl)}<li class="first"><a {if $sAjax}href="#" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.firstAjaxUrl}&title={$aForms.title}&owner={$aForms.author}&category={if isset($aForms.category_id)}{$aForms.category_id}{/if}&feature={if isset($aForms.feature)}{$aForms.feature}{/if}&approve={if isset($aForms.post_status)}{$aForms.post_status}{/if}&from_time={if isset($aForms.from_time)}{$aForms.from_time}{/if}&end_time={if isset($aForms.end_time)}{$aForms.end_time}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.firstUrl}"{/if}>{phrase var='core.first'}</a></li>{/if}
        {if isset($aPager.prevUrl)}<li><a {if $sAjax}href="#" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.prevAjaxUrl}&title={$aForms.title}&owner={$aForms.author}&category={if isset($aForms.category_id)}{$aForms.category_id}{/if}&feature={if isset($aForms.feature)}{$aForms.feature}{/if}&approve={if isset($aForms.post_status)}{$aForms.post_status}{/if}&from_time={if isset($aForms.from_time)}{$aForms.from_time}{/if}&end_time={if isset($aForms.end_time)}{$aForms.end_time}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.prevUrl}"{/if}>{phrase var='core.previous'}</a></li>{/if}

        {foreach from=$aPager.urls key=sLink name=pager item=sPage}
            <li {if !isset($aPager.firstUrl) && $phpfox.iteration.pager == 1} class="first"{/if}><a {if $sAjax}href="#" onclick="{if $sLink}$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$sPage}&title={$aForms.title}&owner={$aForms.author}&category={if isset($aForms.category_id)}{$aForms.category_id}{/if}&feature={if isset($aForms.feature)}{$aForms.feature}{/if}&approve={if isset($aForms.post_status)}{$aForms.post_status}{/if}&from_time={if isset($aForms.from_time)}{$aForms.from_time}{/if}&end_time={if isset($aForms.end_time)}{$aForms.end_time}{/if}&global_ajax_message=true'); $Core.addUrlPager(this);{/if} return false;{else}href="{if $sLink}{$sLink}{else}#{/if}{/if}"{if $aPager.current == $sPage} class="active"{/if}>{$sPage}</a></li>
        {/foreach}

        {if isset($aPager.nextUrl)}<li><a {if $sAjax}href="#" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}&title={$aForms.title}&owner={$aForms.author}&category={if isset($aForms.category_id)}{$aForms.category_id}{/if}&feature={if isset($aForms.feature)}{$aForms.feature}{/if}&approve={if isset($aForms.post_status)}{$aForms.post_status}{/if}&from_time={if isset($aForms.from_time)}{$aForms.from_time}{/if}&end_time={if isset($aForms.end_time)}{$aForms.end_time}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.nextUrl}"{/if}>{phrase var='core.next'}</a></li>{/if}
        {if isset($aPager.lastUrl)}<li><a {if $sAjax}href="#" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.lastAjaxUrl}&title={$aForms.title}&owner={$aForms.author}&category={if isset($aForms.category_id)}{$aForms.category_id}{/if}&feature={if isset($aForms.feature)}{$aForms.feature}{/if}&approve={if isset($aForms.post_status)}{$aForms.post_status}{/if}&from_time={if isset($aForms.from_time)}{$aForms.from_time}{/if}&end_time={if isset($aForms.end_time)}{$aForms.end_time}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.lastUrl}"{/if}>{phrase var='core.last'}</a></li>{/if}
    </ul>
    <div class="clear"></div>
</div>
{/if}
