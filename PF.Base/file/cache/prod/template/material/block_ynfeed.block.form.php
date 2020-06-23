<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Module_Feed
 * @version        $Id: display.html.php 4176 2012-05-16 10:49:38Z Raymond_Benc $
 */



 if (! defined ( 'PHPFOX_IS_USER_PROFILE' ) || $this->_aVars['aUser']['user_id'] == Phpfox ::getUserId() || ( Phpfox ::getUserParam('profile.can_post_comment_on_profile') && Phpfox ::getService('user.privacy')->hasAccess('' . $this->_aVars['aUser']['user_id'] . '' , 'feed.share_on_wall' ) )): ?>
<div class="ynfeed-bg-focus"></div>
<div id="ynfeed_form_share_holder" class="ynfeed_form_share_holder <?php if(flavor()->active->id == 'material') echo 'ynfeed_form_share_material'?>">
    <div class="activity_feed_form_share">
        <div class="activity_feed_form_share_process"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif','class' => 'v_middle')); ?></div>
<?php if (! isset ( $this->_aVars['bSkipShare'] )): ?>
        <ul class="activity_feed_form_attach">
            <li class="share">
                <a role="button"><?php echo _p('share'); ?>:</a>
            </li>
<?php if (isset ( $this->_aVars['aFeedCallback']['module'] )): ?>
            <li><a href="#" rel="global_attachment_status" class="global_attachment_status active">
                    <div class="ynf-feed-tab-item-text"><?php echo _p('post'); ?><span class="activity_feed_link_form_ajax"><?php echo $this->_aVars['aFeedCallback']['ajax_request']; ?></span>
                    </div>
                    <div class="drop"></div>
                </a></li>
<?php elseif (! isset ( $this->_aVars['bFeedIsParentItem'] ) && ( ! defined ( 'PHPFOX_IS_USER_PROFILE' ) || ( defined ( 'PHPFOX_IS_USER_PROFILE' ) && isset ( $this->_aVars['aUser']['user_id'] ) && $this->_aVars['aUser']['user_id'] == Phpfox ::getUserId()))): ?>
            <li><a href="#" rel="global_attachment_status" class="global_attachment_status active">
                    <div class="ynf-feed-tab-item-text"><?php echo _p('make_post'); ?><span class="activity_feed_link_form_ajax">ynfeed.updateStatus</span></div>
                    <div class="drop"></div>
                </a></li>
<?php else: ?>
            <li><a href="#" rel="global_attachment_status" class="global_attachment_status active">
                    <div class="ynf-feed-tab-item-text"><?php echo _p('post'); ?><span class="activity_feed_link_form_ajax">ynfeed.addComment</span></div>
                    <div class="drop"></div>
                </a></li>
<?php endif; ?>
<?php if (count((array)$this->_aVars['aFeedStatusLinks'])):  $this->_aPhpfoxVars['iteration']['feedlinks'] = 0;  foreach ((array) $this->_aVars['aFeedStatusLinks'] as $this->_aVars['aFeedStatusLink']):  $this->_aPhpfoxVars['iteration']['feedlinks']++; ?>

<?php if (isset ( $this->_aVars['aFeedCallback']['module'] ) && $this->_aVars['aFeedStatusLink']['no_profile']): ?>
<?php else: ?>
<?php if (( $this->_aVars['aFeedStatusLink']['no_profile'] && ! isset ( $this->_aVars['bFeedIsParentItem'] ) && ( ! defined ( 'PHPFOX_IS_USER_PROFILE' ) || ( defined ( 'PHPFOX_IS_USER_PROFILE' ) && isset ( $this->_aVars['aUser']['user_id'] ) && $this->_aVars['aUser']['user_id'] == Phpfox ::getUserId()))) || ! $this->_aVars['aFeedStatusLink']['no_profile']): ?>
                <li>
                    <a href="#" rel="global_attachment_<?php echo $this->_aVars['aFeedStatusLink']['module_id']; ?>" <?php if ($this->_aVars['aFeedStatusLink']['no_input']): ?>
                       class="no_text_input" <?php endif; ?>>
                    <span class="ynf-feed-tab-item-text"><?php echo Phpfox::getLib('locale')->convert($this->_aVars['aFeedStatusLink']['title']); ?></span>
                    <div>
<?php if ($this->_aVars['aFeedStatusLink']['is_frame']): ?>
                        <span class="activity_feed_link_form"><?php echo Phpfox::getLib('phpfox.url')->makeUrl('ynfeed.'.$this->_aVars['aFeedStatusLink']['module_id'].'.frame'); ?></span>
