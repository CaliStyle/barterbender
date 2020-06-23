<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Phpfox
 * @version        $Id: index.html.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='payment_gateways'}
        </div>
    </div>
    <div class="alert alert-info" style="margin: 0">
        {_p var='manage_donate_payment_des'}
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <th style="width:20px;"></th>
                <th>{_p var='api.title'}</th>
                <th class="t_center" style="width:100px;">{_p var='test_mode'}</th>
                <th class="t_center" style="width:60px;">{_p var='active'}</th>
            </tr>
            {foreach from=$aGateways key=iKey item=aGateway}
            <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="Manage"></a>
                    <div class="link_menu">
                        <ul>
                            <li>
                                <a href="{url link='admincp.fundraising.gateway.edit' id={$aGateway.gateway_id}">
                                    {_p var='edit_gateway_setting'}
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
                <td>{$aGateway.title}</td>
                <td class="t_center">
                    <div class="js_item_is_active" style="{if !$aGateway.is_test}display:none;{/if}">
                        <a href="#?call=fundraising.updateGatewayTest&amp;gateway_id={$aGateway.gateway_id}&amp;active=0"
                           class="js_item_active_link" title="{_p var='disable_test_mode'}">
                        </a>
                    </div>
                    <div class="js_item_is_not_active" style="{if $aGateway.is_test}display:none;{/if}">
                        <a href="#?call=fundraising.updateGatewayTest&amp;gateway_id={$aGateway.gateway_id}&amp;active=1"
                           class="js_item_active_link" title="{_p var='enable_test_mode'}"></a>
                    </div>
                </td>
                <td class="t_center">
                    <div class="js_item_is_active" style="{if !$aGateway.is_active}display:none;{/if}">
                        <a href="#?call=fundraising.updateGatewayActivity&amp;gateway_id={$aGateway.gateway_id}&amp;active=0"
                           class="js_item_active_link" title="{_p var='admincp.deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active" style="{if $aGateway.is_active}display:none;{/if}">
                        <a href="#?call=fundraising.updateGatewayActivity&amp;gateway_id={$aGateway.gateway_id}&amp;active=1"
                           class="js_item_active_link" title="{_p var='admincp.activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>