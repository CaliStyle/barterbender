{if isset($aPager) && $aPager.totalPages > 1}
<div class="pager_outer ynmember_pager">
    <ul class="pager">
        {if isset($aPager.firstUrl)}
            <li class="first">
                <a {if $sAjax}href="javascript:void(0)" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.firstAjaxUrl}&full_name={$aForms.full_name}&email={$aForms.email}&user_group_id={if isset($aForms.user_group_id)}{$aForms.user_group_id}{/if}&is_featured={if isset($aForms.is_featured)}{$aForms.is_featured}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.firstUrl}"{/if}>
                    {phrase var='core.first'}
                </a>
            </li>
        {/if}

        {if isset($aPager.prevUrl)}
            <li>
                <a {if $sAjax}href="javascript:void(0)" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.prevAjaxUrl}&full_name={$aForms.full_name}&email={$aForms.email}&user_group_id={if isset($aForms.user_group_id)}{$aForms.user_group_id}{/if}&is_featured={if isset($aForms.is_featured)}{$aForms.is_featured}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.prevUrl}"{/if}>
                    {phrase var='core.previous'}
                </a>
            </li>
        {/if}

        {foreach from=$aPager.urls key=sLink name=pager item=sPage}
            <li {if !isset($aPager.firstUrl) && $phpfox.iteration.pager == 1} class="first"{/if}>
                <a {if $sAjax}href="javascript:void(0)" onclick="{if $sLink}$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$sPage}&full_name={$aForms.full_name}&email={$aForms.email}&user_group_id={if isset($aForms.user_group_id)}{$aForms.user_group_id}{/if}&is_featured={if isset($aForms.is_featured)}{$aForms.is_featured}{/if}&global_ajax_message=true'); $Core.addUrlPager(this);{/if} return false;{else}href="{if $sLink}{$sLink}{else}javascript:void(0);{/if}{/if}"{if $aPager.current == $sPage} class="active"{/if}>
                    {$sPage}
                </a>
            </li>
        {/foreach}

        {if isset($aPager.nextUrl)}
            <li>
                <a {if $sAjax}href="javascript:void(0)" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}&full_name={$aForms.full_name}&email={$aForms.email}&user_group_id={if isset($aForms.user_group_id)}{$aForms.user_group_id}{/if}&is_featured={if isset($aForms.is_featured)}{$aForms.is_featured}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.nextUrl}"{/if}>
                    {phrase var='core.next'}
                </a>
            </li>
        {/if}
        {if isset($aPager.lastUrl)}
            <li>
                <a {if $sAjax}href="javascript:void(0)" onclick="$Core.ajaxMessage();$.ajaxCall('{$sAjax}', 'page={$aPager.lastAjaxUrl}&full_name={$aForms.full_name}&email={$aForms.email}&user_group_id={if isset($aForms.user_group_id)}{$aForms.user_group_id}{/if}&is_featured={if isset($aForms.is_featured)}{$aForms.is_featured}{/if}&global_ajax_message=true'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.lastUrl}"{/if}>
                    {phrase var='core.last'}
                </a>
            </li>
        {/if}
    </ul>
    <div class="clear"></div>
</div>
{/if}

{literal}
<style>
    .ynmember_pager li a.active {
        background: #46b5ac;
        color: white;
    }
</style>
{/literal}