<?php else: ?>
                        <span class="activity_feed_link_form_ajax"><?php echo $this->_aVars['aFeedStatusLink']['module_id']; ?>.<?php echo $this->_aVars['aFeedStatusLink']['ajax_request']; ?></span>
<?php endif; ?>
                        <span class="activity_feed_extra_info"><?php echo Phpfox::getLib('locale')->convert($this->_aVars['aFeedStatusLink']['description']); ?></span>
                    </div>
                    <div class="drop"></div>
                    </a>
                </li>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; endif; ?>
        </ul>
<?php endif; ?>
        <div class="clear"></div>
        <div class="ynf-form-feed-close-btn js_ynf_form_feed_close_btn"><i class="ico ico-close"></i></div>
    </div>

    <div class="activity_feed_form ynfeed_activity_feed_form">
        <form method="post" action="#" id="js_activity_feed_form" enctype="multipart/form-data">
            <div id="js_custom_privacy_input_holder"></div>
<?php if (isset ( $this->_aVars['aFeedCallback']['module'] )): ?>
            <div><input type="hidden" name="val[callback_item_id]" value="<?php echo $this->_aVars['aFeedCallback']['item_id']; ?>"/></div>
            <div><input type="hidden" name="val[callback_module]" value="<?php echo $this->_aVars['aFeedCallback']['module']; ?>"/></div>
            <div><input type="hidden" name="val[parent_user_id]" value="<?php echo $this->_aVars['aFeedCallback']['item_id']; ?>"/></div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['bFeedIsParentItem'] )): ?>
            <div><input type="hidden" name="val[parent_table_change]" value="<?php echo $this->_aVars['sFeedIsParentItemModule']; ?>"/></div>
<?php endif; ?>
<?php if (defined ( 'PHPFOX_IS_USER_PROFILE' ) && isset ( $this->_aVars['aUser']['user_id'] ) && $this->_aVars['aUser']['user_id'] != Phpfox ::getUserId()): ?>
            <div><input type="hidden" name="val[parent_user_id]" value="<?php echo $this->_aVars['iUserProfileId']; ?>"/></div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['bForceFormOnly'] ) && $this->_aVars['bForceFormOnly']): ?>
            <div><input type="hidden" name="force_form" value="1"/></div>
<?php endif; ?>
            <div class="activity_feed_form_holder">

                <div id="activity_feed_upload_error" style="display:none;">
                    <div class="error_message" id="activity_feed_upload_error_message"></div>
                </div>

                <div class="global_attachment_holder_section" id="global_attachment_status" style="display:block;">
                    <div id="global_attachment_status_value" style="display:none;">
                        <textarea name="val[user_status]" id="ynfeed_status_content" cols="30" rows="10"></textarea>
                    </div>

                    <div class="ynfeed_compose_status">
                        <div class="item-avatar"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aGlobalUser'],'suffix' => '_50_square')); ?></div>
                        <div class="ynfeed_highlighter"></div>
                        <div class="contenteditable" placeholder="<?php echo _p('whats_on_your_mind'); ?>"
                             data-js="<?php echo $this->_aVars['corePath']; ?>/assets/js/fulltagger.js"></div>
                        <?php
						Phpfox::getLib('template')->getBuiltFile('ynfeed.block.emoticons');
						?>
                        <div class="ynfeed_autocomplete" style="display: none;"></div>
                    </div>
                </div>

