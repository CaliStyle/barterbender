<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 21, 2020, 11:27 pm */ ?>
<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */

?>

<style type="text/css">
    .yn_profilepopup_cover.type-user img {
        position: relative;
        left:0;
        top: <?php echo $this->_aVars['coverPhotoPosition']; ?>px;
    }
</style>

<div class="no-popup uiContextualDialogContent">
    <div class="yn_profilepopup_hovercard_stage" <?php if (! isset ( $this->_aVars['aCoverPhoto'] )): ?> style="padding-top: 10px;" <?php endif; ?>>
        <!-- user not found -->
<?php if (isset ( $this->_aVars['iIsUser'] ) && $this->_aVars['iIsUser'] == 0): ?>
        <div class="yn_profilepopup_hovercard_content">
            <div>
                <div class="yn_profilepopup_info yn_profilepopup_info_left">
<?php echo _p('profilepopup.user_not_found'); ?>.
                </div>
            </div>
        </div>
<?php endif; ?>

        <!-- profile is private -->
<?php if (isset ( $this->_aVars['iIsCanViewProfile'] ) && $this->_aVars['iIsCanViewProfile'] == 0): ?>
        <div class="yn_profilepopup_hovercard_content">
<?php if (isset ( $this->_aVars['aCoverPhoto'] )): ?>
              <div class="yn_profilepopup_cover type-user">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aCoverPhoto']['server_id'],'path' => 'photo.url_photo','file' => $this->_aVars['aCoverPhoto']['destination'],'suffix' => '_500')); ?>
                  <div class="yn_profilepopup_backgroundcover"></div>
              </div>
<?php else: ?>
              <div class="yn-profilepopup-nocover"></div>
