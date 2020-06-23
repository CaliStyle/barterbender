<?php
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsSearch}
<script type="text/javascript" src="{$corePath}/assets/jscript/manage.js"></script>
{/if}
<!-- Filter Search Form Layout -->

<!-- Form Header -->
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('search_filter')}
        </div>
    </div>
    <div class="panel-body">
        <form class="ynuv_video_search_form" method="post" onsubmit="return ultimatevideo.getSearchData(this);">
            <div class="form-group">
                <label for="">{_p('Title')}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}"
                       id="title" size="50"/>
            </div>

            <div class="form-group">
                <label for="">{_p('Owner')}:</label>
                <input class="form-control" type="text" name="search[owner]" value="{value type='input' id='owner'}"
                       id="owner" size="50"/>
            </div>

            <div class="form-group">
                <label for="">{_p('Category')}:</label>
                {$aCategories}
            </div>

            <div class="form-group">
                <label for="">{_p('Featured')}:</label>
                <select name="search[feature]" class="form-control">
                    <option value="0">{_p('Any')}</option>
                    <option value="featured" {value type='select' id='feature' default = 'featured'}>{_p('Featured')}</option>
                    <option value="not_featured" {value type='select' id='feature' default = 'not_featured'}>{_p('Un-Feature')}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="">{_p('video_source')}:</label>
                <select name="search[source]" class="form-control">
                    <option value="0">{_p('Any')}</option>
                    <option value="1" {value type='select' id='source' default = '1'}>{_p('Youtube')}</option>
                    <option value="2" {value type='select' id='source' default = '2'}>{_p('Vimeo')}</option>
                    <option value="3" {value type='select' id='source' default = '3'}>{_p('Uploaded')}</option>
                    <option value="4" {value type='select' id='source' default = '4'}>{_p('Dailymotion')}</option>
                    <option value="5" {value type='select' id='source' default = '5'}>{_p('video_url')}</option>
                    <option value="6" {value type='select' id='source' default = '6'}>{_p('Embed')}</option>
                    <option value="7" {value type='select' id='source' default = '7'}>{_p('Facebook')}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="">{_p('Approve')}:</label>
                <select name="search[approve]" class="form-control">
                    <option value="0">{_p('Any')}</option>
                    <option value="approved" {value type='select' id='approve' default = 'approved'}>{_p('approve_only')}</option>
                    <option value="denied" {value type='select' id='approve' default = 'denied'}>{_p('deny_only')}</option>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" id="ynuv_filter_video_submit" name="search[submit]" value="{_p('Search')}"
                       class="btn btn-primary"/>
            </div>
        </form>
    </div>
</div>
<br/>