<?php if (count((array)$this->_aVars['aFeedStatusLinks'])):  foreach ((array) $this->_aVars['aFeedStatusLinks'] as $this->_aVars['aFeedStatusLink']): ?>
<?php if (! empty ( $this->_aVars['aFeedStatusLink']['module_block'] )): ?>
<?php Phpfox::getBlock($this->_aVars['aFeedStatusLink']['module_block'], array()); ?>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php if (Phpfox ::isModule('egift')): ?>
<?php Phpfox::getBlock('egift.display', array()); ?>
<?php endif; ?>
            </div>
            <div class="activity_feed_form_button">

                <div class="activity_feed_form_button_status_info">
                    <textarea name="val[status_info]" id="ynfeed_status_info" cols="30" rows="10"></textarea>
                    <div class="ynfeed_compose_status">
                        <div class="item-avatar"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aGlobalUser'],'suffix' => '_50_square')); ?></div>
                        <div class="ynfeed_highlighter"></div>
                        <div class="contenteditable" placeholder="<?php echo _p('whats_on_your_mind'); ?>"
                             data-js="<?php echo $this->_aVars['corePath']; ?>/assets/js/fulltagger.js"></div>
                        <?php
						Phpfox::getLib('template')->getBuiltFile('ynfeed.block.emoticons');
						?>
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
                            <div class="ynfeed-with"><?php echo _p('With'); ?></div>
                            <div class="ynfeed-tagging-input-box">
                                <input type="hidden" id="ynfeed_input_tagged" name="val[tagged]">
                                <span class="ynfeed_tagged_items"></span>
                                <span class="ynfeed_input_tagging_wrapper">
                                        <input type="text" class="ynfeed_input_tagging"
                                               placeholder="<?php echo _p('who_is_with_you'); ?>"
                                               data-js="<?php echo $this->_aVars['corePath']; ?>/assets/js/taguser.js">
                                </span>
                                <div class="ynfeed_autocomplete" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="ynfeed_compose_extra ynfeed_compose_business">
                        <div class="ynfeed-box">
                            <div class="ynfeed-with"><?php echo _p('At'); ?></div>
                            <div class="ynfeed-business-input-box">
                                <input type="hidden" id="ynfeed_input_selected_business" name="val[business]">
                                <span class="ynfeed_tagged_items"></span>
                                <span class="ynfeed_input_tagging_wrapper">
                             <input type="text" class="ynfeed_input_business" placeholder="<?php echo _p('business_name'); ?>"
                                    data-js="<?php echo $this->_aVars['corePath']; ?>/assets/js/business.js">
                          </span>
                                <div class="ynfeed_autocomplete" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="ynfeed_compose_extra ynfeed_compose_feeling">
                        <div class="ynfeed-box">
                            <div class="ynfeed-with"><?php echo _p('Feeling'); ?></div>

                            <div class="ynfeed-feeling-input-box">
                                <input type="hidden" id="ynfeed_input_selected_feeling" name="val[feeling]">
                                <input type="hidden" id="ynfeed_input_custom_feeling_text" name="val[custom_feeling_text]">
                                <input type="hidden" id="ynfeed_input_custom_feeling_image" name="val[custom_feeling_image]">
                                <span class="ynfeed_tagged_items"></span>
                                <span class="ynfeed_input_tagging_wrapper">
                           <input type="text" class="ynfeed_input_feeling"
                                  placeholder="<?php echo _p('what_do_you_feel_right_now'); ?>"
                                  data-js="<?php echo $this->_aVars['corePath']; ?>/assets/js/feeling.js">
                        </span>
                                <div class="ynfeed_autocomplete"></div>
                            </div>
                        </div>
                    </div>
                </div>
<?php if ($this->_aVars['bLoadCheckIn']): ?>
                <div id="js_location_input" class="ynfeed-location-box" data-js="<?php echo $this->_aVars['corePath']; ?>/assets/js/location.js">
                    <div class="ynfeed-box">
                        <div class="ynfeed-with"><?php echo _p('At'); ?></div>

                        <div class="ynfeed-location-input-box">
                            <input type="text" id="hdn_location_name">
                            <a class="ynfeed_btn_delete_checkin" href="javascript:void(0)" onclick="$Core.ynfeedCheckin.cancelCheckin();" style="display: none;">
                                <i class="ico ico-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
<?php endif; ?>
                <div class="activity_feed_form_button_position">
                    <div class="ynfeed-form-button-box dont-unbind-children">
                        <div class="ynfeed-form-button-box-wrapper">
<?php if (! Phpfox ::getUserBy('profile_page_id')): ?>
                            <div id="activity_feed_share_this_one">
                                <a href="#" type="button" id="ynfeed_btn_tag"
                                class="activity_feed_share_this_one_link parent "
                                onclick="return false;" title="<?php echo _p("tag_friends"); ?>">
                                    <i class="ico ico-user1-plus-o"></i><span class="item-text"><?php echo _p('tag_friends'); ?></span>
                                </a>
                            </div>
<?php endif; ?>
<?php if ($this->_aVars['bLoadCheckIn']): ?>
                            <div id="activity_feed_share_this_one">
                                <?php
						Phpfox::getLib('template')->getBuiltFile('ynfeed.block.checkin');
						?>
                            </div>
<?php endif; ?>
<?php if ($this->_aVars['bLoadBusiness']): ?>
                            <div id="activity_feed_share_this_one">
                                <a href="#" type="button" id="ynfeed_btn_business"
                                class="activity_feed_share_this_one_link parent"
                                onclick="return false;">
                                    <i class="ico ico-briefcase-o" aria-hidden="true"></i><span class="item-text"><?php echo _p('checkin_business'); ?></span>
                                </a>
                            </div>
