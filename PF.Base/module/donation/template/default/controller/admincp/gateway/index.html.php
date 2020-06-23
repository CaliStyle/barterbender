<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='donation.payment_gateways'}
        </div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="">{phrase var='donation.manage_donate_payment_des'}</label>
            <br>
            {phrase var='donation.payment_info'}
        </div>
        <div class="table-responsive flex-sortable">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="t_center w20"></th>
                        <th>{phrase var='api.title'}</th>
                        <th class="t_center w100">{phrase var='donation.test_mode'}</th>
                        <th class="t_center w60" >{phrase var='donation.active'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$aGateways key=iKey item=aGateway}
                    <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="Manage"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="{url link='admincp.donation.gateway.edit' id={$aGateway.gateway_id}">{phrase var='donation.edit_gateway_setting'}</a></li>
                                </ul>
                            </div>
                        </td>
                        <td>{$aGateway.title}</td>
                        <td class="t_center">
                            <div class="js_item_is_active"{if !$aGateway.is_test} style="display:none;"{/if}>
                                <a href="#?call=donation.updateGatewayTest&amp;gateway_id={$aGateway.gateway_id}&amp;active=0" class="js_item_active_link" title="{phrase var='donation.disable_test_mode'}"></a>
                            </div>
                            <div class="js_item_is_not_active"{if $aGateway.is_test} style="display:none;"{/if}>
                                <a href="#?call=donation.updateGatewayTest&amp;gateway_id={$aGateway.gateway_id}&amp;active=1" class="js_item_active_link" title="{phrase var='donation.enable_test_mode'}"></a>
                            </div>
                        </td>
                        <td class="t_center">
                            <div class="js_item_is_active"{if !$aGateway.is_active} style="display:none;"{/if}>
                                <a href="#?call=donation.updateGatewayActivity&amp;gateway_id={$aGateway.gateway_id}&amp;active=0" class="js_item_active_link" title="{phrase var='admincp.deactivate'}"></a>
                            </div>
                            <div class="js_item_is_not_active"{if $aGateway.is_active} style="display:none;"{/if}>
                                <a href="#?call=donation.updateGatewayActivity&amp;gateway_id={$aGateway.gateway_id}&amp;active=1" class="js_item_active_link" title="{phrase var='admincp.activate'}"></a>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>