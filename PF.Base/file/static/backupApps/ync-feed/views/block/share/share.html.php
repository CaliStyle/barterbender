<?php
/**
 * [PHPFOX_HEADER]
 *
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="ynfeed_share_box" data-bound="0" class="dont-unbind-children activity_feed_form">
    <form id="js_ynfeed_share_form" class="form" method="post" action="#">
        <div><input type="hidden" name="val[parent_feed_id]" value="{$iFeedId}"></div>
        <div><input type="hidden" name="val[parent_module_id]" value="{$sShareModule|clean}"></div>
        <div class="ynfeed-popup-share-feed-title">
            <div class="post-type-dropdown">
                <input type="hidden" id="post_type" name="val[post_type]" value="1">
                <a data-toggle="dropdown" class="" aria-expanded="false">
                    <span class="txt-label"><i class="ico ico-compose mr-1"></i>{_p var='share_on_your_feed'}</span>
                    <span class="txt-label js_hover_info"><i class="ico ico-user3-two mr-1"></i>{_p var='share_on_your_feed'}</span>
                    <i class="fa fa-caret-down ml-1"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-checkmark">
                    <li role="presentation">
                        <a href="javascript:void(0)" class="is_active_image" data-toggle="share_item" rel="1"><i
                                    class="ico ico-compose mr-1"></i>{_p var='share_on_your_feed'}</a>
                    </li>
                    <li role="presentation">
                        <a href="javascript:void(0)" data-toggle="share_item" rel="2"><i
                                    class="ico ico-user3-two mr-1"></i>{_p var='share_on_a_friend_timeline'}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="" id="js_feed_share_friend_holder" style="display:none;">
            <div class="ynfeed-share-friend-wrapper">
                <span class="ynfeed-share-title-search">{_p var='friend'}</span>
                {module name='friend.search-small' input_name='val[friends]'}
            </div>
        </div>

        <div class="activity_feed_form_holder">
            <div id="global_attachment_status_value" style="display:none;">
                <textarea name="val[post_content]" id="ynfeed_status_content" cols="30" rows="10"></textarea>
            </div>
            <div class="ynfeed_compose_status">
                <div class="ynfeed_highlighter"></div>
                <div class="contenteditable" placeholder="{_p('say_something_about_this')}"
                     data-js="{$corePath}/assets/js/fulltagger.js"></div>
                {template file='ynfeed.block.emoticons'}
                <div class="ynfeed_autocomplete" style="display: none;"></div>
            </div>
        </div>

        <div class="ynfeed_extra_preview" style="display:none;">
            <span id="ynfeed_extra_preview_feeling"></span>
            <span id="ynfeed_extra_preview_tagged"></span>
            <span id="ynfeed_extra_preview_checkin"></span>
            <span id="ynfeed_extra_preview_business"></span>
        </div>

        <div class="_app_{$sShareModule} row_feed_loop js_parent_feed_entry js_user_feed yncfeed-feed-item" style="z-index: 0;">
            {module name='ynfeed.share.preview' parent_feed_id=$aFeed.parent_feed_id parent_module_id=$aFeed.parent_module_id}
            <div class="ynfeed-btn-collapse-content js-ynfeed-btn-collapse-popup" style="display: none;"
                 onclick="$('#ynfeed_share_box').toggleClass('full-content');"><span
                        class="item-show-all">{_p var='show_all'}</span><span
                        class="item-collapse">{_p var='collapse'}</span></div>
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
                        <div class="ynfeed_autocomplete" style="display: none;"></div>
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
                        <a class="ynfeed_btn_delete_checkin" href="javascript:void(0)"
                           onclick="$Core.ynfeedCheckin.cancelCheckin();" style="display: none;">
                            <i class="ico ico-close"></i>
                        </a>
                    </div>
                </div>
            </div>
        {/if}

        {if Phpfox::getParam('feed.enable_check_in') && (Phpfox::getParam('core.ip_infodb_api_key') != '' ||
        Phpfox::getParam('core.google_api_key') != '')}
            <div id="js_add_location">
                <div><input type="hidden" id="ynfeed_val_location_latlng" name="val[location][latlng]"></div>
                <div><input type="hidden" id="ynfeed_val_location_name" name="val[location][name]"></div>
            </div>
        {/if}


        <div class="ynfeed-popup-share-feed-bottom">
            <div class="feed-share-bottom-icon-group">
                <div class="feed-icon">
                    <a id="ynfeed_btn_tag" href="javascript:void(0)" type="button" class="activity_feed_share_this_one_link" title="{_p('tag_friends')}">
                        <i class="ico ico-user1-plus-o"></i>
                    </a>
                </div>
                
                {if 1==0}
                    <div class="feed-icon">
                        <a id="btn_ynfeed_display_check_in" href="javascript:void(0)" type="button" class="activity_feed_share_this_one_link" title="{_p var='check_in'}">
                            <i class="ico ico-checkin-o"></i>
                        </a>
                    </div>
                {/if}

                <div class="feed-icon">
                    <a id="ynfeed_btn_feeling" href="javascript:void(0)" type="button" class="activity_feed_share_this_one_link" {_p var='feeling_activity'}>
                        <i class="ico ico-smile-o"></i>
                    </a>
                </div>
            </div>
            <div class="feed-share-bottom-btn-group">
                {module name='privacy.form' privacy_name='privacy' privacy_type='mini'}

                <a id="ynfeed_close_btn" href="javascript:void(0)" type="button" class="btn btn-default btn-sm">
                    {_p var='cancel'}
                </a>

                <input type="submit" value="{_p var='post'}" class="btn btn-primary btn-sm btn-submit">
            </div>
        </div>

    </form>

</div>
<script type="text/javascript">
    $Core.loadInit();
</script>