<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/01/2017
 * Time: 13:42
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsSearch}
<script type="text/javascript" src="{$corePath}/assets/jscript/manage.js"></script>
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
                <label for="title">{_p('Title')}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50" />
            </div>

            <div class="form-group">
                <label for="author">{_p('Author')}:</label>
                <input class="form-control" type="text" name="search[author]" value="{value type='input' id='author'}" id="author" size="50" />
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label for="add_select">{_p('category')}:</label>
                        <select name="search[category]" id="add_select" class="form-control">
                            <option value="0">{_p('all_categories')}</option>
                            {foreach from=$aCategories item=aCategory}
                                <option {if isset($aForms.category) && $aCategory.category_id == $aForms.category}selected="true"{/if} value="{$aCategory.category_id}"{value type='select' id='category_id' default=$aCategory.category_id}>
                                    {softPhrase var=$aCategory.name|convert}
                                </option>
                                {foreach from=$aCategory.categories item=aSubCategory}
                                    <option {if isset($aForms.category) && $aSubCategory.category_id == $aForms.category}selected="true"{/if} value="{$aSubCategory.category_id}"{value type='select' id='category_id' default=$aSubCategory.category_id}>
                                        --{softPhrase var=$aSubCategory.name|convert}
                                    </option>
                                {/foreach}
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label for="feature">{_p('Featured')}:</label>
                        <select name="search[feature]" class="form-control" id="feature">
                            <option value="0">{_p('Any')}</option>
                            <option value="featured"  {value type='select' id='feature' default = 'featured'}>{_p('Featured')}</option>
                            <option value="not_featured"  {value type='select' id='feature' default = 'not_featured'}>{_p('Un-Featured')}</option>

                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label for="post_status">{_p('Status')}:</label>
                        <select name="search[post_status]" class="form-control">
                            <option value="0">{_p('Any')}</option>
                            <option value="draft"  {value type='select' id='post_status' default = 'draft'}>{_p('draft_only')}</option>
                            <option value="pending"  {value type='select' id='post_status' default = 'pending'}>{_p('pending_only')}</option>
                            <option value="approved"  {value type='select' id='post_status' default = 'approved'}>{_p('approve_only')}</option>
                            <option value="denied"  {value type='select' id='post_status' default = 'denied'}>{_p('deny_only')}</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Submit Buttons -->
            <div class="form-group">
                <input type="submit" value="{_p('Search')}" class="btn btn-primary"/>
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
                            <th class="t_center">{_p('Featured')}</th>
                            <th class="t_center">{_p('Category')}</th>
                            <th class="t_center">{_p('Status')}</th>
                            <th class="t_center">{_p('Date')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aList key=iKey item=aItem}
                            <tr id="js_row{$aItem.blog_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                                <td class="t_center"><input type = "checkbox" name="blog_row[]" class="checkbox" id="js_id_row{$aItem.blog_id}" value="{$aItem.blog_id}"/></td>
                                <td class="t_center">
                                    <a href="javascript:void(0)" class="js_drop_down_link" title="Options"></a>
                                    <div class="link_menu">
                                        <ul>
                                            {if $aItem.post_status == 'public' && $aItem.is_approved == 0}
                                                <li>
                                                    <a href="javascript:$.ajaxCall('ynblog.approveBlogInAdmin', 'iBlogId={$aItem.blog_id}');void(0);" class="sJsConfirm" data-message="{_p('are_you_sure_want_to_approve_this_blog')}">
                                                        {_p('Approve')}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:$.ajaxCall('ynblog.denyBlogInAdmin', 'iBlogId={$aItem.blog_id}');void(0);" class="sJsConfirm" data-message="{_p('are_you_sure_want_to_deny_this_blog')}">
                                                        {_p('Deny')}
                                                    </a>
                                                </li>
                                            {/if}
                                            <li>
                                                <a target="_blank" href="{permalink module='ynblog.add' id='id_'.$aItem.blog_id}">
                                                    {_p('Edit')}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:$.ajaxCall('ynblog.deleteBlogInAdmin', 'iBlogId={$aItem.blog_id}');void(0);" class="sJsConfirm" data-message="{_p('are_you_sure_want_to_delete_this_blog')}">
                                                    {_p('Delete')}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="td_flex">
                                    <a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" >
                                        {$aItem.title|shorten:50:'...'}
                                    </a>
                                </td>
                                <td class="t_center w120">
                                    {$aItem|user}
                                </td>
                                <td class="on_off w80">
                                    {if $aItem.canFeature}
                                        <div class="js_item_is_active"  style="{if !$aItem.is_featured}display:none;{/if}">
                                            <a href="#?call=ynblog.updateFeaturedInAdmin&amp;iBlogId={$aItem.blog_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                                        </div>
                                        <div class="js_item_is_not_active" style="{if $aItem.is_featured}display:none;{/if}">
                                            <a href="#?call=ynblog.updateFeaturedInAdmin&amp;iBlogId={$aItem.blog_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                                        </div>
                                    {/if}
                                </td>
                                <td class="t_center w120">
                                    {if $aItem.category_name}
                                        {softPhrase var=$aItem.category_name}
                                    {else}
                                        {_p('None')}
                                    {/if}
                                </td>

                                <td class="t_center w80" id="ynab_blog_update_approve_{$aItem.blog_id}">
                                    {if $aItem.post_status != 'public'}
                                        {_p var=$aItem.post_status}
                                    {else}
                                        {if $aItem.is_approved == 1}
                                            {_p var='Public'}
                                        {else}
                                            {_p var='Pending'}
                                        {/if}
                                    {/if}
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
            <input type="submit" name="val[delete_selected]" id="delete_selected" disabled value="{_p('delete_selected')}" class="sJsConfirm sJsCheckBoxButton delete_selected btn btn-primary disabled" onclick="ynadvancedblog_manage.switchAction(event, 'delete');" data-message="{_p var='are_you_sure_you_want_to_delete_this_blog_s_permanently'}"/>
            <input type="submit" name="val[approve_selected]" id="approve_selected" disabled value="{_p('approve_selected')}" class="sJsConfirm sJsCheckBoxButton approve_selected btn btn-primary disabled" onclick="ynadvancedblog_manage.switchAction(event, 'approve');" data-message="{_p var='are_you_sure_you_want_to_approve_this_blog_s_permanently'}"/>
            <input type="submit" name="val[deny_selected]" id="deny_selected" disabled value="{_p('deny_selected')}" class="sJsConfirm sJsCheckBoxButton deny_selected btn btn-primary disabled" onclick="ynadvancedblog_manage.switchAction(event, 'deny');" data-message="{_p var='are_you_sure_you_want_to_deny_this_blog_s_permanently'}"/>
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