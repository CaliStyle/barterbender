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
<div class="activity_feed_form ynfeed_form_edit {if $aForms.type_id == 'photo'}photo-type{/if}">
    <span class="activity_feed_link_form_ajax">ynfeed.updatePost</span>
    <form method="post" action="#" id="js_activity_feed_form" enctype="multipart/form-data">
        <div><input type="hidden" name="val[feed_id]" value="{$iFeedId}"/></div>
        <div><input type="hidden" name="val[type_id]" value="{$aForms.type_id}"/></div>
        <div><input type="hidden" name="val[item_id]" value="{$aForms.item_id}"/></div>
        <div><input type="hidden" name="val[no_check_empty_user_status]" value="{$aForms.parent_feed_id}"/></div>
        <div id="js_custom_privacy_input_holder"></div>
        {if isset($aFeedCallback.module)}
        <div><input type="hidden" name="val[callback_item_id]" value="{$aFeedCallback.item_id}"/></div>
        <div><input type="hidden" name="val[callback_module]" value="{$aFeedCallback.module}"/></div>
        <div><input type="hidden" name="val[parent_user_id]" value="{$aFeedCallback.item_id}"/></div>
        <div><input type="hidden" name="val[table_prefix]" value="{$aFeedCallback.table_prefix}"/></div>
        <div><input type="hidden" name="val[callback_user_id]" value="{$aForms.user_id}"></div>
        {/if}
        {if isset($bFeedIsParentItem)}
        <div><input type="hidden" name="val[parent_table_change]" value="{$sFeedIsParentItemModule}"/></div>
        {/if}
        {if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id != Phpfox::getUserId()}
        <div><input type="hidden" name="val[parent_user_id]" value="{$aUser.user_id}"/></div>
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
                    <div class="contenteditable" contenteditable="true" placeholder="What's on your mind?"
                         data-js="{$corePath}/assets/js/fulltagger.js">{$aForms.feed_status|ynfeed_parse_emojis}</div>
                    {template file='ynfeed.block.emoticons'}
                    <div class="ynfeed_autocomplete" style="display: none;"></div>
                    <!--Preview link-->
                    {if $aForms.type_id == 'link'}
                    <div id="js_preview_link_attachment_custom_form_sub" class="js_preview_link_attachment_custom_form" style="margin-top:5px;">
                        <div><input type="hidden" name="val[link][url]" value="{$aForms.feed_link_actual}"></div>
                        {if !empty($aForms.custom_data_cache.image) && file_get_contents($aForms.custom_data_cache.image) !== false}
                        <div class="attachment_image">
                            <div class="attachment_image_holder">
                                <div><input type="hidden" name="val[link][image_hide]" value="0" id="js_attachment_link_default_image_hide"></div>
                                <div><input type="hidden" name="val[link][image]" value="{$aForms.custom_data_cache.image}" id="js_attachment_link_default_image_input"></div>
                                <div id="js_attachment_link_default_image">
                                    <img src="{$aForms.custom_data_cache.image}" alt="" style="max-width:120px;" />
                                </div>
                            </div>
                        </div>
                        {/if}
                        <div class="attachment_body" {if !(!empty($aForms.custom_data_cache.image) && file_get_contents($aForms.custom_data_cache.image) !== false)}style="margin-left: 0 !important;"{/if}>
                            <div>
                                <div class="js_text_attachment_edit" style="display:none;"><input type="text" name="val[link][title]" value="{$aForms.feed_title}" class="js_text_attachment_edit_value"></div>
                                <a class="attachment_body_title js_text_attachment_edit_link" href="javascript:void(0);">{$aForms.feed_title}</a>
                            </div>
                            <div class="attachment_body_link">{$aForms.feed_link_actual}</div>
                            <div class="attachment_body_description">
                                <div class="js_text_attachment_edit" style="display:none;"><textarea cols="30" rows="4" name="val[link][description]" class="js_text_attachment_edit_value">{$aForms.feed_content}</textarea></div>
                                <a class="js_text_attachment_edit_link" href="javascript:void(0);">{$aForms.feed_content}</a>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <!--End preview link-->
                    {elseif $aForms.type_id == 'photo' && !empty($editedFeedPhotos)}
                    <div id="global_attachment_photo">
                        {plugin call='photo.template_block_share_1'}
                        <div><input type="hidden" name="val[group_id]" value="{if isset($aFeedCallback.item_id)}{$aFeedCallback.item_id}{else}0{/if}" /></div>
                        <div><input type="hidden" name="val[action]" value="upload_photo_via_share"/></div>
                        {module name='core.upload-form' type='photo_feed' }
                        {plugin call='photo.template_block_share_2'}
                    </div>
                    {plugin call='photo.template_block_share_3'}
                    {/if}
                </div>
            </div>

            <!--For each aFeedStatusLinks module block-->
            {if Phpfox::isModule('egift')}
            {module name='egift.display'}
            {/if}
        </div>
        <div class="activity_feed_form_button">
            <div class="ynfeed_extra_preview" style="display:none;">
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
                            <input type="hidden" id="ynfeed_input_tagged" name="val[tagged]" value="{$aForms.tagged}">
                            <span class="ynfeed_tagged_items"></span>
                            <span class="ynfeed_input_tagging_wrapper">
                           <input type="text" class="ynfeed_input_tagging" placeholder="{_p('Who is with you?')}"
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
                            <input type="hidden" id="ynfeed_input_selected_business" name="val[business]" value="{if isset($aForms.aBusiness.business_id)}{$aForms.aBusiness.business_id}{/if}">
                            <span class="ynfeed_tagged_items"></span>
                            <span class="ynfeed_input_tagging_wrapper">
                             <input type="text" class="ynfeed_input_business" placeholder="{_p('Business name')}"
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
                            <input type="hidden" id="ynfeed_input_selected_feeling" name="val[feeling]" value="{if isset($aForms.aFeeling.feeling_id)}{$aForms.aFeeling.feeling_id}{/if}">
                            <input type="hidden" id="ynfeed_input_custom_feeling_text" name="val[custom_feeling_text]" value="{if isset($aForms.aFeeling.feeling_id) && $aForms.aFeeling.feeling_id == -1}{$aForms.aFeeling.title_translated}{/if}">
                            <input type="hidden" id="ynfeed_input_custom_feeling_image" name="val[custom_feeling_image]" value="{if isset($aForms.aFeeling.feeling_id) && $aForms.aFeeling.feeling_id == -1}{$aForms.aFeeling.image}{/if}">
                            <span class="ynfeed_tagged_items"></span>
                            <span class="ynfeed_input_tagging_wrapper">
                           <input type="text" class="ynfeed_input_feeling"
                                  placeholder="{_p('What do you feel right now?')}"
                                  data-js="{$corePath}/assets/js/feeling.js">
                        </span>
                            <div class="ynfeed_autocomplete" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
            {if $bLoadCheckIn}
            <div id="js_location_input" class="ynfeed-location-box" data-js="{$corePath}/assets/js/location.js"
                 data-lat="{if isset($aForms.location_latlng.latitude)}{$aForms.location_latlng.latitude}{/if}"
                 data-lng="{if isset($aForms.location_latlng.longitude)}{$aForms.location_latlng.longitude}{/if}">
                <div class="ynfeed-box">
                    <div class="ynfeed-with">{_p('At')}</div>
                    <div class="ynfeed-location-input-box">
                        <input type="text" id="hdn_location_name" value="{if isset($aForms.location_name)}{$aForms.location_name}{/if}">
                        <a class="" href="javascript:void(0)" onclick="$Core.ynfeedCheckin.cancelCheckin();">
                            <i class="ico ico-close"></i>
                        </a>
                    </div>
                </div>
            </div>
            {/if}
            <div class="activity_feed_form_button_position">
                <div class="ynfeed-form-button-box">
                    <div class="ynfeed-form-button-box-wrapper">
                        {if !Phpfox::getUserBy('profile_page_id')}
                         <div id="activity_feed_share_this_one">
                            <a href="#" type="button" id="ynfeed_btn_tag"
                            class="activity_feed_share_this_one_link parent "
                            onclick="return false;" title="{_p('tag_friends')}">
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
                    {if (defined('PHPFOX_IS_PAGES_VIEW') && $aPage.is_admin)}
                    <div id="activity_feed_share_this_one" class="ynfeed-page-posting-options">
                        <ul class="ynfeed-action-items">
                            {if defined('PHPFOX_IS_PAGES_VIEW') && $aPage.is_admin && $aPage.page_id !=
                            Phpfox::getUserBy('profile_page_id') && ($aPage.item_type == 0)}
                            <li class="ynfeed-action-item">
                                <input type="hidden" name="custom_pages_post_as_page" value="{$aPage.page_id}">
                                <div class="dropdown">
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
                </div>
                    <!--End page view-->
                </div>

                <div class="ynfeed-form-button-share">
                <div class="activity_feed_form_button_position_button">
                    <button type="submit" value="{_p var='share'}"  id="activity_feed_submit" class="button btn btn-sm btn-primary"><span class="ico ico-paperplane hide"></span><span>{_p var='Update'}</span></button>
                </div>
                {if isset($aFeedCallback.module)}
                {else}
                    {if !isset($bFeedIsParentItem) && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId() && empty($mOnOtherUserProfile))) && $aForms.type_id != 'feed_comment'}
                        {module name='privacy.form' privacy_name='privacy' privacy_type='mini' btn_size='normal'}
                    {/if}
                {/if}
                </div>
            </div>

            {if Phpfox::getParam('feed.enable_check_in') && (Phpfox::getParam('core.ip_infodb_api_key') != '' ||
            Phpfox::getParam('core.google_api_key') != '')}
            <div id="js_add_location">
                <div><input type="hidden" id="ynfeed_val_location_latlng" name="val[location][latlng]" value="{if isset($aForms.location_latlng.latitude)}{$aForms.location_latlng.latitude}{/if},{if isset($aForms.location_latlng.latitude)}{$aForms.location_latlng.longitude}{/if}"></div>
                <div><input type="hidden" id="ynfeed_val_location_name" name="val[location][name]" value="{if isset($aForms.location_name)}{$aForms.location_name}{/if}"></div>
            </div>
            {/if}

        </div>
    </form>
    <div class="activity_feed_form_iframe"></div>
</div>
{/if}

<script type="text/javascript">
    {if $aForms.type_id == 'photo'}
    setTimeout(function(){l}
        $Core.Photo.processUploadImageForAdvFeed.initEditPhotoStatus();
    {r},100);
    {/if}
    $Core.loadInit();
    $Core.ynfeed.finishLoadEditForm($('.ynfeed_form_edit').closest('.js_box').prop('id'));
</script>