<?php endif; ?>
            <div class="yn-profilepopup_basic_info" <?php if (! Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>style="margin-left: 10px;"<?php endif; ?>>
<?php if (Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>
                <div class="yn_profilepopup_image">
<?php if (! $this->_aVars['isFBUser']): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aUser'],'suffix' => '_200_square','max_width' => 100,'max_height' => 100)); ?>
<?php else: ?>
<?php echo $this->_aVars['sUserProfileImage']; ?>
<?php endif; ?>
                </div>
<?php endif; ?>
                <div class="yn_profilepopup_main_title <?php if (! Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>yn_profilepopup_title_nophoto<?php endif; ?> "><?php echo Phpfox::getLib('phpfox.parse.output')->split('<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aUser']['user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aUser']['user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aUser']['user_name'], ((empty($this->_aVars['aUser']['user_name']) && isset($this->_aVars['aUser']['profile_page_id'])) ? $this->_aVars['aUser']['profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aUser']['user_id'], $this->_aVars['aUser']['full_name'])), 30, '...') . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aUser']['user_id']) ? '' : '</a>') . '</span>', 20); ?></div>
            </div>
            <div class="yn_profilepopup_main" <?php if (! Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>style="margin-left: 10px;"<?php endif; ?>>
                <div class="yn_profilepopup_info yn_profilepopup_info_left">
<?php echo _p('profilepopup.profile_is_private'); ?>.
                </div>
            </div>
        </div>
<?php endif; ?>

        <!-- show profile -->
<?php if (isset ( $this->_aVars['iIsUser'] ) && $this->_aVars['iIsUser'] == 1 && isset ( $this->_aVars['iIsCanViewProfile'] ) && $this->_aVars['iIsCanViewProfile'] == 1): ?>
        <div class="yn_profilepopup_hovercard_content">
<?php if (isset ( $this->_aVars['aCoverPhoto'] )): ?>
              <div class="yn_profilepopup_cover type-user">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aCoverPhoto']['server_id'],'path' => 'photo.url_photo','file' => $this->_aVars['aCoverPhoto']['destination'],'suffix' => '_500')); ?>
                  <div class="yn_profilepopup_backgroundcover"></div>
              </div>
<?php else: ?>
              <div class="yn-profilepopup-nocover"></div>
<?php endif; ?>
            <div class="yn-profilepopup_basic_info" <?php if (! Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>style="margin-left: 10px;"<?php endif; ?>>
<?php if (Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>
                    <div class="yn_profilepopup_image">
<?php if (! $this->_aVars['isFBUser']): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aUser'],'suffix' => '_200_square','max_width' => 100,'max_height' => 100)); ?>
<?php else: ?>
<?php echo $this->_aVars['sUserProfileImage']; ?>
<?php endif; ?>
                    </div>
<?php endif; ?>
                 <div class="yn_profilepopup_main_title <?php if (! Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>yn_profilepopup_title_nophoto<?php endif; ?> "><?php echo Phpfox::getLib('phpfox.parse.output')->split('<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aUser']['user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aUser']['user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aUser']['user_name'], ((empty($this->_aVars['aUser']['user_name']) && isset($this->_aVars['aUser']['profile_page_id'])) ? $this->_aVars['aUser']['profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aUser']['user_id'], $this->_aVars['aUser']['full_name'])), 30, '...') . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aUser']['user_id']) ? '' : '</a>') . '</span>', 20); ?></div>
            </div>
                <div class="yn_profilepopup_main"  <?php if (! Phpfox ::getParam('profilepopup.enable_thumbnails')): ?>style="margin-left: 10px;"<?php endif; ?>>
<?php (($sPlugin = Phpfox_Plugin::get('profilepopup.template_block_popup_1')) ? eval($sPlugin) : false); ?>
<?php (($sPlugin = Phpfox_Plugin::get('profilepopup.template_block_popup_3')) ? eval($sPlugin) : false); ?>
<?php if ($this->_aVars['bIsPage']): ?>
<?php echo Phpfox::getLib('locale')->convert($this->_aVars['aUser']['page']['category_name']); ?>
                        <br />
<?php if ($this->_aVars['aUser']['page']['page_type'] == '1'): ?>
<?php if ($this->_aVars['aUser']['page']['total_like'] == 1): ?>
<?php echo _p('profilepopup.1_member'); ?>
<?php elseif ($this->_aVars['aUser']['page']['total_like'] > 1): ?>
<?php echo _p('profilepopup.total_members', array('total' => number_format($this->_aVars['aUser']['page']['total_like'])));  endif; ?>
<?php else: ?>
<?php if ($this->_aVars['aUser']['page']['total_like'] == 1): ?>
<?php echo _p('profilepopup.1_person_likes_this'); ?>
<?php elseif ($this->_aVars['aUser']['page']['total_like'] > 1): ?>
<?php echo _p('profilepopup.total_people_like_this', array('total' => number_format($this->_aVars['aUser']['page']['total_like']))); ?>
<?php endif; ?>
<?php endif; ?>
<?php else: ?>
                        <div>
<?php if (count ( $this->_aVars['aAllItems'] ) > 0): ?>
<?php if (count((array)$this->_aVars['aAllItems'])):  foreach ((array) $this->_aVars['aAllItems'] as $this->_aVars['iKey'] => $this->_aVars['aItem']): ?>
                                <!-- status -->
<?php if (isset ( $this->_aVars['aStatus'] ) === true && $this->_aVars['aItem']['name'] == 'status' && isset ( $this->_aVars['aStatus']['content'] ) && strlen ( trim ( $this->_aVars['aStatus']['content'] ) ) > 0 && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php if (isset ( $this->_aVars['aStatus'] ) === true && count ( $this->_aVars['aStatus'] ) > 0):  echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->parse($this->_aVars['aStatus']['content']), $this->_aVars['iShorten'], '...'), 20);  else: ?>&nbsp;<?php endif; ?></div>
                                </div>
<?php endif; ?>

<?php if (isset ( $this->_aVars['iIsCanViewBasicInfo'] ) && $this->_aVars['iIsCanViewBasicInfo'] == 1): ?>
                                <!-- first name -->
