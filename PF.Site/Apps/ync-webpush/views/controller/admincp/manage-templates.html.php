<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="panel panel-default ync-webpush-template-holder">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='notification_templates'}
        </div>
    </div>
        <div class="row">
            <div class="col-xs-5 col-sm-5 ync-left pr-0">
                <div class="ync-webpush-list-template">
                    <div class="ync-webpush-template-search-form">
                        <form action="{url link='admincp.yncwebpush.manage-templates'}" method="get">
                            <div class="form-inline ync-search-form">
                                <div class="ync-search-input">
                                    <input type="text" class="form-control" name="search[title]" placeholder="{_p var='search_by_title'}" value="{value type='input' id='title'}">
                                    <button type="submit" class="ico ico-search-o btn-default ync-search-submit"></button>
                                </div>
                                <a href="{$sCreateLink}">{_p var='add_new'}</a>
                            </div>
                        </form>
                    </div>
                    <hr class="divider"/>
                    <div class="ync-webpush-template-items">
                        {if count($aTemplates)}
                            <ul class="item-wrapper">
                                {foreach from=$aTemplates item=aTemplate}
                                    <li class="item" onclick="yncwebpush_admin.loadTemplateDetail($(this), {$aTemplate.template_id});" title="{_p var='click_to_view_detail'}">
                                        <a href="{url link='admincp.yncwebpush.manage-templates' delete=$aTemplate.template_id}" class="item-delete sJsConfirm"><i class="ico ico-trash-o"></i></a>
                                        <div class="item-title">
                                            <label>{$aTemplate.template_name|clean}</label>
                                        </div>
                                        <div class="item-time">
                                            {_p var='created_on'}: {$aTemplate.time_stamp|convert_time:'core.global_update_time'}
                                        </div>
                                    </li>
                                {/foreach}
                                <div class="item-loadmore">
                                    {pager}
                                </div>
                            </ul>
                        {else}
                            {if !$bIsSearch}
                                <div class="extra_info">
                                    <a href="{$sCreateLink}">
                                        <i class="ico ico-folder-alt" style="font-size: 100px;"></i>
                                    </a>
                                    <div class="notice">
                                        {_p var='you_have_not_yet_created_any_template'}
                                        <br/>
                                        {_p var='try_to_create_a_new_one_a_href_now' href=$sCreateLink}
                                    </div>
                                </div>
                            {else}
                                <div class="extra_info" style="padding: 15px 0;">
                                    {_p var='no_results_found'}
                                </div>
                            {/if}
                        {/if}
                    </div>
                </div>
            </div>
            <div class="col-xs-7 col-sm-7 ync-right">
                <div id="ync-loading" class="loading" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="ync-webpush-detail-template" id="js_ync_webpush_detail_template">
                </div>
            </div>
    </div>
</div>
