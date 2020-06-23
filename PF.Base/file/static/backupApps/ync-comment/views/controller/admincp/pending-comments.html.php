<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<style type="text/css">
    .table-responsive .content-photo {
        padding-top: 5px;
    }
    .table-responsive .content-sticker {
        padding-top: 5px;
    }
    .table-responsive .content-photo .item-photo img {
        max-height: 150px;
    }
    .table-responsive .content-sticker {
        line-height: 0;
    }
    .table-responsive .content-sticker .item-sticker img {
        max-height: 80px;
    }
    .table-responsive .content-text .item-tag-emoji img {
        max-width: 16px;
    }
</style>
{/literal}
<form method="post" id="manage_sticker_set" action="{url link='admincp.ynccomment.pending-comments'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='pending_comments'}
            </div>
        </div>
        {if count($aComments)}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th class="w40 js_checkbox">
                            <input type="checkbox" name="val[ids]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                        </th>
                        <th class="w40"></th>
                        <th class="t_center w120">{_p var='date'}</th>
                        <th class="t_center w120">{_p var='user'}</th>
                        <th class="t_center w180">{_p var='item'}</th>
                        <th class="t_center">{_p var='content'}</th>
                    </tr>
                    {foreach from=$aComments item=aComment}
                        <tr>
                            <td class="t_center js_checkbox">
                                <input type="checkbox" name="ids[]" class="checkbox" value="{$aComment.comment_id}" id="js_id_row{$aComment.comment_id}" />
                            </td>
                            <td class="w40">
                                <a href="#" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                                <div class="link_menu">
                                    <ul>
                                        <li><a href="#" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('ynccomment.moderateSpam', 'id={$aComment.comment_id}&amp;action=approve&amp;inacp=1'); return false;{r},function(){l}{r});" >{_p var='approve'}</a></li>
                                        <li><a href="#" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('ynccomment.moderateSpam', 'id={$aComment.comment_id}&amp;action=deny&amp;inacp=1'); return false;{r},function(){l}{r});">{_p var='deny'}</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td class="w120">
                                {$aComment.time_stamp}
                            </td>
                            <td class="w120">
                                {$aComment|user}
                            </td>
                            <td class="w180">
                                {if $aComment.item_name}
                                    {$aComment.item_name}
                                {/if}
                            </td>
                            <td>
                                <div class="content-text {if $aComment.view_id == '1'}row_moderate{/if}" id="js_comment_text_{$aComment.comment_id}">
                                    {$aComment.text|ynccomment_parse|shorten:'300':'comment.view_more':true|split:30|max_line}
                                </div>
                                {if !empty($aComment.extra_data)}
                                    {if $aComment.extra_data.extra_type == 'photo'}
                                        <div class="content-photo">
                                            <span class="item-photo">
                                                {img server_id=$aComment.extra_data.server_id path='core.url_pic' file="ynccomment/".$aComment.extra_data.image_path suffix='_500'}
                                            </span>
                                        </div>
                                    {elseif $aComment.extra_data.extra_type == 'sticker'}
                                    <div class="content-sticker">
                                        <span class="item-sticker">
                                            {$aComment.extra_data.full_path}
                                        </span>
                                    </div>
                                    {/if}
                                {/if}

                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
            <div class="panel-footer">
                <input type="submit" name="val[approve_selected]" id="approve_selected" disabled value="{_p('approve_selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-success disabled"/>
                <input type="submit" name="val[deny_selected]" id="deny_selected" disabled value="{_p('deny_selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-danger disabled"/>
            </div>
        {else}
            <div class="alert alert-info">
                {_p var='no_comments'}
            </div>
        {/if}
    </div>
    {if count($aComments)}
        {pager}
    {/if}
</form>