<?php if (array_key_exists ( 'first_name' , $this->_aVars['aUser'] ) === true && $this->_aVars['aItem']['name'] == 'first_name' && strlen ( trim ( $this->_aVars['aUser']['first_name'] ) ) > 0 && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aUser']['first_name']; ?>&nbsp;</div>
                                </div>
<?php endif; ?>

                                <!-- last name -->
<?php if (array_key_exists ( 'last_name' , $this->_aVars['aUser'] ) === true && $this->_aVars['aItem']['name'] == 'last_name' && strlen ( trim ( $this->_aVars['aUser']['last_name'] ) ) > 0 && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aUser']['last_name']; ?>&nbsp;</div>
                                </div>
<?php endif; ?>

                                <!-- gender -->
<?php if (array_key_exists ( 'gender_name' , $this->_aVars['aUser'] ) === true && $this->_aVars['aItem']['name'] == 'gender' && strlen ( trim ( $this->_aVars['aUser']['gender_name'] ) ) > 0 && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aUser']['gender_name']; ?>&nbsp;</div>
                                </div>
<?php endif; ?>

                                <!-- birthday -->
<?php if (array_key_exists ( 'birthdate_display' , $this->_aVars['aUser'] ) === true && $this->_aVars['aItem']['name'] == 'birthday' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && count ( $this->_aVars['aUser']['birthdate_display'] ) > 0): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php if (count((array)$this->_aVars['aUser']['birthdate_display'])):  foreach ((array) $this->_aVars['aUser']['birthdate_display'] as $this->_aVars['sAgeType'] => $this->_aVars['sBirthDisplay']): ?> <?php if ($this->_aVars['aUser']['dob_setting'] == '2'): ?>  <?php echo _p('profilepopup.age_years_old', array('age' => $this->_aVars['sBirthDisplay'])); ?>  <?php else: ?> <?php echo $this->_aVars['sBirthDisplay']; ?> <?php endif; ?> <?php endforeach; endif; ?>&nbsp;</div>
                                </div>
<?php endif; ?>

                                <!-- relationship status -->
<?php if (isset ( $this->_aVars['aRelationshipStatus'] ) === true && Phpfox ::getParam('user.enable_relationship_status') && isset ( $this->_aVars['aRelationshipStatus']['lang_name'] ) && strlen ( trim ( $this->_aVars['aRelationshipStatus']['lang_name'] ) ) > 0 && $this->_aVars['aItem']['name'] == 'relationship_status' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php if (isset ( $this->_aVars['aRelationshipStatus'] ) === true && count ( $this->_aVars['aRelationshipStatus'] ) > 0):  if ($this->_aVars['bIsMarried']):  echo $this->_aVars['sRelationship'];  else:  echo $this->_aVars['aRelationshipStatus']['lang_name'];  endif;  else: ?>&nbsp;<?php endif; ?></div>
                                </div>
<?php endif; ?>
<?php endif; ?>


                                <!-- custom field -->
<?php if (array_key_exists ( 'cf_content' , $this->_aVars['aItem'] ) === true && isset ( $this->_aVars['iIsCanViewProfileInfo'] ) && $this->_aVars['iIsCanViewProfileInfo'] == 1 && intval ( $this->_aVars['aItem']['is_custom_field'] ) == 1 && strlen ( trim ( $this->_aVars['aItem']['cf_content'] ) ) > 0 && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->parse($this->_aVars['aItem']['cf_content']), $this->_aVars['iShorten'], '...'), 20); ?>&nbsp;</div>
                                </div>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php endif; ?>
                        </div>

                        <!-- Resume Module -->
<?php if (isset ( $this->_aVars['canViewResume'] ) && $this->_aVars['canViewResume'] == '1' && isset ( $this->_aVars['aResumeItems'] ) && $this->_aVars['oneItemResumeIsDisplay'] == '1'): ?>
                        <div class="yn_profilepopup_mutual" style=<?php if ($this->_aVars['bShowLine']): ?>"border-top: 1px solid #B8B8B8;"<?php else: ?>"padding-top:0; margin-top:0;"<?php endif; ?>>
<?php if (count((array)$this->_aVars['aResumeItems'])):  foreach ((array) $this->_aVars['aResumeItems'] as $this->_aVars['iKey'] => $this->_aVars['aItem']): ?>
                                <!-- Currently Work -->
<?php if ($this->_aVars['aItem']['name'] == 'currently_work' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && isset ( $this->_aVars['aCurrentWork']['title'] ) && strlen ( $this->_aVars['aCurrentWork']['title'] ) > 0): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aCurrentWork']['title']; ?> <?php echo _p("resume.at"); ?> <?php echo $this->_aVars['aCurrentWork']['company_name']; ?></div>
                                </div>
<?php endif; ?>
                                <!-- Highest Level -->
<?php if ($this->_aVars['aItem']['name'] == 'highest_level' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && $this->_aVars['aResume']['level_id'] > 0): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['aResume']['level_name'])); ?></div>
                                </div>
<?php endif; ?>
                                <!-- Highest Education -->
<?php if ($this->_aVars['aItem']['name'] == 'highest_education' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && isset ( $this->_aVars['aLatestEducation'] )): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aLatestEducation']['degree']; ?>, <?php echo $this->_aVars['aLatestEducation']['field']; ?> <?php echo _p("resume.at"); ?> <?php echo $this->_aVars['aLatestEducation']['school_name']; ?></div>
                                </div>
<?php endif; ?>
                                <!-- Phone Number -->
<?php if ($this->_aVars['aItem']['name'] == 'phone_number' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && ! empty ( $this->_aVars['aResume']['phone'] ) && isset ( $this->_aVars['aResume']['phone']['0'] )): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aResume']['phone']['0']['text']; ?> (<?php echo _p("resume.".$this->_aVars['aResume']['phone']['0']['type']); ?>)</div>
                                </div>
<?php endif; ?>
                                <!-- IM -->
<?php if ($this->_aVars['aItem']['name'] == 'im' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && ! empty ( $this->_aVars['aResume']['imessage'] ) && isset ( $this->_aVars['aResume']['imessage']['0'] )): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aResume']['imessage']['0']['text']; ?> (<?php echo _p("resume.".$this->_aVars['aResume']['imessage']['0']['type']); ?>)</div>
                                </div>
<?php endif; ?>
                                <!-- Categories -->
<?php if ($this->_aVars['aItem']['name'] == 'categories' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && isset ( $this->_aVars['aCats'] ) && count ( $this->_aVars['aCats'] ) > 0 && strlen ( $this->_aVars['catPlainText'] ) > 0): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">
<?php echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->parse($this->_aVars['catPlainText']), $this->_aVars['iShorten'], '...'), 20); ?>
                                        </div>
                                </div>
