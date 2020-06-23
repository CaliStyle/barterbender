<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Module_Feed
 * @version        $Id: display.html.php 4176 2012-05-16 10:49:38Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !defined('PHPFOX_IS_USER_PROFILE') || $aUser.user_id == Phpfox::getUserId() || (Phpfox::getUserParam('profile.can_post_comment_on_profile') && Phpfox::getService('user.privacy')->hasAccess('' . $aUser.user_id . '', 'feed.share_on_wall'))}
<div class="ynfeed-bg-focus"></div>
<div id="ynfeed_form_share_holder" class="ynfeed_form_share_holder <?php if(flavor()->active->id == 'material') echo 'ynfeed_form_share_material'?>">
    <div class="activity_feed_form_share">
        <div class="activity_feed_form_share_process">{img theme='ajax/add.gif' class='v_middle'}</div>
        {if !isset($bSkipShare)}
        <ul class="activity_feed_form_attach">
            <li class="share">
                <a role="button">{_p var='share'}:</a>
            </li>
            {if isset($aFeedCallback.module)}
            <li><a href="#" rel="global_attachment_status" class="global_attachment_status active">
                    <div class="ynf-feed-tab-item-text">{_p var='post'}<span class="activity_feed_link_form_ajax">{$aFeedCallback.ajax_request}</span>
                    </div>
                    <div class="drop"></div>
                </a></li>
            {elseif !isset($bFeedIsParentItem) && (!defined('PHPFOX_IS_USER_PROFILE') ||
            (defined('PHPFOX_IS_USER_PROFILE')
            && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId()))}
            <li><a href="#" rel="global_attachment_status" class="global_attachment_status active">
                    <div class="ynf-feed-tab-item-text">{_p var='make_post'}<span class="activity_feed_link_form_ajax">ynfeed.updateStatus</span></div>
                    <div class="drop"></div>
                </a></li>
            {else}
            <li><a href="#" rel="global_attachment_status" class="global_attachment_status active">
                    <div class="ynf-feed-tab-item-text">{_p var='post'}<span class="activity_feed_link_form_ajax">ynfeed.addComment</span></div>
                    <div class="drop"></div>
                </a></li>
            {/if}
            {foreach from=$aFeedStatusLinks item=aFeedStatusLink name=feedlinks}
                {if isset($aFeedCallback.module) && $aFeedStatusLink.no_profile}
                {else}
                {if ($aFeedStatusLink.no_profile && !isset($bFeedIsParentItem) &&
                (!defined('PHPFOX_IS_USER_PROFILE') ||
                (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id ==
                Phpfox::getUserId())))
                || !$aFeedStatusLink.no_profile}
                <li>
                    <a href="#" rel="global_attachment_{$aFeedStatusLink.module_id}" {if $aFeedStatusLink.no_input}
                       class="no_text_input" {/if}>
                    <span class="ynf-feed-tab-item-text">{$aFeedStatusLink.title|convert}</span>
                    <div>
                        {if $aFeedStatusLink.is_frame}
                        <span class="activity_feed_link_form">{url link='ynfeed.'$aFeedStatusLink.module_id'.frame'}</span>
                        {else}
                        <span class="activity_feed_link_form_ajax">{$aFeedStatusLink.module_id}.{$aFeedStatusLink.ajax_request}</span>
                        {/if}
                        <span class="activity_feed_extra_info">{$aFeedStatusLink.description|convert}</span>
                    </div>
                    <div class="drop"></div>
                    </a>
                </li>
                {/if}
                {/if}
            {/foreach}
        </ul>
        {/if}
        <div class="clear"></div>
        <div class="ynf-form-feed-close-btn js_ynf_form_feed_close_btn"><i class="ico ico-close"></i></div>
    </div>

    <div class="activity_feed_form ynfeed_activity_feed_form">
        <form method="post" action="#" id="js_activity_feed_form" enctype="multipart/form-data">
            <div id="js_custom_privacy_input_holder"></div>
            {if isset($aFeedCallback.module)}
            <div><input type="hidden" name="val[callback_item_id]" value="{$aFeedCallback.item_id}"/></div>
            <div><input type="hidden" name="val[callback_module]" value="{$aFeedCallback.module}"/></div>
            <div><input type="hidden" name="val[parent_user_id]" value="{$aFeedCallback.item_id}"/></div>
            {/if}
            {if isset($bFeedIsParentItem)}
            <div><input type="hidden" name="val[parent_table_change]" value="{$sFeedIsParentItemModule}"/></div>
            {/if}
            {if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id != Phpfox::getUserId()}
            <div><input type="hidden" name="val[parent_user_id]" value="{$iUserProfileId}"/></div>
            {/if}
            {if isset($bForceFormOnly) && $bForceFormOnly}
            <div><input type="hidden" name="force_form" value="1"/></div>
            {/if}
            <div class="activity_feed_form_holder">

                <div id="activity_feed_upload_error" style="display:none;">
                    <div class="error_message" id="activity_feed_upload_error_message"></div>
                </div>

                <div class="global_attachment_holder_section" id="global_attachment_status" style="display:block;">
                    <div id="global_attachment_status_value" style="display:none;">
                        <textarea name="val[user_status]" id="ynfeed_status_content" cols="30" rows="10"></textarea>
                    </div>

                    <div class="ynfeed_compose_status">
                        <div class="item-avatar">{img user=$aGlobalUser suffix='_50_square'}</div>
                        <div class="ynfeed_highlighter"></div>
                        <div class="contenteditable" placeholder="{_p('whats_on_your_mind')}"
                             data-js="{$corePath}/assets/js/fulltagger.js"></div>
                        {template file='ynfeed.block.emoticons'}
                        <div class="ynfeed_autocomplete" style="display: none;"></div>
                    </div>
                </div>

                {foreach from=$aFeedStatusLinks item=aFeedStatusLink}
                {if !empty($aFeedStatusLink.module_block)}
                {module name=$aFeedStatusLink.module_block}
                {/if}
                {/foreach}
                {if Phpfox::isModule('egift')}
                {module name='egift.display'}
                {/if}
            </div>
            <div class="activity_feed_form_button">

                <div class="activity_feed_form_button_status_info">
                    <textarea name="val[status_info]" id="ynfeed_status_info" cols="30" rows="10"></textarea>
                    <div class="ynfeed_compose_status">
                        <div class="item-avatar">{img user=$aGlobalUser suffix='_50_square'}</div>
                        <div class="ynfeed_highlighter"></div>
                        <div class="contenteditable" placeholder="{_p('whats_on_your_mind')}"
                             data-js="{$corePath}/assets/js/fulltagger.js"></div>
                        {template file='ynfeed.block.emoticons'}
                        <div class="ynfeed_autocomplete" style="display: none;"></div>
                    </div>
                </div>
                <div class="ynfeed_extra_preview" style="display: none;">
                    <span id="ynfeed_extra_preview_feeling"></span>
                    <span id="ynfeed_extra_preview_tagged"></span>
                    <span id="ynfeed_extra_preview_checkin"></span>
                    <span id="ynfeed_extra_preview_business"></span>
                </div>
                <div class="ynfeed-table-tagging">
                    <div class="ynfeed_compose_extra ynfeed_compose_tagging" style="display: none;">
                        <div class="ynfeed-box">
                            <div class="ynfeed-with">{_p('With')}</div>
                            <div class="ynfeed-tagging-input-box">
                                <input type="hidden" id="ynfeed_input_tagged" name="val[tagged]">
                                <span class="ynfeed_tagged_items"></span>
                                <span class="ynfeed_input_tagging_wrapper">
                                        <input type="text" class="ynfeed_input_tagging"
                                               placeholder="{_p('who_is_with_you')}"
                                               data-js="{$corePath}/assets/js/taguser.js">
                                </span>
                                <div class="ynfeed_autocomplete" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="ynfeed_compose_extra ynfeed_compose_business">
                        <div class="ynfeed-box">
                            <div class="ynfeed-with">{_p('At')}</div>
                            <div class="ynfeed-business-input-box">
                                <input type="hidden" id="ynfeed_input_selected_business" name="val[business]">
                                <span class="ynfeed_tagged_items"></span>
                                <span class="ynfeed_input_tagging_wrapper">
                             <input type="text" class="ynfeed_input_business" placeholder="{_p('business_name')}"
                                    data-js="{$corePath}/assets/js/business.js">
                          </span>
                                <div class="ynfeed_autocomplete" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="ynfeed_compose_extra ynfeed_compose_feeling">
                        <div class="ynfeed-box">
                            <div class="ynfeed-with">{_p('Feeling')}</div>

                            <div class="ynfeed-feeling-input-box">
                                <input type="hidden" id="ynfeed_input_selected_feeling" name="val[feeling]">
                                <input type="hidden" id="ynfeed_input_custom_feeling_text" name="val[custom_feeling_text]">
                                <input type="hidden" id="ynfeed_input_custom_feeling_image" name="val[custom_feeling_image]">
                                <span class="ynfeed_tagged_items"></span>
                                <span class="ynfeed_input_tagging_wrapper">
                           <input type="text" class="ynfeed_input_feeling"
                                  placeholder="{_p('what_do_you_feel_right_now')}"
                                  data-js="{$corePath}/assets/js/feeling.js">
                        </span>
                                <div class="ynfeed_autocomplete"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {if $bLoadCheckIn}
                <div id="js_location_input" class="ynfeed-location-box" data-js="{$corePath}/assets/js/location.js">
                    <div class="ynfeed-box">
                        <div class="ynfeed-with">{_p('At')}</div>

                        <div class="ynfeed-location-input-box">
                            <input type="text" id="hdn_location_name">
                            <a class="ynfeed_btn_delete_checkin" href="javascript:void(0)" onclick="$Core.ynfeedCheckin.cancelCheckin();" style="display: none;">
                                <i class="ico ico-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                {/if}
                <div class="activity_feed_form_button_position">
                    <div class="ynfeed-form-button-box dont-unbind-children">
                        <div class="ynfeed-form-button-box-wrapper">
                            {if !Phpfox::getUserBy('profile_page_id')}
                            <div id="activity_feed_share_this_one">
                                <a href="#" type="button" id="ynfeed_btn_tag"
                                class="activity_feed_share_this_one_link parent "
                                onclick="return false;" title="{_p var="tag_friends"}">
                                    <i class="ico ico-user1-plus-o"></i><span class="item-text">{_p('tag_friends')}</span>
                                </a>
                            </div>
                            {/if}
                            {if $bLoadCheckIn}
                            <div id="activity_feed_share_this_one">
                                {template file='ynfeed.block.checkin'}
                            </div>
                            {/if}
                            {if $bLoadBusiness}
                            <div id="activity_feed_share_this_one">
                                <a href="#" type="button" id="ynfeed_btn_business"
                                class="activity_feed_share_this_one_link parent"
                                onclick="return false;">
                                    <i class="ico ico-briefcase-o" aria-hidden="true"></i><span class="item-text">{_p('checkin_business')}</span>
                                </a>
                            </div>
                            {/if}

                            <div id="activity_feed_share_this_one">
                                <a href="#" type="button" id="ynfeed_btn_feeling"
                                class="activity_feed_share_this_one_link parent "
                                onclick="return false;" title="{_p var='feeling_activity'}">
                                    <i class="ico ico-smile-o" aria-hidden="true"></i><span class="item-text">{_p('feeling_activity')}</span>
                                </a>
                            </div>

                            <!--Page view-->
                            {if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE == 'pages' && $aPage.is_admin)}
                            <div id="activity_feed_share_this_one" class="ynfeed-page-posting-options">
                                <ul class="ynfeed-action-items">
                                    {if defined('PHPFOX_IS_PAGES_VIEW') && $aPage.is_admin && $aPage.page_id !=
                                    Phpfox::getUserBy('profile_page_id') && ($aPage.item_type == 0)}
                                    <li class="ynfeed-action-item">
                                        <div class="dropdown">
                                            <input type="hidden" name="custom_pages_post_as_page" value="{$aPage.page_id}">
                                            <a data-toggle="dropdown" role="button" class="btn btn-sm">
                                                <span class="txt-prefix">{_p var='posting_as'}: </span>
                                                <span class="txt-label">{$aPage.full_name|clean|shorten:20:'...'}</span>
                                                <i class="caret"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-checkmark">
                                                <li>
                                                    <a class="is_active_image" data-toggle="privacy_item" role="button"
                                                    rel="{$aPage.page_id}">{$aPage.full_name|clean|shorten:20:'...'}</a>
                                                </li>
                                                <li>
                                                    <a data-toggle="privacy_item" role="button" rel="0">{$sGlobalUserFullName|shorten:20:'...'}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    {/if}
                                </ul>
                            </div>
                            {/if}
                            <!--End page view-->
                            <div class="ynfeed-form-btn-activity-viewmore" onclick="$('.ynfeed-form-button-box').toggleClass('full');"><i class="ico ico-dottedmore"></i></div>
                        </div>
                    </div>
                    <div class="ynfeed-form-button-share">
                        <div class="activity_feed_form_button_position_button">
                            <button type="submit" value="{_p var='share'}"  id="activity_feed_submit" class="button btn btn-primary"><span class="ico ico-paperplane hide"></span><span>{_p var='share'}</span></button>
                        </div>
                        {if isset($aFeedCallback.module)}
                        {else}
                        {if !isset($bFeedIsParentItem) && (!defined('PHPFOX_IS_USER_PROFILE') ||
                        (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id ==
                        Phpfox::getUserId()))}
                        {module name='privacy.form' privacy_name='privacy' privacy_type='mini' btn_size='normal'}
                        {/if}
                        {/if}
                    </div>
                </div>

                {if Phpfox::getParam('feed.enable_check_in') && (Phpfox::getParam('core.ip_infodb_api_key') != '' ||
                Phpfox::getParam('core.google_api_key') != '')}
                <div id="js_add_location">
                    <div><input type="hidden" id="ynfeed_val_location_latlng" name="val[location][latlng]"></div>
                    <div><input type="hidden" id="ynfeed_val_location_name" name="val[location][name]"></div>
                </div>
                {/if}

            </div>
        </form>
        <div class="activity_feed_form_iframe"></div>
    </div>
</div>
{/if}


<script type="text/javascript">
    if(typeof $Behavior.activityFeedProcess != 'undefined')
        $Behavior.activityFeedProcess();
    if(typeof $Core.ynfeed != 'undefined')
        $Core.ynfeed.init();

</script>
