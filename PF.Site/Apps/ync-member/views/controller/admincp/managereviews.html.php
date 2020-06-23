<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsSearch}
<script type="text/javascript" src="{$corePath}/assets/jscript/manage.js"></script>
{/if}
<form class="ynmember_member_search_form" method="post" onsubmit="return ynmember.getSearchReviewData(this);">
    <div class="panel panel-default">
        <div class="panel panel-heading">
            <div class="panel-title">
                {_p('Search Filter')}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="review_by">{_p('Review By')}:</label>
                <input class="form-control" type="text" name="search[review_by]" value="{value type='input' id='review_by'}" id="review_by" size="50">
            </div>
            <div class="form-group">
                <label for="review_for">{_p('Review For')}:</label>
                <input class="form-control" type="text" name="search[review_for]" value="{value type='input' id='review_for'}" id="review_for" size="50">
            </div>
            <div class="form-group">
                <label for="title">{_p('Review Title')}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50">
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="from">{_p('From Date')}:</label>
                    <div class="input-group">
                        <input class="form-control" type="text" name="search[from]" value="{value type='input' id='from'}" id="from" size="50">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="button" id="js_from_date_anchor"><i class="fa fa-calendar" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="to">{_p('To Date')}:</label>
                    <div class="input-group">
                        <input class="form-control" type="text" name="search[to]" value="{value type='input' id='to'}" id="to" size="50">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="button" id="js_to_date_anchor"><i class="fa fa-calendar" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" id="ynmember_filter_member_submit" name="search[submit]" class="btn btn-primary">{_p('Search')}</button>
        </div>
    </div>
</form>

<span id="ynab_loading" style="display: none;">{img theme='ajax/add.gif'}</span>

{if count($aList) >0}
<form method="post" id="ynmember_review_list" action="" onsubmit="return ynmember.actionMultiSelect(this);">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var="Manage Reviews"}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="t_center w20"><input type="checkbox" onclick="ynmember.checkAllReview(this);" id="ynmember_review_list_check_all" name="ynmember_review_list_check_all"/></th>
                        <th class="t_center"></th>
                        <th class="t_center">{_p('Review Title')}</th>
                        <th class="t_center">{_p('Review By')}</th>
                        <th class="t_center">{_p('Review For')}</th>
                        <th class="t_center">{_p('Rating')}</th>
                        <th class="t_center">{_p('Review Date')}</th>
                    </tr>
                </thead>
                <tbody>
                { foreach from=$aList key=iKey item=aItem }
                    <tr id="ynmember_review_row_{$aItem.user_id}" class="">
                        <td class="w20">
                            <input type = "checkbox" class="review_row_checkbox" id="ynmember_review_{$aItem.review_id}" name="review_row[]" value="{$aItem.review_id}" onclick="ynmember.checkDisableStatus();"/>
                        </td>

                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="Options"></a>
                            <div class="link_menu">
                                <ul>
                                    <li>
                                        <a target="_blank" href="{url link='ynmember.review' user_id=$aItem.item_id}">
                                            {_p('View Details')}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="ynmember.deleteReview({$aItem.review_id});">
                                            {_p('Delete')}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>

                        <td>
                            {$aItem.title}
                        </td>
                        <td>
                            {$aItem.review_by}
                        </td>
                        <td>
                            {$aItem.review_for}
                        </td>
                        <td align="center">
                            {$aItem.rating}
                        </td>
                        <td align="center" style="min-width:100px">
                            {$aItem.time_stamp|date:'core.global_update_time'}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <!-- Delete selected button -->
        <div class="panel-footer">
            {template file="ynmember.block.review_pager"}
            <div class="t_right">
                <input type="hidden" name="val[selected]" id="ynmember_multi_select_action" value="0"/>
                <input type="submit" name="val[delete_selected]" id="delete_selected" value="{_p('Delete Selected')}" class="delete_selected btn btn-danger disabled" disabled onclick="return ynmember.deleteSelectedReview();"/>
            </div>
        </div>
    </div>
</form>
{else}
<div class="alert alert-info">
    {_p('No Reviews Found.')}
</div>
{/if}