<?php endif; ?>
                                <!-- Email -->
<?php if ($this->_aVars['aItem']['name'] == 'email' && intval ( $this->_aVars['aItem']['is_active'] ) == 1 && intval ( $this->_aVars['aItem']['is_display'] ) == 1 && ! empty ( $this->_aVars['aResume']['email'] ) && isset ( $this->_aVars['aResume']['email']['0'] )): ?>
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left"><?php echo $this->_aVars['aItem']['lang_name']; ?>:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right"><?php echo $this->_aVars['aResume']['email']['0']; ?></div>
                                </div>
<?php endif; ?>
<?php endforeach; endif; ?>
                        </div>
<?php endif; ?>

<?php if (isset ( $this->_aVars['iIsCanViewMutualFriends'] ) && $this->_aVars['iIsCanViewMutualFriends'] == 1 && $this->_aVars['sShowMutualFriend'] === '1' && $this->_aVars['iMutualTotal'] > 0): ?>
                            <div class="yn_profilepopup_mutual" style=<?php if ($this->_aVars['bShowLine']): ?>"border-top: 1px solid #B8B8B8;"<?php else: ?>"padding-top:0; margin-top:0;"<?php endif; ?>>
                                <a href="#" onclick="$Core.box('profilepopup.getMutualFriends', 300, 'user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>'); return false;"><?php echo _p('profilepopup.mutual_friends_total', array('total' => $this->_aVars['iMutualTotal'])); ?></a>
                                <div class="yn_profilepopup_block_listing_inline">
                                        <ul>
<?php if (count((array)$this->_aVars['aMutualFriends'])):  foreach ((array) $this->_aVars['aMutualFriends'] as $this->_aVars['iKey'] => $this->_aVars['aMutual']): ?>
                                                <li><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aMutual'],'suffix' => '_50_square','max_width' => 32,'max_height' => 32,'class' => 'js_hover_title')); ?></li>
<?php endforeach; endif; ?>
                                        </ul>
                                </div>
                            </div>
