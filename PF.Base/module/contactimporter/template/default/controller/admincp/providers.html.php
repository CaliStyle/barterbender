<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: index.html.php 1424 2010-01-25 13:34:36Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
{if count($providers)}
<div id="provider-list" class="panel panel-default table-responsive">
	<table id="table-2" class="table table-bordered">
		<thead>
            <tr style="cursor: none">
                <th class="w200">{_p var='admincp_providers_title'}</th>
                <th>{_p var='admincp_providers_type'}</th>
                <th>{_p var='admincp_providers_logo'}</th>
                <th class="providers-active">{_p var='enabled'}</th>
            </tr>
		</thead>
        <tbody>
            {foreach from=$providers key=iKey item=provider}
            <tr id="{$provider.name}" class="{if is_int($iKey/2)} tr{else}{/if}" style="cursor:move;">
                <td>{$provider.title|convert|clean}</td>
                <td class="provider-type">{$provider.type}</td>
                <td style="width:10px"><img style="width: 50px" src="{$core_url}module/contactimporter/static/image/{$provider.logo}_status_up.png" /></td>
                <td class="w80 t_center">
                    <div class="js_item_is_active"{if !$provider.enable} style="display:none;"{/if}>
                        <a href="#?call=contactimporter.updateProviderActive&amp;provider_name={$provider.name}&amp;active=0" class="js_item_active_link" title="{_p('deactivate')}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $provider.enable} style="display:none;"{/if}>
                        <a href="#?call=contactimporter.updateProviderActive&amp;provider_name={$provider.name}&amp;active=1" class="js_item_active_link" title="{_p('activate')}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
	</table>
</div>
{/if}

