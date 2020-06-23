<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if count($providers)}
<div id="provider-list" class="panel panel-default">
    <div class="table-responsive">
        <table id="title" class="table table-bordered">
            <tr>
                <th width="70%">{_p var='admincp_providers_title'}</th>
                <th>{_p var='admincp_providers_totalinvitations'}</th>
            </tr>
            {foreach from=$providers key=iKey item=provider}
            <tr class="{if is_int($iKey/2)} tr{else}{/if}">
                <td width="70%">{$provider.title|convert|clean}</td>
                <td>{$provider.iTotalInvitations}</td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>
{/if}