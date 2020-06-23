<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<style type="text/css">
    .yn_profilepopup_cover.type-user img {l}
        position: relative;
        left:0;
        top: {$coverPhotoPosition}px;
    {r}
</style>

<div class="no-popup uiContextualDialogContent">
    <div class="yn_profilepopup_hovercard_stage" {if !isset($aCoverPhoto)} style="padding-top: 10px;" {/if}>
        <!-- user not found -->
        {if isset($iIsUser) && $iIsUser == 0}
        <div class="yn_profilepopup_hovercard_content">
            <div>
                <div class="yn_profilepopup_info yn_profilepopup_info_left">
                    {phrase var='profilepopup.user_not_found'}.
                </div>
            </div>
        </div>
        {/if}

        <!-- profile is private -->
        {if isset($iIsCanViewProfile) && $iIsCanViewProfile == 0}
        <div class="yn_profilepopup_hovercard_content">
              {if isset($aCoverPhoto)}
              <div class="yn_profilepopup_cover type-user">
                  {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_500' }
                  <div class="yn_profilepopup_backgroundcover"></div>
              </div>
              {else}
              <div class="yn-profilepopup-nocover"></div>
              {/if}
            <div class="yn-profilepopup_basic_info" {if !Phpfox::getParam('profilepopup.enable_thumbnails')}style="margin-left: 10px;"{/if}>
                {if Phpfox::getParam('profilepopup.enable_thumbnails')}
                <div class="yn_profilepopup_image">
                   {if !$isFBUser}
                            {img user=$aUser suffix='_200_square' max_width=100 max_height=100}
                    {else}
                            {$sUserProfileImage}
                    {/if}
                </div>
                {/if}
                <div class="yn_profilepopup_main_title {if !Phpfox::getParam('profilepopup.enable_thumbnails')}yn_profilepopup_title_nophoto{/if} ">{$aUser|user:'':'':30|split:20}</div>
            </div>
            <div class="yn_profilepopup_main" {if !Phpfox::getParam('profilepopup.enable_thumbnails')}style="margin-left: 10px;"{/if}>
                <div class="yn_profilepopup_info yn_profilepopup_info_left">
                        {phrase var='profilepopup.profile_is_private'}.
                </div>
            </div>
        </div>
        {/if}

        <!-- show profile -->
        {if isset($iIsUser) && $iIsUser == 1 && isset($iIsCanViewProfile) && $iIsCanViewProfile == 1}
        <div class="yn_profilepopup_hovercard_content">
              {if isset($aCoverPhoto)}
              <div class="yn_profilepopup_cover type-user">
                  {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_500' }
                  <div class="yn_profilepopup_backgroundcover"></div>
              </div>
              {else}
              <div class="yn-profilepopup-nocover"></div>
              {/if}
            <div class="yn-profilepopup_basic_info" {if !Phpfox::getParam('profilepopup.enable_thumbnails')}style="margin-left: 10px;"{/if}>
                 {if Phpfox::getParam('profilepopup.enable_thumbnails')}
                    <div class="yn_profilepopup_image">
                       {if !$isFBUser}
                                {img user=$aUser suffix='_200_square' max_width=100 max_height=100}
                        {else}
                                {$sUserProfileImage}
                        {/if}
                    </div>
                 {/if}
                 <div class="yn_profilepopup_main_title {if !Phpfox::getParam('profilepopup.enable_thumbnails')}yn_profilepopup_title_nophoto{/if} ">{$aUser|user:'':'':30|split:20}</div>
            </div>
                <div class="yn_profilepopup_main"  {if !Phpfox::getParam('profilepopup.enable_thumbnails')}style="margin-left: 10px;"{/if}>
                        {plugin call='profilepopup.template_block_popup_1'}
                        {plugin call='profilepopup.template_block_popup_3'}
                        {if $bIsPage}
                        {$aUser.page.category_name|convert}
                        <br />
                        {if $aUser.page.page_type == '1'}
                        {if $aUser.page.total_like == 1}
                        {phrase var='profilepopup.1_member'}
                        {elseif $aUser.page.total_like > 1}
                        {phrase var='profilepopup.total_members' total=$aUser.page.total_like|number_format}{/if}
                        {else}
                        {if $aUser.page.total_like == 1}
                        {phrase var='profilepopup.1_person_likes_this'}
                        {elseif $aUser.page.total_like > 1}
                        {phrase var='profilepopup.total_people_like_this' total=$aUser.page.total_like|number_format}
                        {/if}
                        {/if}
                        {else}
                        <div>
                                {if count($aAllItems) > 0}
                                {foreach from=$aAllItems key=iKey item=aItem}
                                <!-- status -->
                                {if isset($aStatus) === true && $aItem.name == 'status' && isset($aStatus.content) && strlen(trim($aStatus.content)) > 0 &&  intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{if isset($aStatus) === true && count($aStatus) > 0}{$aStatus.content|parse|shorten:$iShorten:'...'|split:20}{else}&nbsp;{/if}</div>
                                </div>
                                {/if}

                                {if isset($iIsCanViewBasicInfo) && $iIsCanViewBasicInfo == 1}
                                <!-- first name -->
                                {if array_key_exists('first_name', $aUser) === true && $aItem.name == 'first_name' && strlen(trim($aUser.first_name)) > 0 && intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aUser.first_name}&nbsp;</div>
                                </div>
                                {/if}

                                <!-- last name -->
                                {if array_key_exists('last_name', $aUser) === true && $aItem.name == 'last_name' && strlen(trim($aUser.last_name)) > 0 &&  intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aUser.last_name}&nbsp;</div>
                                </div>
                                {/if}

                                <!-- gender -->
                                {if array_key_exists('gender_name', $aUser) === true && $aItem.name == 'gender' && strlen(trim($aUser.gender_name)) > 0 &&  intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aUser.gender_name}&nbsp;</div>
                                </div>
                                {/if}

                                <!-- birthday -->
                                {if array_key_exists('birthdate_display', $aUser) === true && $aItem.name == 'birthday' &&  intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1 && count($aUser.birthdate_display) > 0}
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{foreach from=$aUser.birthdate_display key=sAgeType item=sBirthDisplay} {if $aUser.dob_setting == '2'}  {phrase var='profilepopup.age_years_old' age=$sBirthDisplay}  {else} {$sBirthDisplay} {/if} {/foreach}&nbsp;</div>
                                </div>
                                {/if}

                                <!-- relationship status -->
                                {if isset($aRelationshipStatus) === true && Phpfox::getParam('user.enable_relationship_status') && isset($aRelationshipStatus.lang_name) && strlen(trim($aRelationshipStatus.lang_name)) > 0 &&  $aItem.name == 'relationship_status' && intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{if isset($aRelationshipStatus) === true && count($aRelationshipStatus) > 0}{if $bIsMarried}{$sRelationship}{else}{$aRelationshipStatus.lang_name}{/if}{else}&nbsp;{/if}</div>
                                </div>
                                {/if}
                                {/if}


                                <!-- custom field -->
                                {if array_key_exists('cf_content', $aItem) === true && isset($iIsCanViewProfileInfo) && $iIsCanViewProfileInfo == 1 && intval($aItem.is_custom_field) == 1 && strlen(trim($aItem.cf_content)) > 0 &&  intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aItem.cf_content|parse|shorten:$iShorten:'...'|split:20}&nbsp;</div>
                                </div>
                                {/if}
                                {/foreach}
                                {/if}
                        </div>

                        <!-- Resume Module -->
                        {if isset($canViewResume) && $canViewResume == '1' && isset($aResumeItems) && $oneItemResumeIsDisplay == '1'}
                        <div class="yn_profilepopup_mutual" style={if $bShowLine}"border-top: 1px solid #B8B8B8;"{else}"padding-top:0; margin-top:0;"{/if}>
                            {foreach from=$aResumeItems key=iKey item=aItem}
                                <!-- Currently Work -->
                                {if $aItem.name == 'currently_work'
                                    &&  intval($aItem.is_active) == 1
                                    && intval($aItem.is_display) == 1
                                    && isset($aCurrentWork.title) && strlen($aCurrentWork.title) > 0
                                }
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aCurrentWork.title} {phrase var="resume.at"} {$aCurrentWork.company_name}</div>
                                </div>
                                {/if}
                                <!-- Highest Level -->
                                {if $aItem.name == 'highest_level'
                                    &&  intval($aItem.is_active) == 1
                                    && intval($aItem.is_display) == 1
                                    && $aResume.level_id > 0
                                }
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aResume.level_name|convert|clean}</div>
                                </div>
                                {/if}
                                <!-- Highest Education -->
                                {if $aItem.name == 'highest_education'
                                    &&  intval($aItem.is_active) == 1
                                    && intval($aItem.is_display) == 1
                                    && isset($aLatestEducation)
                                }
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aLatestEducation.degree}, {$aLatestEducation.field} {phrase var="resume.at"} {$aLatestEducation.school_name}</div>
                                </div>
                                {/if}
                                <!-- Phone Number -->
                                {if $aItem.name == 'phone_number'
                                    &&  intval($aItem.is_active) == 1
                                    && intval($aItem.is_display) == 1
                                    && !empty($aResume.phone)
                                    && isset($aResume.phone.0)
                                }
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aResume.phone.0.text} ({phrase var="resume.".$aResume.phone.0.type})</div>
                                </div>
                                {/if}
                                <!-- IM -->
                                {if $aItem.name == 'im'
                                    &&  intval($aItem.is_active) == 1
                                    && intval($aItem.is_display) == 1
                                    && !empty($aResume.imessage)
                                    && isset($aResume.imessage.0)
                                }
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aResume.imessage.0.text} ({phrase var="resume.".$aResume.imessage.0.type})</div>
                                </div>
                                {/if}
                                <!-- Categories -->
                                {if $aItem.name == 'categories'
                                    &&  intval($aItem.is_active) == 1
                                    && intval($aItem.is_display) == 1
                                    && isset($aCats)
                                    && count($aCats) > 0
                                    && strlen($catPlainText) > 0
                                }
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">
                                            {$catPlainText|parse|shorten:$iShorten:'...'|split:20}
                                        </div>
                                </div>
                                {/if}
                                <!-- Email -->
                                {if $aItem.name == 'email'
                                    &&  intval($aItem.is_active) == 1
                                    && intval($aItem.is_display) == 1
                                    && !empty($aResume.email)
                                    && isset($aResume.email.0)
                                }
                                <div class="yn_profilepopup_info">
                                        <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                        <div class="yn_profilepopup_info_right">{$aResume.email.0}</div>
                                </div>
                                {/if}
                            {/foreach}
                        </div>
                        {/if}

                        {if isset($iIsCanViewMutualFriends) && $iIsCanViewMutualFriends == 1 && $sShowMutualFriend === '1' && $iMutualTotal > 0}
                            <div class="yn_profilepopup_mutual" style={if $bShowLine}"border-top: 1px solid #B8B8B8;"{else}"padding-top:0; margin-top:0;"{/if}>
                                <a href="#" onclick="$Core.box('profilepopup.getMutualFriends', 300, 'user_id={$aUser.user_id}'); return false;">{phrase var='profilepopup.mutual_friends_total' total=$iMutualTotal}</a>
                                <div class="yn_profilepopup_block_listing_inline">
                                        <ul>
                                                {foreach from=$aMutualFriends key=iKey item=aMutual}
                                                <li>{img user=$aMutual suffix='_50_square' max_width=32 max_height=32 class='js_hover_title'}</li>
                                                {/foreach}
                                        </ul>
                                </div>
                            </div>
                        {/if}
                        {plugin call='profilepopup.template_block_popup_5'}
                        {/if}
                        {plugin call='profilepopup.template_block_popup_2'}
                </div>
        </div>
        {/if}
    </div>

    {if Phpfox::isUser() && isset($iIsUser) && $iIsUser == 1 && $aUser.user_id != Phpfox::getUserId() && !$bIsPage}
        <div class="yn_profilepopup_hovercard_footer">
            <ul class="yn_profilepopup_list_horizontal">
                {if isset($aUser.is_online) && (intval($aUser.is_online) == 1)}
                <li class="yn_profilepopup_list_item">
                        <a title="{phrase var='profilepopup.pp_online'}" onclick="return false;" class="yn_profilepopup_icon_being_online" href="#"><i class="fa fa-dot-circle-o"></i></a>
                </li>
                {/if}

                {if isset($canViewResume) && $canViewResume == '1' && isset($aResumeItems) && $oneItemResumeIsDisplay == '1'}
                <li class="yn_profilepopup_list_item">
                    <a title="{phrase var='profilepopup.view_resume'}" target="_blank" class="yn_profilepopup_icon_resume" href="{permalink module='resume.view' id=$aResume.resume_id title=$aResume.headline}" >
                        <i class="fa fa-eye"></i>&nbsp;{phrase var='profilepopup.view_resume'}
                    </a>
                </li>
                {/if}

                {if Phpfox::isModule('foxfavorite') && Phpfox::isUser() && isset($sFFModule) && isset($iFFItemId) && $sFFModule == 'profile'}
                        {if !$bFFIsAlreadyFavorite}
                                <li class="yn_profilepopup_list_item">
                                        <a title="{phrase var='profilepopup.favorite'}" onclick="ynfbpp.closePopup(); $('#js_favorite_link_unlike_{$iFFItemId}').show(); $('#js_favorite_link_like_{$iFFItemId}').hide(); $.ajaxCall('foxfavorite.addFavorite', 'type={$sFFModule}&amp;id={$iFFItemId}', 'GET'); {if $bEnableCachePopup}window.setTimeout('ynfbpp.refreshPage(null)', 500);{/if} return false;" class="yn_profilepopup_icon_favorite" href="#" >
                                            <i class="fa fa-heart"></i>&nbsp;{phrase var='profilepopup.favorite'
                                        }</a>
                                </li>
                        {else}
                                <li class="yn_profilepopup_list_item">
                                        <a title="{phrase var='profilepopup.unfavorite'}" onclick="ynfbpp.closePopup(); $('#js_favorite_link_like_{$iFFItemId}').show(); $('#js_favorite_link_unlike_{$iFFItemId}').hide(); $.ajaxCall('foxfavorite.deleteFavorite', 'type={$sFFModule}&amp;id={$iFFItemId}', 'GET'); {if $bEnableCachePopup}window.setTimeout('ynfbpp.refreshPage(null)', 500);{/if} return false;" class="yn_profilepopup_icon_unfavorite" href="#" >
                                            <i class="fa fa-heart-o"></i>&nbsp;{phrase var='profilepopup.unfavorite'}
                                        </a>
                                </li>
                        {/if}
                {/if}

                {if isset($aUser.is_friend) === false || !$aUser.is_friend}
                <li class="yn_profilepopup_list_item">
                    <a title="{phrase var='profilepopup.add_to_friends'}" onclick="ynfbpp.closePopup();return $Core.addAsFriend('{$aUser.user_id}');" class="yn_profilepopup_icon_add_friend" href="#">
                        <i class="fa fa-user-plus"></i>&nbsp;{phrase var='profilepopup.add_as_friend'}
                    </a>
                </li>
                {/if}

                {if Phpfox::getUserBy('profile_page_id') == 0 && isset($aFriend) && isset($aFriend.friend_id) && intval($aFriend.friend_id) > 0 }
                <li class="yn_profilepopup_list_item">
                        <a title="{phrase var='profilepopup.unfriend'}" rel="{$aFriend.friend_id}" onclick="ynfbpp.closePopup();return ynfbpp.unfriend('{$aFriend.friend_id}');" class="yn_profilepopup_icon_remove_friend" href="#">
                            <i class="fa fa-user-times"></i>&nbsp;{phrase var='profilepopup.unfriend'}
                        </a>
                </li>
                {/if}
                <li class="yn_profilepopup_list_item">
                    <a title="{phrase var='profilepopup.send_message'}" onclick="ynfbpp.closePopup();$Core.composeMessage({left_curly}user_id: {$aUser.user_id}{right_curly}); return false;" class="yn_profilepopup_icon_send_message" href="#">
                        <i class="fa fa-envelope"></i>&nbsp;{phrase var='profilepopup.send_message'}
                    </a>
                </li>
                {if isset($bShowBDay) && $bShowBDay == true}
                <li class="yn_profilepopup_list_item">
                    <a title="{phrase var='profilepopup.say_happy_birthday'}" href="{url link=$aUser.user_name}" onclick="ynfbpp.closePopup(); return true;" class="yn_profilepopup_icon_say_happy_birthday">
                        <i class="fa fa-birthday-cake"></i>&nbsp;{phrase var='profilepopup.say_happy_birthday'}
                    </a>
                </li>
                {/if}
            </ul>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
        </div>
    {else}
        {if isset($canViewResume) && $canViewResume == '1' && isset($aResumeItems) && $oneItemResumeIsDisplay == '1'}
            <div class="yn_profilepopup_hovercard_footer">
                <ul class="yn_profilepopup_list_horizontal">
                    <li class="yn_profilepopup_list_item">
                        <a title="{phrase var='profilepopup.view_resume'}" target="_blank" class="yn_profilepopup_icon_resume" href="{permalink module='resume.view' id=$aResume.resume_id title=$aResume.headline}" >
                            <i class="fa fa-eye"></i>&nbsp;{phrase var='profilepopup.view_resume'}
                        </a>
                    </li>
                </ul>
                <div class="clearfix"></div>
                <div class="clearfix"></div>
            </div>
        {else}
            <div class="yn_profilepopup_hovercard_footer no-action"></div>
        {/if}
    {/if}
</div>