<?php endif; ?>
<?php (($sPlugin = Phpfox_Plugin::get('profilepopup.template_block_popup_5')) ? eval($sPlugin) : false); ?>
<?php endif; ?>
<?php (($sPlugin = Phpfox_Plugin::get('profilepopup.template_block_popup_2')) ? eval($sPlugin) : false); ?>
                </div>
        </div>
<?php endif; ?>
    </div>

<?php if (Phpfox ::isUser() && isset ( $this->_aVars['iIsUser'] ) && $this->_aVars['iIsUser'] == 1 && $this->_aVars['aUser']['user_id'] != Phpfox ::getUserId() && ! $this->_aVars['bIsPage']): ?>
        <div class="yn_profilepopup_hovercard_footer">
            <ul class="yn_profilepopup_list_horizontal">
<?php if (isset ( $this->_aVars['aUser']['is_online'] ) && ( intval ( $this->_aVars['aUser']['is_online'] ) == 1 )): ?>
                <li class="yn_profilepopup_list_item">
                        <a title="<?php echo _p('profilepopup.pp_online'); ?>" onclick="return false;" class="yn_profilepopup_icon_being_online" href="#"><i class="fa fa-dot-circle-o"></i></a>
                </li>
<?php endif; ?>

<?php if (isset ( $this->_aVars['canViewResume'] ) && $this->_aVars['canViewResume'] == '1' && isset ( $this->_aVars['aResumeItems'] ) && $this->_aVars['oneItemResumeIsDisplay'] == '1'): ?>
                <li class="yn_profilepopup_list_item">
                    <a title="<?php echo _p('profilepopup.view_resume'); ?>" target="_blank" class="yn_profilepopup_icon_resume" href="<?php echo Phpfox::permalink('resume.view', $this->_aVars['aResume']['resume_id'], $this->_aVars['aResume']['headline'], false, null, (array) array (
)); ?>" >
                        <i class="fa fa-eye"></i>&nbsp;<?php echo _p('profilepopup.view_resume'); ?>
                    </a>
                </li>
<?php endif; ?>

<?php if (Phpfox ::isModule('foxfavorite') && Phpfox ::isUser() && isset ( $this->_aVars['sFFModule'] ) && isset ( $this->_aVars['iFFItemId'] ) && $this->_aVars['sFFModule'] == 'profile'): ?>
<?php if (! $this->_aVars['bFFIsAlreadyFavorite']): ?>
                                <li class="yn_profilepopup_list_item">
                                        <a title="<?php echo _p('profilepopup.favorite'); ?>" onclick="ynfbpp.closePopup(); $('#js_favorite_link_unlike_<?php echo $this->_aVars['iFFItemId']; ?>').show(); $('#js_favorite_link_like_<?php echo $this->_aVars['iFFItemId']; ?>').hide(); $.ajaxCall('foxfavorite.addFavorite', 'type=<?php echo $this->_aVars['sFFModule']; ?>&amp;id=<?php echo $this->_aVars['iFFItemId']; ?>', 'GET'); <?php if ($this->_aVars['bEnableCachePopup']): ?>window.setTimeout('ynfbpp.refreshPage(null)', 500);<?php endif; ?> return false;" class="yn_profilepopup_icon_favorite" href="#" >
                                            <i class="fa fa-heart"></i>&nbsp;<?php echo _p('profilepopup.favorite'); ?></a>
                                </li>
<?php else: ?>
                                <li class="yn_profilepopup_list_item">
                                        <a title="<?php echo _p('profilepopup.unfavorite'); ?>" onclick="ynfbpp.closePopup(); $('#js_favorite_link_like_<?php echo $this->_aVars['iFFItemId']; ?>').show(); $('#js_favorite_link_unlike_<?php echo $this->_aVars['iFFItemId']; ?>').hide(); $.ajaxCall('foxfavorite.deleteFavorite', 'type=<?php echo $this->_aVars['sFFModule']; ?>&amp;id=<?php echo $this->_aVars['iFFItemId']; ?>', 'GET'); <?php if ($this->_aVars['bEnableCachePopup']): ?>window.setTimeout('ynfbpp.refreshPage(null)', 500);<?php endif; ?> return false;" class="yn_profilepopup_icon_unfavorite" href="#" >
                                            <i class="fa fa-heart-o"></i>&nbsp;<?php echo _p('profilepopup.unfavorite'); ?>
                                        </a>
                                </li>
