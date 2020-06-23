<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-body">
        {if empty($aStats)}
        <div class="message">{phrase var='opensocialconnect.no_providers_found'}</div>
        {else}
        <table class="table table-bordered" id="js_opensocialconnect_stat" cellpadding="0" cellspacing="0">
            <tr>
                <th>{phrase var='opensocialconnect.provider'}</th>
                <th class="text-center">{phrase var='opensocialconnect.total_signup'}</th>
                <th class="text-center">{phrase var='opensocialconnect.total_login'}</th>
            </tr>
            {foreach from=$aStats name=stats item=aStat}
            <tr{if is_int($phpfox.iteration.stats/2)} class="tr"{/if}>
                <td>{$aStat.title}</td>
                <td class="text-center">{$aStat.total_signup}</td>
                <td class="text-center">{$aStat.total_login}</td>
            </tr>
            {/foreach}
        </table>
        {/if}
    </div>
</div>
