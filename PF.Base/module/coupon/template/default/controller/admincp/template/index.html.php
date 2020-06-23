<?php

/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         AnNT
 * @package        Module_Coupon
 * @version        3.02
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='print_templates'}
        </div>
    </div>
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="t_center">{phrase var='template'}</th>
                    <th class="t_center">{phrase var='action'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aTemplates key=iKey item=aTemplate}
                <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td>{$aTemplate.name}</td>
                    <td>
                        <a href="#" onclick="tb_show('Preview', $.ajaxBox('coupon.blockPreview', 'width=450&id={$aTemplate.template_id}')); return false;">{phrase var='preview'}</a> |
                        <a href="{url link='admincp.coupon.template.add' id=$aTemplate.template_id}">{phrase var='edit'}</a> |
                        <a title="{phrase var='delete'}" href="{url link='admincp.coupon.template.delete' id=$aTemplate.template_id};" class="sJsConfirm" data-message="{phrase var='are_you_sure'}" > {phrase var='delete'} </a>
                    </td>
                </tr>
                {foreachelse}
                <tr class="checkRow tr">
                    <td colspan="2">{phrase var='no_template_found'}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>