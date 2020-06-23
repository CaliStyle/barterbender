<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 *
 *
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
?>

{if count($aLicenses)}
<form method="post" action="{url link='admincp.document.managelicense'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='manage_licenses'}
            </div>
        </div>
        <div class="alert alert-info">
            {_p var='tip_delete_license'}
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>
                        <input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox"/>
                    </th>
                    <th>{phrase var='license_name'}</th>
                    <th>{phrase var='license_icon'}</th>
                    <th>{phrase var='reference_link'}</th>
                    <th>{phrase var='created'}</th>
                    <th>{phrase var='manage'}</th>
                </tr>
                {foreach from=$aLicenses key=iKey item=aLicense}
                <tr id="js_row{$aLicense.license_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td>
                        <input type="checkbox" name="id[]" class="checkbox" value="{$aLicense.license_id}"
                               id="js_id_row{$aLicense.license_id}">
                    </td>
                    <td> {$aLicense.license_name}</td>
                    <td><img src="{$aLicense.image_url}" style="max-width: 120px; max-height: 120px;"/>
                    <td><a href="{$aLicense.reference_url}" target="_blank"> {$aLicense.reference_url}</a></td>
                    <td>{$aLicense.time_stamp|date:'core.global_update_time'}</td>
                    <td><a href="{$aLicense.edit_link}">{phrase var='edit'}</a></td>
                </tr>
                {/foreach}
            </table>
        </div>
        <div class="panel-footer t_right">
            <button type="submit" name="delete" class="sJsConfirm delete btn btn-danger sJsCheckBoxButton disabled"
                    disabled="disabled">{phrase var='delete_selected'}
            </button>
        </div>
    </div>
</form>
{else}
<div class="alert alert-info">
    {phrase var='no_document_licenses_have_been_created'} <a href="{url link='admincp.document.addlicense'}">{phrase
        var='create_one_now'}</a>.
</div>
{/if}
