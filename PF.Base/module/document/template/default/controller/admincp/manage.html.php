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

<form method="get" action="{url link='admincp.document.manage'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{phrase var='title'}</label>
                {$aFilters.search}
            </div>
            <div class="form-group">
                <label>{phrase var='owner'}:</label>
                {$aFilters.user}
            </div>
            <div class="form-group">
                <label>{phrase var='status'}</label>
                {$aFilters.status}
            </div>
        </div>
        <div class="panel-footer">
            <button class="btn btn-primary" type="submit" name="search[submit]" value="{phrase var='submit'}">{phrase
                var='submit'}
            </button>
        </div>
    </div>
</form>
{if count($aDocuments)}
<div id="document_public_message" class="public_message" style="display:none"></div>

<form method="post" action="{url link='admincp.document.manage'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='documents'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="t_center w20"><input type="checkbox" name="val[id]" value="" id="js_check_box_all"
                                                   class="main_checkbox"/></th>
                    <th class="t_center w60"></th>
                    <th class="t_center">{phrase var='title'}</th>
                    <th class="t_center">{phrase var='owner'}</th>
                    <th class="t_center">{phrase var='created'}</th>
                    <th class="t_center">{phrase var='approved'}</th>
                    <th class="t_center">{phrase var='featured'}</th>
                    <th class="t_center">{phrase var='allow_br_download'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aDocuments key=iKey item=aDocument}
                <tr id="jp_row_{$aDocument.document_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td class="t_center w20">
                        <input type="checkbox" name="id[]" class="checkbox" value="{$aDocument.document_id}" id="js_id_row{$aDocument.document_id}"/>
                    </td>
                    <td class="t_center w60">
                        <a href="#" class="js_drop_down_link" title="Manage"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{$aDocument.link}" target="_blank">{phrase var='view'}</a></li>
                                <li><a href="{$aDocument.edit_link}" target="_blank">{phrase var='edit'}</a></li>
                                <li><a href="javascript:void(0)"
                                       onclick="$.ajaxCall('document.adminDelete', 'document_id={$aDocument.document_id}'); return false;">{phrase
                                        var='delete'}</a></li>
                            </ul>
                        </div>
                    </td>

                    <td class="t_center">
                        <a href="{$aDocument.link}" target="_blank" title="{$aDocument.title|clean}">
                            {$aDocument.short_title}
                        </a>
                    </td>

                    <td class="t_center">{$aDocument.full_name}</td>

                    <td class="t_center">{$aDocument.created_date}</td>

                    <td class="t_center">
                        <div class="js_item_is_active" style="{if !$aDocument.is_approved}display:none;{/if}">
                            <a href="#?call=document.updateApprove&amp;id={$aDocument.document_id}&amp;active=0"
                               class="js_item_active_link" title="{phrase var='disapprove'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aDocument.is_approved}display:none;{/if}">
                            <a href="#?call=document.updateApprove&amp;id={$aDocument.document_id}&amp;active=1"
                               class="js_item_active_link" title="{phrase var='approve'}"></a>
                        </div>
                    </td>

                    <td class="t_center">
                        <div class="js_item_is_active" style="{if !$aDocument.is_featured}display:none;{/if}">
                            <a href="#?call=document.updateFeature&amp;id={$aDocument.document_id}&amp;active=0"
                               class="js_item_active_link" title="{phrase var='un_feature'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aDocument.is_featured}display:none;{/if}">
                            <a href="#?call=document.updateFeature&amp;id={$aDocument.document_id}&amp;active=1"
                               class="js_item_active_link" title="{phrase var='feature'}"></a>
                        </div>
                    </td>

                    <td class="t_center">
                        <div class="js_item_is_active" style="{if !$aDocument.allow_download}display:none;{/if}">
                            <a href="#?call=document.updateAllowDownload&amp;id={$aDocument.document_id}&amp;active=0"
                               class="js_item_active_link" title="{phrase var='dont_allow_download'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aDocument.allow_download}display:none;{/if}">
                            <a href="#?call=document.updateAllowDownload&amp;id={$aDocument.document_id}&amp;active=1"
                               class="js_item_active_link" title="{phrase var='allow_download'}"></a>
                        </div>
                    </td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        </div>

        <div class="panel-footer">
            <button type="submit" name="approve" value="1" class="sJsConfirm approve btn btn-primary sJsCheckBoxButton disabled"
                    disabled="disabled">{phrase var='approve_selected'}
            </button>
            <button type="submit" name="feature" value="1" class="sJsConfirm feature btn btn-primary sJsCheckBoxButton disabled"
                    disabled="disabled">{phrase var='feature_selected'}
            </button>
            <button type="submit" name="delete" value="1" class="sJsConfirm delete btn btn-danger sJsCheckBoxButton disabled"
                    disabled="disabled">{phrase var='delete_selected'}
            </button>
        </div>
    </div>
</form>

{pager}

{else}
<div class="alert alert-info">{phrase var='no_documents_found'}</div>
{/if}
