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
<form method="post" id="feedback_colorpicker" ENCTYPE="multipart/form-data" action="{if $edit == 'edit'}{url link='admincp.feedback.serverity' serverity_id=$aEdit.serverity_id page=$pageNumber}{else}{url link='admincp.feedback.serverity'}{/if}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='add_new_serverity'}
            </div>
        </div>
        <div class="panel-body">
            {if $edit == 'edit'}
                <input type="hidden" name="val[serverity_id]" value="{$aEdit.serverity_id}" />
                <input type="hidden" class="form-control"  name="val[name]" value="{$aEdit.name}"/>
            {/if}
            {field_language phrase='sPhraseName' label='Name' field='name' format='val[name_' size=30 maxlength=50 required=true}
            <div class="form-group">
                <div class="form-inline">
                    <label>
                     {_p var='pick_a_colour'}
                    </label>
                    <input type="hidden" name="val[colour]" value="{if isset($aEdit.colour)}{$aEdit.colour}{else}2681D5{/if}" data-rel="colorChooser" class="_colorpicker" />
                    <div class="_colorpicker_holder"></div>
                </div>
            </div>
            <div class="form-group">
                <label >
                    {_p var='description'}
                </label>
                {if $edit == 'edit'}
                <textarea class="form-control" name="val[description]" cols="30" rows="5" >{$aEdit.description}</textarea>
                {else}
                <textarea class="form-control" name="val[description]" cols="30" rows="5" ></textarea>
                {/if}
            </div>
        </div>
        <div class="panel-footer" >
            <input type="submit" name="{if $edit == 'edit'}val[editserverity]{else}val[submit]{/if}" value="{phrase var='core.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>
{if $edit != 'edit'}
{if count($aSers)> 0}
    <form action="{url link='current'}" method="post" id="order_display_sb">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p var='serverities_management'}
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
                        {foreach from=$aSers key=iKey item=aSer}
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td><span style="border-radius: 2px 2px 2px 2px;color: #FFFFFF !important;padding: 2px 3px;background-color: #{$aSer.colour}">{$aSer.name|shorten:30:'...'}</span></td>
                            <td>{$aSer.description|shorten:100:'...'}</td>
                            <td>{$aSer.numbers}</td>
                            <td>
                                <a id="edit_{$aSer.serverity_id}" href="{url link='admincp.feedback.serverity' serverity_id=$aSer.serverity_id page=$pageNumber }"
                                class="popup" title="{_p var='edit_serverity'}">{_p var='edit'}</a> | <a id="delete_{$aSer.serverity_id}" href="javascript:void(0);"
                                onclick="deleteServerity({$aSer.serverity_id}); return false;"
                                title="{_p var='delete_serverity'}">{_p var='delete'}</a>
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
        {_p var='no_serveritties_found'}
    </div>
{/if}
{/if}