{if count($aList) >0}
    <form method="post" id="ynuv_video_list" action="" onsubmit="return ultimatevideo.actionMultiSelect(this);">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p('video_listing')}
                </div>
            </div>

            <div class="panel-body">
                <span id="ynuv_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
                <div class="table-responsive flex-sortable">
                    <table class="table table-bordered">
                        <!-- Table rows header -->
                        <tr>
                            <th class="w40"><input type="checkbox" onclick="ultimatevideo.checkAllVideo();"
                                                   id="ynuv_video_list_check_all" name="ynuv_video_list_check_all"/>
                            </th>
                            <th class="w60"></th>
                            <th>{_p('Title')}</th>
                            <th class="t_center">{_p('Owner')}</th>
                            <th class="t_center">{_p('Featured')}</th>
                            <th class="t_center">{_p('Approve')}</th>
                            <th class="t_center">{_p('Category')}</th>
                            <th class="t_center">{_p('video_source')}</th>
                            <th class="t_center">{_p('Status')}</th>
                            <th class="t_center">{_p('Date')}</th>
                            <th class="t_center">{_p('Views')}</th>
                            <th class="t_center">{_p('Likes')}</th>
                            <th class="t_center">{_p('Comments')}</th>
                        </tr>
                        { foreach from=$aList key=iKey item=aItem }
                        <tr id="ynuv_video_row_{$aItem.video_id}" class="">
                            <td class="w40">
                                <input type="checkbox" class="video_row_checkbox" id="ynuv_video_{$aItem.video_id}"
                                       name="video_row[]" value="{$aItem.video_id}"
                                       onclick="ultimatevideo.checkDisableStatus();"/>
                            </td>
                            <td class="t_center w60">
                                <a href="javascript:void(0)" class="js_drop_down_link" title="Options"></a>
                                <div class="link_menu">
                                    <ul>
                                        <li>
                                            <a target="_blank"
                                               href="{permalink module='ultimatevideo.add' id='id_'.$aItem.video_id}">
                                                {_p('Edit')}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"
                                               onclick="ultimatevideo.deleteVideo({$aItem.video_id});">
                                                {_p('Delete')}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td style="min-width:200px">
                                <a href="{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title}">
                                    {$aItem.title|shorten:50:'...'}
                                </a>
                            </td>
                            <td class="t_center">
                                {$aItem|user}
                            </td>

                            <td id="ynuv_video_update_featured_{$aItem.video_id}" class="t_center">
                                <div class="{if $aItem.is_featured}js_item_is_active{else}js_item_is_not_active{/if}">
                                    <a class="js_item_active_link" href="javascript:void(0);"
                                       onclick="$Core.ajaxMessage();ultimatevideo.updateFeatured({$aItem.video_id}, {$aItem.is_featured});">
                                    </a>
                                </div>
                            </td>

                            <td id="ynuv_video_update_approve_{$aItem.video_id}" class="t_center">

                                <div class="{if $aItem.is_approved}js_item_is_active{else}js_item_is_not_active{/if}">
                                    <a class="js_item_active_link"
                                       href="#?call=ultimatevideo.updateApprovedInAdmin&amp;iVideoId={$aItem.video_id}&amp;iIsApproved={$aItem.is_approved}"
                                    >
                                    </a>
                                </div>
                            </td>
                            <td class="t_center">
                                {if $aItem.category_title}
                                    {softPhrase var=$aItem.category_title}
                                {else}
                                    {_p('None')}
                                {/if}
                            </td>
                            <td class="t_center">
                                {$aItem.source}
                            </td>
                            <td class="t_center">
                                {if $aItem.status == 1}
                                    {_p('Ready')}
                                {elseif $aItem.status == 0}
                                    {_p('Queued')}
                                {else}
                                    {_p('Failed')}
                                {/if}
                            </td>
                            <td class="t_center w120">
                                {$aItem.time_stamp|date:'core.global_update_time'}
                            </td>
                            <td class="t_center">
                                {$aItem.total_view}
                            </td>
                            <td class="t_center">
                                {$aItem.total_like}
                            </td>
                            <td class="t_center">
                                {$aItem.total_comment}
                            </td>
                        </tr>
                        {/foreach}
                    </table>
                </div>
                {template file="ultimatevideo.block.pager"}
            </div>
            <!-- Delete selected button -->
            <div class="panel-footer t_right">
                <input type="hidden" name="val[selected]" id="ynuv_multi_select_action" value="0"/>
                <input type="submit" name="val[delete_selected]" id="delete_selected" value="{_p('delete_selected')}"
                       class="delete_selected btn btn-primary disabled" disabled
                       onclick="return ultimatevideo.switchAction(this,'delete');"/>
                <input type="submit" name="val[approve_selected]" id="approve_selected" value="{_p('approve_selected')}"
                       class="approve_selected btn btn-primary disabled" disabled
                       onclick="return ultimatevideo.switchAction(this,'approve');"/>
                <input type="submit" name="val[unapprove_selected]" id="unapprove_selected"
                       value="{_p('unapprove_selected')}" class="unapprove_selected btn btn-primary disabled" disabled
                       onclick="return ultimatevideo.switchAction(this,'unapprove');"/>
            </div>
        </div>
    </form>
{else}
    <div>
        <p class="alert alert-warning">
            {_p('no_videos_found')}
        </p>
    </div>
{/if}