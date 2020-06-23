<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !Phpfox::isModule('socialbridge')}
<div class="alert alert-danger">
	<strong>{phrase var='opensocialconnect.please_install_social_bridge_plugin_first'}</strong>
</div>
{else}
<div class="alert alert-info">
    <strong>
        {phrase var='opensocialconnect.all_social_api_keys_configuration_was_setup_in'} <a href="{url link='admincp.socialbridge.providers'}" target="_blank">{url link='admincp.socialbridge.providers'}</a>
    </strong>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{phrase var='opensocialconnect.mange_social_providers'}</div>
    </div>
    <table class="table table-bordered" id="js_drag_drop" cellpadding="0" cellspacing="0">
        <tr>
            <th></th>
            <th class="t_center" style="width:40px;"></th>
            <th class="t_center" style="width:80px;">{phrase var='opensocialconnect.name'}</th>
            <th >{phrase var='opensocialconnect.title'}</th>
            <th class="t_center" style="width:60px;">{phrase var='rss.active'}</th>
            <th class="t_center" style="width:60px;">{phrase var='opensocialconnect.option'}</th>
        </tr>
        {foreach from=$aOpenProviders key=iKey item=aOpenProvider}
        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
            <td class="drag_handle"><input type="hidden" name="val[ordering][{$aOpenProvider.service_id}]" value="{$aOpenProvider.ordering}" /></td>
            <td class="t_center">
                <img src="{$sCoreUrl}module/opensocialconnect/static/image/{$aOpenProvider.name}.png" alt="{$aOpenProvider.title}" width="32px"/>
            </td>
            <td>{$aOpenProvider.name}</td>
            <td>
                {$aOpenProvider.title}
            </td>
            <td class="t_center">
                <div class="js_item_is_active"{if !$aOpenProvider.is_active} style="display:none;"{/if}>
                    <a href="#?call=opensocialconnect.updateActivity&amp;id={$aOpenProvider.service_id}&amp;active=0" class="js_item_active_link" title="{phrase var='rss.deactivate'}"></a>
                </div>
                <div class="js_item_is_not_active"{if $aOpenProvider.is_active} style="display:none;"{/if}>
                    <a href="#?call=opensocialconnect.updateActivity&amp;id={$aOpenProvider.service_id}&amp;active=1" class="js_item_active_link" title="{phrase var='rss.activate'}"></a>
                </div>
            </td>
            <td class="t_center">
                <a href="#" onclick="tb_show('{phrase var='opensocialconnect.profile_questions_synchronization' phpfox_squote=true}', $.ajaxBox('opensocialconnect.mappingData', 'height=240&amp;width=400&amp;provider={$aOpenProvider.name}'));return false;">
                    {phrase var='admincp.edit'}
                </a>
            </td>
        </tr>
    {/foreach}
    </table>
</div>
{/if}
