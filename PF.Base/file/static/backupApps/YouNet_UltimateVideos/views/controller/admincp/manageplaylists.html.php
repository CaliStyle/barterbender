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
<script type="text/javascript" src="{$corePath}/assets/jscript/manageplaylist.js"></script>
{/if}
<!-- Filter Search Form Layout -->
<form class="ynuv_playlist_search_form" method="post" onsubmit="return ultimatevideo_playlist.getSearchData(this);">
    <!-- Form Header -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('search_filter')}
            </div>
        </div>

        <div class="panel-body">
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
                <label for="">{_p('Approve')}:</label>
                <select name="search[approve]" class="form-control">
                    <option value="0">{_p('Any')}</option>
                    <option value="approved" {value type='select' id='approve' default = 'approved'}>{_p('approve_only')}</option>
                    <option value="denied" {value type='select' id='approve' default = 'denied'}>{_p('deny_only')}</option>
                </select>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" id="ynuv_filter_playlist_submit" name="search[submit]" value="{_p('Search')}"
                   class="btn btn-primary"/>
        </div>
    </div>
</form>
<br/>

<div class="panel panel-default">
    {if count($aList) >0}
    <form method="post" id="ynuv_playlist_list" action=""
          onsubmit="return ultimatevideo_playlist.actionMultiSelect(this);">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('playlists_listing')}
            </div>
        </div>
        <div class="panel-body">
            <span id="ynuv_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
            <div class="table-responsive flex-sortable">
                <table class="table table-bordered">
                    <!-- Table rows header -->
                    <tr>
                        <th class="w40"><input type="checkbox" onclick="ultimatevideo_playlist.checkAllPlaylist();"
                                               id="ynuv_playlist_list_check_all" name="ynuv_playlist_list_check_all"/>
                        </th>
                        <th class="w60"></th>
                        <th class="t_center w200">{_p('Title')}</th>
                        <th class="t_center">{_p('Owner')}</th>
                        <th class="t_center w80">{_p('Featured')}</th>
                        <th class="t_center w80">{_p('Approve')}</th>
                        <th class="t_center w120">{_p('Category')}</th>
                        <th class="t_center w180">{_p('Date')}</th>
                        <th class="t_center w80">{_p('Views')}</th>
                        <th class="t_center w80">{_p('Likes')}</th>
                        <th class="t_center">{_p('Comments')}</th>
                    </tr>
                    { foreach from=$aList key=iKey item=aItem }
                    <tr id="ynuv_playlist_row_{$aItem.playlist_id}" class="">
                        <td class="w40">
                            <input type="checkbox" class="playlist_row_checkbox" id="ynuv_playlist_{$aItem.playlist_id}"
                                   name="playlist_row[]" value="{$aItem.playlist_id}"
                                   onclick="ultimatevideo_playlist.checkDisableStatus();"/>
                        </td>
                        <td class="t_center w60">
                            <a href="javascript:void(0)" class="js_drop_down_link" title="Options"></a>
                            <div class="link_menu">
                                <ul>
                                    <li>
                                        <a target="_blank"
                                           href="{permalink module='ultimatevideo.addplaylist' id='id_'.$aItem.playlist_id}">
                                            {_p('Edit')}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"
                                           onclick="ultimatevideo_playlist.deletePlaylist({$aItem.playlist_id});">
                                            {_p('Delete')}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td class="w200">
                            <a href="{permalink module='ultimatevideo.playlist' id=$aItem.playlist_id title=$aItem.title}">
                                {$aItem.title|shorten:50:'...'}
                            </a>
                        </td>
                        <td class="t_center">
                            {$aItem|user}
                        </td>
                        <td id="ynuv_playlist_update_featured_{$aItem.playlist_id}" class="t_center w80">
                            <div class="{if $aItem.is_featured}js_item_is_active{else}js_item_is_not_active{/if}">
                                <a class="js_item_active_link" href="javascript:void(0);"
                                   onclick="ultimatevideo_playlist.updateFeatured({$aItem.playlist_id}, {$aItem.is_featured});;">
                                </a>
                            </div>
                        </td>

                        <td id="ynuv_playlist_update_approve_{$aItem.playlist_id}" class="t_center w80">

                            <div class="{if $aItem.is_approved}js_item_is_active{else}js_item_is_not_active{/if}">
                                <a class="js_item_active_link" href="javascript:void(0);"
                                   onclick="ultimatevideo_playlist.updateApproved({$aItem.playlist_id}, {$aItem.is_approved});">
                                </a>
                            </div>
                        </td>
                        <td class="t_center w120">
                            {if $aItem.category_title}
                                {softPhrase var=$aItem.category_title}
                            {else}
                                {_p('None')}
                            {/if}
                        </td>
                        <td class="t_center w180">
                            {$aItem.time_stamp|date:'core.global_update_time'}
                        </td>
                        <td class="t_center w80">
                            {$aItem.total_view}
                        </td>
                        <td class="t_center w80">
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
                   onclick="return ultimatevideo_playlist.switchAction(this,'delete');"/>
            <input type="submit" name="val[approve_selected]" id="approve_selected" value="{_p('approve_selected')}"
                   class="approve_selected btn btn-primary disabled" disabled
                   onclick="return ultimatevideo_playlist.switchAction(this,'approve');"/>
            <input type="submit" name="val[unapprove_selected]" id="unapprove_selected"
                   value="{_p('unapprove_selected')}" class="unapprove_selected btn btn-primary disabled" disabled
                   onclick="return ultimatevideo_playlist.switchAction(this,'unapprove');"/>
        </div>
    </form>
</div>

{else}
    <div>
        <p class="alert alert-warning">
            {_p('no_playlists_found')}
        </p>
    </div>
{/if}