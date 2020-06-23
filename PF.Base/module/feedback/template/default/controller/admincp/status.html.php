<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<style type="text/css">
    #feedback_colorpicker ._colorpicker_holder {
        position: inherit;
        width: 64px;
        height: 32px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        display: inline-block;
        vertical-align: middle;
        margin-left: 10px;
    }
</style>
{/literal}
<form method="post" id="feedback_colorpicker" ENCTYPE="multipart/form-data" action="{if $edit == 'edit'}{url link='admincp.feedback.status' status=$aStatus.status_id page=$page_number}{else}{url link='admincp.feedback.status'}{/if}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if $edit == 'edit'}
                    {_p var='edit_a_status'}
                {else}
                    {_p var='add_new_status'}
                {/if}
            </div>
        </div>
        <div class="panel-body">
            {if $edit == 'edit'}
                <input type="hidden" name="val[status_id]" value="{$aStatus.status_id}" />
                <input type="hidden" name="val[name]" value="{$aStatus.name}" />
            {/if}
            {field_language phrase='sPhraseName' label='Name' field='name' format='val[name_' size=30 maxlength=50 required=true}

            <div class="form-group">
                <div class="form-inline">
                <label>{_p var='pick_a_colour'}</label>
                <input type="hidden" name="val[colour]" value="{if isset($aStatus.colour)}{$aStatus.colour}{else}2681D5{/if}" data-rel="colorChooser" class="_colorpicker" />
                <div class="_colorpicker_holder"></div>
            </div>
            <div class="form-group">
                <label>
                   {_p var='description'}
                </label>
                 {if $edit == 'edit'}
                    <textarea class="form-control" name="val[description]" cols="30" rows="5" >{$aStatus.description}</textarea>
                 {else}
                    <textarea class="form-control" name="val[description]" cols="30" rows="5" ></textarea>
                 {/if}
            </div>
        </div>
        <div class="panel-footer">
            {if $edit == 'edit'}
                <input type="submit" name="val[editstatus]" value="{_p var='save_changes'}" class="btn btn-primary" />
            {else}
                <input type="submit" name="val[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            {/if}
        </div>
    </div>
</form>
{if $edit != 'edit'}
    {if count($sStatus)>0}
        <form action="{url link='current'}" method="post" id="order_display_sb" >
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        {_p var='status_management'}
                    </div>
                </div>
                <div class="table-responsive">
                    <table align="center" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="w220">{_p var='name'}</th>
                                <th>{_p var='description'}</th>
                                <th class="w180">{_p var='number_of_times_used'}</th>
                                <th class="w100">{_p var='options'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$sStatus key=iKey item=aStatus}
                                <tr  class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                                    <td><span style="border-radius: 2px 2px 2px 2px;color: #FFFFFF !important;padding: 2px 3px;background-color: #{$aStatus.colour}">{$aStatus.name|shorten:30:'...'}</span></td>
                                    <td>{$aStatus.description|shorten:100:'...'}</td>
                                    <td>{$aStatus.numbers}</td>
                                   <td>
                                        <a id="edit_{$aStatus.status_id}" href="{url link='admincp.feedback.status' status=$aStatus.status_id page=$page_number }" class="popup" title="{_p var='edit_status'}">{_p var='edit'}</a>  |
                                        <a id="delete_{$aStatus.status_id}" onclick="deleteStatus({$aStatus.status_id}); return false;" href="javascript:void(0);" title="{_p var='delete_status'}">{_p var='delete'}</a>
                                   </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        {pager}
    {else}
        <div class="extra_info">
            {_p var='no_status_found'}
        </div>
    {/if}
{/if}



