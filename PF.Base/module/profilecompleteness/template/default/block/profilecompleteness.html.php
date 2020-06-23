<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
{literal}
<style type="text/css">
    .profile_completed {
        background-color: #337AB7;
        border-radius: 3px 3px 3px 3px;
        height: 12px;
    }

    .profile_project_content span.percent {
        font-size: 18px;
        font-weight: bold;

    }

    .profile_completed_box {
        border-radius: 3px 3px 3px 3px;
    }

    .layout_profile_completeness_profile_completeness > ul > li + li {
        margin-top: 5px;
        font-size: 1.0em;

    }

    .profile_project_content a:link, a:visiter {
        color: #5F93B4;
        text-decoration: none;

    }

    .ynpcompleteness_btn_block {
        margin-top: 15px !important;
    }
</style>
{/literal}

<div class="layout_profile_completeness_profile_completeness">
    <ul class="profile_project_content">
        <li>
            <span class="percent">{$iPercent}%</span> {phrase var='profilecompleteness.profile_completeness'}
        </li>
        <li>
            <div class="profile_completed_box"
                 style="background-color: {if $colorbackground != ''}{$colorbackground};{else}#EEEEEE;{/if}">
                <div class="profile_completed" style="width: {$iPercent}%;">
                </div>
            </div>
        </li>
        {if $PercentTotal!=100}
        {if $iGroup_id == 'basic'}
        <li>
            {phrase var='profilecompleteness.next'}: <a
                    href="{if $isPhoTo==1}{url link='user.profile'}{else}{url link='user.photo'}{/if}">+ {$Key}
                (+{$PercentValue}%)</a>
        </li>
        {else}
        <li>
            {phrase var='profilecompleteness.next'}: <a
                    href="{if $isPhoTo==1}{url link='user.profile' group=$iGroup_id}{else}{url link='user.photo'}{/if}">+
                {$Key} (+{$PercentValue}%)</a>
        </li>
        {/if}
        <li class="text-center">
            <a class="btn btn-success btn-sm" href="{url link='user.profile'}">{phrase
                var='profilecompleteness.update_profile'}</a>
        </li>

        {/if}
    </ul>
</div>

{literal}
<script type="text/javascript">
    $Behavior.profile_completeness_block = function(){
        setTimeout(function(){
            $('.layout_profile_completeness_profile_completeness').find('a[href*="user/photo"]').off('click');
        },100);
    }
</script>
{/literal}


