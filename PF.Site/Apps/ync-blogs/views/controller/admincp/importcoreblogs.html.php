<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/01/2017
 * Time: 18:40
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<script type="text/javascript" src="{$corePath}/assets/jscript/manage.js"></script>
{if !$bIsSearch}
<div id="ynadvblog_dialog_content" style="display: none">
    <p>{_p('please_select_the_category_which_the_imported_blogs_will_belong_to')}</p>
    <select name="search[category]" id="add_select" class="form-control">
        <option value="0">{_p('all_categories')}</option>
        {foreach from=$aCategories item=aCategory}
            <option value="{$aCategory.category_id}">
                {softPhrase var=$aCategory.name|convert}
            </option>
            {foreach from=$aCategory.categories item=aSubCategory}
                <option value="{$aSubCategory.category_id}">
                    --{softPhrase var=$aSubCategory.name|convert}
                </option>
            {/foreach}
        {/foreach}
    </select>
</div>
{/if}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Search Filter')}
        </div>
    </div>
    <div class="panel-body">
        <!--Begin Search Filter Form-->
        <form class="ynab_blog_search_form" method="GET">
            <!-- Coupon Name-->
            <div class="form-group">
                <label for="title" class="required">{_p('Title')}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50" />
            </div>

            <div class="form-group">
                <label for="author" class="required">{_p('Owner')}:</label>
                <input class="form-control" type="text" name="search[author]" value="{value type='input' id='author'}" id="author" size="50" />
            </div>
            <div class="row dont-unbind-children">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label class="required">{_p('Created From')}:</label>
                        {select_date prefix='start_time_' id='_start_time' start_year='1970' end_year='+10' field_separator=' / '}
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label class="required">{_p('Created To')}:</label>
                        {select_date prefix='end_time_' id='_end_time' start_year='1970' end_year='+10' field_separator=' / '}
                    </div>
                </div>
            </div>
            <!-- Submit Buttons -->
            <div class="form-group">
                <input type="submit" id="ynab_filter_blog_submit" value="{_p('Search')}" class="btn btn-primary"/>
            </div>
        </form>
        <!--End Search Filter Form-->
    </div>
</div>

{if count($aList) >0}
<!--Begin Blogs Listing-->
<form method="post" id="ynab_blog_list" action="" onsubmit="return ynadvancedblog_manage.actionMultiSelect(this);">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Blog Listing')}
        </div>
    </div>
    <div class="panel-body">
        <span id="ynab_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <!-- Table rows header -->
                    <thead>
                        <tr>
                            <th class="w40"><input type="checkbox" id="js_check_box_all" class="main_checkbox" name="ynab_blog_list_check_all"/></th>
                            <th class="w60"></th>
                            <th>{_p('Title')}</th>
                            <th class="t_center">{_p('Author')}</th>
                            <th class="t_center">{_p('Date')}</th>
                        </tr>
                    </thead>
                    <tbody>
                    { foreach from=$aList key=iKey item=aItem }
                    <tr id="js_row{$aItem.blog_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="t_center"><input type = "checkbox" name="blog_row[]" class="checkbox" id="js_id_row{$aItem.blog_id}" value="{$aItem.blog_id}"/></td>
                        <td class="t_center">
                            <a href="javascript:void(0)" class="js_drop_down_link" title="Options"></a>
                            <div class="link_menu">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0)" onclick="ynadvancedblog_manage.chooseCategoryInAdmin(this, {$aItem.blog_id}); return false;">
                                            {_p('Import')}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td class="td_flex">
                            <a href="{permalink module='blog' id=$aItem.blog_id title=$aItem.title}" >
                                {$aItem.title|shorten:50:'...'}
                            </a>
                        </td>
                        <td class="t_center w120">
                            {$aItem|user}
                        </td>
                        <td class="t_center w120">
                            {$aItem.time_stamp|date:'core.global_update_time'}
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
                {template file="ynblog.block.pager"}
            </div>
        </div>
        <!-- Delete selected button -->
        <div class="panel-footer t_right">
            <input type="hidden" name="val[selected]" id="ynab_multi_select_action" value="0"/>
            <input type="button" name="val[import_selected]" id="import_selected" disabled value="{_p('Import Selected')}" class="sJsCheckBoxButton delete_selected btn btn-primary disabled" onclick="ynadvancedblog_manage.chooseCategoryInAdmin(this, 0); return false;"/>
        </div>
    </div>
</form>
{else}
    <div>
        <p class="alert alert-warning">
            {_p('no_blogs_found')}
        </p>
    </div>
{/if}

<!--End Blogs Listing-->