<?php endif; ?>

                            <div id="activity_feed_share_this_one">
                                <a href="#" type="button" id="ynfeed_btn_feeling"
                                class="activity_feed_share_this_one_link parent "
                                onclick="return false;" title="<?php echo _p('feeling_activity'); ?>">
                                    <i class="ico ico-smile-o" aria-hidden="true"></i><span class="item-text"><?php echo _p('feeling_activity'); ?></span>
                                </a>
                            </div>

                            <!--Page view-->
<?php if (( defined ( 'PHPFOX_IS_PAGES_VIEW' ) && defined ( 'PHPFOX_PAGES_ITEM_TYPE' ) && PHPFOX_PAGES_ITEM_TYPE == 'pages' && $this->_aVars['aPage']['is_admin'] )): ?>
                            <div id="activity_feed_share_this_one" class="ynfeed-page-posting-options">
                                <ul class="ynfeed-action-items">
<?php if (defined ( 'PHPFOX_IS_PAGES_VIEW' ) && $this->_aVars['aPage']['is_admin'] && $this->_aVars['aPage']['page_id'] != Phpfox ::getUserBy('profile_page_id') && ( $this->_aVars['aPage']['item_type'] == 0 )): ?>
                                    <li class="ynfeed-action-item">
                                        <div class="dropdown">
                                            <input type="hidden" name="custom_pages_post_as_page" value="<?php echo $this->_aVars['aPage']['page_id']; ?>">
                                            <a data-toggle="dropdown" role="button" class="btn btn-sm">
                                                <span class="txt-prefix"><?php echo _p('posting_as'); ?>: </span>
                                                <span class="txt-label"><?php echo Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aPage']['full_name']), 20, '...'); ?></span>
                                                <i class="caret"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-checkmark">
                                                <li>
                                                    <a class="is_active_image" data-toggle="privacy_item" role="button"
                                                    rel="<?php echo $this->_aVars['aPage']['page_id']; ?>"><?php echo Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aPage']['full_name']), 20, '...'); ?></a>
                                                </li>
                                                <li>
                                                    <a data-toggle="privacy_item" role="button" rel="0"><?php echo Phpfox::getLib('phpfox.parse.output')->shorten($this->_aVars['sGlobalUserFullName'], 20, '...'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
<?php endif; ?>
                                </ul>
                            </div>
<?php endif; ?>
                            <!--End page view-->
                            <div class="ynfeed-form-btn-activity-viewmore" onclick="$('.ynfeed-form-button-box').toggleClass('full');"><i class="ico ico-dottedmore"></i></div>
                        </div>
                    </div>
                    <div class="ynfeed-form-button-share">
                        <div class="activity_feed_form_button_position_button">
                            <button type="submit" value="<?php echo _p('share'); ?>"  id="activity_feed_submit" class="button btn btn-primary"><span class="ico ico-paperplane hide"></span><span><?php echo _p('share'); ?></span></button>
                        </div>
<?php if (isset ( $this->_aVars['aFeedCallback']['module'] )): ?>
<?php else: ?>
<?php if (! isset ( $this->_aVars['bFeedIsParentItem'] ) && ( ! defined ( 'PHPFOX_IS_USER_PROFILE' ) || ( defined ( 'PHPFOX_IS_USER_PROFILE' ) && isset ( $this->_aVars['aUser']['user_id'] ) && $this->_aVars['aUser']['user_id'] == Phpfox ::getUserId()))): ?>
<?php Phpfox::getBlock('privacy.form', array('privacy_name' => 'privacy','privacy_type' => 'mini','btn_size' => 'normal')); ?>
<?php endif; ?>
<?php endif; ?>
                    </div>
                </div>

<?php if (Phpfox ::getParam('feed.enable_check_in') && ( Phpfox ::getParam('core.ip_infodb_api_key') != '' || Phpfox ::getParam('core.google_api_key') != '' )): ?>
                <div id="js_add_location">
                    <div><input type="hidden" id="ynfeed_val_location_latlng" name="val[location][latlng]"></div>
                    <div><input type="hidden" id="ynfeed_val_location_name" name="val[location][name]"></div>
                </div>
<?php endif; ?>

            </div>
        
</form>

        <div class="activity_feed_form_iframe"></div>
    </div>
</div>
<?php endif; ?>


<script type="text/javascript">
    if(typeof $Behavior.activityFeedProcess != 'undefined')
        $Behavior.activityFeedProcess();
    if(typeof $Core.ynfeed != 'undefined')
        $Core.ynfeed.init();

</script>