<?php endif; ?>
<?php endif; ?>

<?php if (isset ( $this->_aVars['aUser']['is_friend'] ) === false || ! $this->_aVars['aUser']['is_friend']): ?>
                <li class="yn_profilepopup_list_item">
                    <a title="<?php echo _p('profilepopup.add_to_friends'); ?>" onclick="ynfbpp.closePopup();return $Core.addAsFriend('<?php echo $this->_aVars['aUser']['user_id']; ?>');" class="yn_profilepopup_icon_add_friend" href="#">
                        <i class="fa fa-user-plus"></i>&nbsp;<?php echo _p('profilepopup.add_as_friend'); ?>
                    </a>
                </li>
<?php endif; ?>

<?php if (Phpfox ::getUserBy('profile_page_id') == 0 && isset ( $this->_aVars['aFriend'] ) && isset ( $this->_aVars['aFriend']['friend_id'] ) && intval ( $this->_aVars['aFriend']['friend_id'] ) > 0): ?>
                <li class="yn_profilepopup_list_item">
                        <a title="<?php echo _p('profilepopup.unfriend'); ?>" rel="<?php echo $this->_aVars['aFriend']['friend_id']; ?>" onclick="ynfbpp.closePopup();return ynfbpp.unfriend('<?php echo $this->_aVars['aFriend']['friend_id']; ?>');" class="yn_profilepopup_icon_remove_friend" href="#">
                            <i class="fa fa-user-times"></i>&nbsp;<?php echo _p('profilepopup.unfriend'); ?>
                        </a>
                </li>
<?php endif; ?>
                <li class="yn_profilepopup_list_item">
                    <a title="<?php echo _p('profilepopup.send_message'); ?>" onclick="ynfbpp.closePopup();$Core.composeMessage({user_id: <?php echo $this->_aVars['aUser']['user_id']; ?>}); return false;" class="yn_profilepopup_icon_send_message" href="#">
                        <i class="fa fa-envelope"></i>&nbsp;<?php echo _p('profilepopup.send_message'); ?>
                    </a>
                </li>
<?php if (isset ( $this->_aVars['bShowBDay'] ) && $this->_aVars['bShowBDay'] == true): ?>
                <li class="yn_profilepopup_list_item">
                    <a title="<?php echo _p('profilepopup.say_happy_birthday'); ?>" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aUser']['user_name']); ?>" onclick="ynfbpp.closePopup(); return true;" class="yn_profilepopup_icon_say_happy_birthday">
                        <i class="fa fa-birthday-cake"></i>&nbsp;<?php echo _p('profilepopup.say_happy_birthday'); ?>
                    </a>
                </li>
<?php endif; ?>
            </ul>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
        </div>
<?php else: ?>
<?php if (isset ( $this->_aVars['canViewResume'] ) && $this->_aVars['canViewResume'] == '1' && isset ( $this->_aVars['aResumeItems'] ) && $this->_aVars['oneItemResumeIsDisplay'] == '1'): ?>
            <div class="yn_profilepopup_hovercard_footer">
                <ul class="yn_profilepopup_list_horizontal">
                    <li class="yn_profilepopup_list_item">
                        <a title="<?php echo _p('profilepopup.view_resume'); ?>" target="_blank" class="yn_profilepopup_icon_resume" href="<?php echo Phpfox::permalink('resume.view', $this->_aVars['aResume']['resume_id'], $this->_aVars['aResume']['headline'], false, null, (array) array (
)); ?>" >
                            <i class="fa fa-eye"></i>&nbsp;<?php echo _p('profilepopup.view_resume'); ?>
                        </a>
                    </li>
                </ul>
                <div class="clearfix"></div>
                <div class="clearfix"></div>
            </div>
<?php else: ?>
            <div class="yn_profilepopup_hovercard_footer no-action"></div>
<?php endif; ?>
<?php endif; ?>
</div>

