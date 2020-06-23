<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if Phpfox::isUser()}
<nav class="pull-right ynrespassion-sticky-bar-sm-has-search" >
    <div id="search-panel" class="ynrespassion-search-sm visible-sm ">
        <div class="js_temp_friend_search_form"></div>
        <form method="get" action="{url link='search'}" class="header_search_form" id="header_search_form_sm">
            <div class="input-group has-feedback">
                <input type="text" name="q" placeholder="{_p var='Search...'}" autocomplete="off" class="form-control js_temp_friend_search_input in_focus" id="header_sub_menu_search_input" />
                <span class="input-group-btn" aria-hidden="true">
                    <button class="btn btn-default" type="submit">
                         <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>
    </div>
    <ul class="list-inline header-right-menu">
        {if Phpfox::getUserBy('profile_page_id') > 0}
        {else}
        <li class="pl-5" id="hd-notification">
            <a role="button"
               class="btn-abr"
               data-panel="#notification-panel-body-sm"
               data-toggle="dropdown"
               data-url="{url link='notification.panel'}">
                <i class="fa fa-globe"></i>
                <span id="js_total_new_notifications"></span>
            </a>
            <div class="dropdown-panel">
                <div class="dropdown-panel-body" id="notification-panel-body-sm"></div>
            </div>
        </li>
        <li class="pl-5" id="hd-request">
            <a role="button"
               data-toggle="dropdown"
               class="btn-abr"
               data-panel="#request-panel-body-sm"
               data-url="{url link='friend.panel'}">
                <i class="fa fa-user-plus"></i>
                <span id="js_total_new_friend_requests"></span>
            </a>
            <div class="dropdown-panel">
                <div class="dropdown-panel-body" id="request-panel-body-sm"></div>
            </div>
        </li>
        <li class="pl-5" id="hd-message">
            <a role="button"
               class="btn-abr"
               data-toggle="dropdown"
               data-panel="#message-panel-body-sm"
               data-url="{url link='mail.panel'}">
                <i class="fa fa-comments"></i>
                <span id="js_total_new_messages"></span>
            </a>
            <div class="dropdown-panel">
                <div class="dropdown-panel-body" id="message-panel-body-sm"></div>
            </div>
        </li>
        {/if}
        <li class="pl-0" id="hd-cof">
            <a href="#"
               class="btn-abr"
               data-toggle="dropdown"
               type="button"
               aria-haspopup="true"
               aria-expanded="false">
                <i class="fa fa-angle-down"></i>
            </a>
            {if Phpfox::getUserBy('profile_page_id') > 0}
            <ul class="dropdown-menu dropdown-menu-right dont-unbind">
                <li class="header_menu_user_link_page">
                    <a href="#" onclick="$.ajaxCall('pages.logBackIn'); return false;">
                        <i class="fa fa-reply" aria-hidden="true"></i>
                        {_p var='log_back_in_as_global_full_name'
                        global_full_name=$aGlobalProfilePageLogin.full_name|clean}
                    </a>
                </li>
                <li>
                    <a href="{url link='pages.add' id=$iGlobalProfilePageId}">
                        <i class="fa fa-cog"></i>
                        {_p var='edit_page'}
                    </a>
                </li>
            </ul>
            {else}
            <ul class="dropdown-menu dropdown-menu-right dont-unbind">
                {if Phpfox::isModule('pages') && Phpfox::getUserParam('pages.can_add_new_pages')}
                <li>
                    <a href="#" onclick="$Core.box('pages.login', 400); return false;">
                        <i class="fa fa-flag"></i>
                        {_p var='login_as_page'}
                    </a>
                </li>
                {/if}
                <li role="presentation">
                    <a href="{url link='user.setting'}" class="no_ajax">
                        <i class="fa fa-cog"></i>
                        {_p var='account_settings'}
                    </a>
                </li>
                <li role="presentation">
                    <a href="{url link='user.profile'}" class="no_ajax">
                        <i class="fa fa-edit"></i>
                        {_p var='edit_profile'}
                    </a>
                </li>
                <li role="presentation">
                    <a href="{url link='friend'}" class="no_ajax">
                        <i class="fa fa-group"></i>
                        {_p var='manage_friends'}
                    </a>
                </li>
                <li role="presentation">
                    <a href="{url link='user.privacy'}" class="no_ajax">
                        <i class="fa fa-shield"></i>
                        {_p var='privacy_settings'}
                    </a>
                </li>
                {plugin call='core.template-notification-custom'}
                {if Phpfox::isAdmin() }
                <li class="divider"></li>
                <li role="presentation">
                    <a href="{url link='admincp'}" class="no_ajax">
                        <i class="fa fa-diamond"></i>
                        {_p var='menu_admincp'}
                    </a>
                </li>
                {/if}
                {plugin call='core.template_block_notification_dropdown_menu'}
                <li class="divider"></li>
                <li role="presentation">
                    <a href="{url link='user.logout'}" class="no_ajax logout">
                        <i class="fa fa-toggle-off"></i>
                        {_p var='logout'}
                    </a>
                </li>
            </ul>
            {/if}
        </li>
        <li class="pl-5" id="hd-user">
            {img user=$aGlobalUser suffix='_50_square'}
        </li>
    </ul>
</nav>
{else}
<div id="search-panel" class="ynrespassion-search-sm visible-sm ">
        <div class="js_temp_friend_search_form"></div>
        <form method="get" action="{url link='search'}" class="header_search_form" id="header_search_form_sm">
            <div class="input-group has-feedback">
                <input type="text" name="q" placeholder="{_p var='Search...'}" autocomplete="off" class="form-control js_temp_friend_search_input in_focus" id="header_sub_menu_search_input" />
                <span class="input-group-btn" aria-hidden="true">
                    <button class="btn btn-default" type="submit">
                         <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>
    </div>
<div class="guest_login_small pull-right">
    <a class="btn btn01 btn-primary text-uppercase {if Phpfox::canOpenPopup('login')}popup{else}no_ajax{/if}" rel="hide_box_title" role="link" href="{url link='login'}">
        <i class="fa fa-sign-in"></i> {_p var='sign in'}
    </a>
    {if Phpfox::getParam('user.allow_user_registration') && !Phpfox::getParam('user.invite_only_community')}
    <a class="btn btn02 btn-info text-uppercase {if Phpfox::canOpenPopup('login')}popup{else}no_ajax{/if}" rel="hide_box_title" role="link" href="{url link='user.register'}">
        {_p var='sign up'}
    </a>
    {/if}
</div>
{/if}
