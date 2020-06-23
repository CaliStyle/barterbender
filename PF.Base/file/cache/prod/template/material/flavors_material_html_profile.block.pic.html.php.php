<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php
    
?>
<style type="text/css">
    .profiles_banner_bg .cover img.cover_photo
        {
        position: relative;
        left: 0;
        top: <?php echo $this->_aVars['iCoverPhotoPosition']; ?>px;
    }
</style>
<?php echo '
<script>
    $Core.coverPhotoPositionTop = ';  echo $this->_aVars['iCoverPhotoPosition'];  echo ';
</script>
'; ?>

<div class="profiles_banner <?php if (isset ( $this->_aVars['aCoverPhoto']['server_id'] )): ?>has_cover<?php endif; ?>">
<?php if (! empty ( $this->_aVars['aCoverPhoto']['destination'] ) || ! empty ( $this->_aVars['sCoverDefaultUrl'] )): ?>
	<div class="profiles_banner_bg">
		<div class="cover_bg"></div>
        <div class="cover-reposition-message"><?php echo _p('drag_to_reposition_your_photo'); ?></div>
<?php if (! empty ( $this->_aVars['aCoverPhoto']['destination'] )): ?>
        <a href="<?php echo Phpfox::permalink('photo', $this->_aVars['aCoverPhoto']['photo_id'], $this->_aVars['aCoverPhoto']['title'], false, null, (array) array (
)); ?>">
<?php endif; ?>
            <div class="cover" id="cover_bg_container">
                <div id="uploading-cover" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0"
                             aria-valuemin="0" aria-valuemax="100" style="width:0"></div>
                    </div>
                    <div><?php echo _p('uploading_your_photo'); ?>...</div>
                </div>
<?php if (! empty ( $this->_aVars['aCoverPhoto']['destination'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aCoverPhoto']['server_id'],'path' => 'photo.url_photo','file' => $this->_aVars['aCoverPhoto']['destination'],'suffix' => '_1024','class' => "visible-lg cover_photo")); ?>
                    <span style="background-image: url(<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aCoverPhoto']['server_id'],'path' => 'photo.url_photo','file' => $this->_aVars['aCoverPhoto']['destination'],'suffix' => '_1024','return_url' => true)); ?>)" class="hidden-lg"></span>
<?php elseif (! empty ( $this->_aVars['sCoverDefaultUrl'] )): ?>
                    <span style="background-image: url(<?php echo $this->_aVars['sCoverDefaultUrl']; ?>)"></span>
<?php endif; ?>
            </div>
<?php if (! empty ( $this->_aVars['aCoverPhoto']['destination'] )): ?>
        </a>
<?php endif; ?>
        <div class="cover-reposition-actions">
            <a role="button" class="btn btn-default" onclick="$Core.CoverPhoto.reposition.cancel()"><?php echo _p('cancel'); ?></a>
            <div id="save_reposition_cover" class="btn btn-primary" onclick="$Core.CoverPhoto.reposition.save()"><?php echo _p('save'); ?></div>
        </div>
    </div>
<?php endif; ?>

<?php if (Phpfox ::getUserParam('profile.can_change_cover_photo') && Phpfox ::getUserId() == $this->_aVars['aUser']['user_id']): ?>
        <div class="dropdown change-cover-block">
            <a title="<?php echo _p('change_cover'); ?>" role="button" data-toggle="dropdown" class=" btn btn-primary btn-gradient" id="js_change_cover_photo">
                <span class="ico ico-camera"></span>
            </a>

            <ul class="dropdown-menu">
                <li class="cover_section_menu_item">
                    <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aUser']['user_name'].'.photo'); ?>">
<?php echo _p('choose_from_photos'); ?>
                    </a>
                </li>
                <li class="cover_section_menu_item">
                    <a role="button" id="js_change_cover_photo" onclick="$Core.box('profile.logo', 500); return false;">
<?php echo _p('upload_photo'); ?>
                    </a>
                </li>
<?php if (! empty ( $this->_aVars['aUser']['cover_photo'] )): ?>
                    <li class="cover_section_menu_item reposition" role="presentation">
                        <a role="button" onclick="$Core.CoverPhoto.reposition.init('user', <?php echo $this->_aVars['aUser']['user_id']; ?>); return false;"><?php echo _p('reposition'); ?></a></li>
                    <li class="cover_section_menu_item " role="presentation">
                        <a role="button" onclick="$('#cover_section_menu_drop').hide(); $.ajaxCall('user.removeLogo'); return false;"><?php echo _p('remove_cover_photo'); ?></a></li>
<?php endif; ?>
            </ul>
        </div>
<?php endif; ?>

	<div class="profile-info-block">
		<div class="profile-image">
            <div class="profile_image_holder">
<?php if (Phpfox ::isModule('photo') && $this->_aVars['aProfileImage']): ?>
                <a href="<?php echo \Phpfox::permalink('photo', $this->_aVars['aProfileImage']['photo_id'], $this->_aVars['aProfileImage']['title']) ?>">
<?php echo $this->_aVars['sProfileImage']; ?>
                </a>
<?php else: ?>
<?php echo $this->_aVars['sProfileImage']; ?>
<?php endif; ?>
<?php if (Phpfox ::getUserId() == $this->_aVars['aUser']['user_id']): ?>
				<?php echo '
				<script>
					function changingProfilePhoto() {
						if ($(\'.profile_image_holder\').find(\'i.fa.fa-spin.fa-circle-o-notch\').length > 0) {
							$(\'.profile_image_holder\').find(\'a\').show();
							$(\'.profile_image_holder\').find(\'i.fa.fa-spin.fa-circle-o-notch\').remove();
						}
						else {
							$(\'.profile_image_holder\').find(\'a\').hide();
							$(\'.profile_image_holder\').append(\'<i class="fa fa-circle-o-notch fa-spin"></i>\');
						}
					};
				</script>
				'; ?>

				<form action="#">
					<label class="btn-primary btn-gradient" title="<?php echo _p('change_photo'); ?>" onclick="$Core.ProfilePhoto.update(<?php if ($this->_aVars['sPhotoUrl']): ?>'<?php echo $this->_aVars['sPhotoUrl']; ?>'<?php else: ?>false<?php endif; ?>, <?php echo $this->_aVars['iServerId']; ?>)"><span class="ico ico-camera"></span></label>
				
</form>

<?php endif; ?>
            </div>
		</div>

		<div class="profile-info">
			<div class="profile-extra-info">
				<h1 <?php if (Phpfox ::getParam('user.display_user_online_status')): ?>class="has-status-online"<?php endif; ?>>
                    <a href="<?php if (isset ( $this->_aVars['aUser']['link'] ) && ! empty ( $this->_aVars['aUser']['link'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aUser']['link']);  else:  echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aUser']['user_name']);  endif; ?>" title="<?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aUser']['full_name']); ?> <?php if (Phpfox ::getUserParam('profile.display_membership_info')): ?> &middot; <?php echo _p($this->_aVars['aUser']['title']);  endif; ?>">
<?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aUser']['full_name']); ?>
                    </a>
<?php if (Phpfox ::getParam('user.display_user_online_status')): ?>
<?php if ($this->_aVars['aUser']['is_online']): ?>
                            <span class="user_is_online" title="<?php echo _p('online'); ?>"><i class="fa fa-circle js_hover_title"></i></span>
<?php else: ?>
                            <span class="user_is_offline" title="<?php echo _p('offline'); ?>"><i class="fa fa-circle js_hover_title"></i></span>
<?php endif; ?>
<?php endif; ?>
				</h1>
				<div class="profile-info-detail">
<?php if (( ! empty ( $this->_aVars['aUser']['gender_name'] ) )): ?>
<?php echo $this->_aVars['aUser']['gender_name']; ?><b>.</b>
<?php endif; ?>
<?php if (User_Service_Privacy_Privacy ::instance()->hasAccess('' . $this->_aVars['aUser']['user_id'] . '' , 'profile.view_location' ) && ( ! empty ( $this->_aVars['aUser']['city_location'] ) || ! empty ( $this->_aVars['aUser']['country_child_id'] ) || ! empty ( $this->_aVars['aUser']['location'] ) )): ?>
						<span>
<?php echo _p('lives_in'); ?>
<?php if (! empty ( $this->_aVars['aUser']['city_location'] )): ?>&nbsp;<?php echo $this->_aVars['aUser']['city_location'];  endif; ?>
<?php if (! empty ( $this->_aVars['aUser']['city_location'] ) && ( ! empty ( $this->_aVars['aUser']['country_child_id'] ) || ! empty ( $this->_aVars['aUser']['location'] ) )): ?>,<?php endif; ?>
<?php if (! empty ( $this->_aVars['aUser']['country_child_id'] )): ?>&nbsp;<?php echo Phpfox::getService('core.country')->getChild($this->_aVars['aUser']['country_child_id']); ?>,<?php endif; ?>
<?php if (! empty ( $this->_aVars['aUser']['location'] )): ?>&nbsp;<?php echo $this->_aVars['aUser']['location'];  endif; ?><b>.</b></span>
<?php endif; ?>

<?php if (isset ( $this->_aVars['aUser']['birthdate_display'] ) && is_array ( $this->_aVars['aUser']['birthdate_display'] ) && count ( $this->_aVars['aUser']['birthdate_display'] )): ?>
						<span>
<?php if (count((array)$this->_aVars['aUser']['birthdate_display'])):  foreach ((array) $this->_aVars['aUser']['birthdate_display'] as $this->_aVars['sAgeType'] => $this->_aVars['sBirthDisplay']): ?>
<?php if ($this->_aVars['aUser']['dob_setting'] == '2'): ?>
<?php if ($this->_aVars['sBirthDisplay'] == 1): ?>
<?php echo _p('1_year_old'); ?>
<?php else: ?>
<?php echo _p('age_years_old', array('age' => $this->_aVars['sBirthDisplay'])); ?>
<?php endif; ?>
<?php else: ?>
<?php echo _p('born_on_birthday', array('birthday' => $this->_aVars['sBirthDisplay'])); ?>
<?php endif; ?>
<?php endforeach; endif; ?><b>.</b></span>
<?php endif; ?>
<?php if (Phpfox ::getParam('user.enable_relationship_status') && isset ( $this->_aVars['sRelationship'] ) && $this->_aVars['sRelationship'] != ''): ?><span><?php echo $this->_aVars['sRelationship']; ?></span><b>.</b><?php endif; ?>
<?php if (isset ( $this->_aVars['aUser']['category_name'] )): ?><span><?php echo Phpfox::getLib('locale')->convert($this->_aVars['aUser']['category_name']); ?></span><?php endif; ?>
				</div>
			</div>

			<div class="profile-actions">
<?php if (Phpfox ::getUserId() == $this->_aVars['aUser']['user_id']): ?>
                    <a class="btn btn-default btn-icon btn-round" role="link" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('user.profile'); ?>">
                        <span class="ico ico-pencilline-o mr-1"></span>
<?php echo _p('edit_profile'); ?>
                    </a>
<?php endif; ?>
<?php if (Phpfox ::getUserId() != $this->_aVars['aUser']['user_id']): ?>
<?php if (( isset ( $this->_aVars['aUser']['is_friend_request'] ) && $this->_aVars['aUser']['is_friend_request'] == 2 )): ?>
                        <div class="dropdown pending-request">
                            <a class="btn btn-default btn-round" data-toggle="dropdown">
                                <span class="ico ico-clock-o mr-1"></span>
<?php echo _p('pending_friend_request'); ?>
                                <span class="ico ico-caret-down ml-1"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="item-delete">
                                    <a href="javascript:void(0)" onclick="$.ajaxCall('friend.removePendingRequest', 'id=<?php echo $this->_aVars['aUser']['is_friend_request_id']; ?>','GET');">
                                        <span class="ico ico-ban mr-1"></span>
<?php echo _p('cancel_request'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
<?php endif; ?>
				
				    <div class="profile-action-block profile-viewer-actions dropdown">
<?php if (Phpfox ::isUser() && Phpfox ::isModule('friend') && ! $this->_aVars['aUser']['is_friend'] && $this->_aVars['aUser']['is_friend_request'] !== 2): ?>
<?php if (! $this->_aVars['aUser']['is_friend'] && $this->_aVars['aUser']['is_friend_request'] === 3): ?>
                                <a class="btn btn-primary btn-icon btn-gradient btn-round add_as_friend_button" href="#" onclick="return $Core.addAsFriend('<?php echo $this->_aVars['aUser']['user_id']; ?>');" title="<?php echo _p('add_to_friends'); ?>">
                                    <span class="ico ico-user2-check-o"></span>
                                    <span class=""><?php echo _p('confirm_friend_request'); ?></span>
                                </a>
<?php elseif (Phpfox ::getUserParam('friend.can_add_friends')): ?>
                                <a class="btn btn-primary btn-icon btn-gradient btn-round add_as_friend_button" href="#" onclick="return $Core.addAsFriend('<?php echo $this->_aVars['aUser']['user_id']; ?>');" title="<?php echo _p('add_to_friends'); ?>">
                                    <span class="ico ico-user1-plus-o"></span>
                                    <span class=""><?php echo _p('add_to_friends'); ?></span>
                                </a>
<?php endif; ?>
<?php endif; ?>

<?php if (Phpfox ::isAppActive('Core_Messages') && User_Service_Privacy_Privacy ::instance()->hasAccess('' . $this->_aVars['aUser']['user_id'] . '' , 'mail.send_message' ) && ( ! Phpfox ::getUserParam('mail.restrict_message_to_friends') || ( Phpfox ::getUserParam('mail.restrict_message_to_friends') && isset ( $this->_aVars['aUser']['is_friend'] ) && $this->_aVars['aUser']['is_friend'] === true ) )): ?>
                            <a class="btn btn-default btn-icon btn-round" href="#" onclick="$Core.composeMessage({user_id: <?php echo $this->_aVars['aUser']['user_id']; ?>}); return false;">
                                <span class="ico ico-comment-o"></span>
                                <span class=""><?php echo _p('message'); ?></span>
                            </a>
<?php endif; ?>

<?php (($sPlugin = Phpfox_Plugin::get('profile.template_block_menu_more')) ? eval($sPlugin) : false); ?>

<?php if (( Phpfox ::getUserBy('profile_page_id') <= 0 ) && ( ( Phpfox ::getUserParam('user.can_block_other_members') && isset ( $this->_aVars['aUser']['user_group_id'] ) && Phpfox ::getUserGroupParam('' . $this->_aVars['aUser']['user_group_id'] . '' , 'user.can_be_blocked_by_others' ) ) || ( Phpfox ::isAppActive('Core_Activity_Points') && Phpfox ::getUserParam('activitypoint.can_gift_activity_points')) || ( Phpfox ::isModule('friend') && Phpfox ::getUserParam('friend.link_to_remove_friend_on_profile') && isset ( $this->_aVars['aUser']['is_friend'] ) && $this->_aVars['aUser']['is_friend'] === true ) || ( $this->_aVars['bCanPoke'] && User_Service_Privacy_Privacy ::instance()->hasAccess('' . $this->_aVars['aUser']['user_id'] . '' , 'poke.can_send_poke' ) ) || ( Phpfox ::isUser() && $this->_aVars['aUser']['user_id'] != Phpfox ::getUserId()) || ( ! empty ( $this->_aVars['bShowRssFeedForUser'] ) ) )): ?>
                            <div class="dropup btn-group">
                                <a class="btn" title="<?php echo _p('more'); ?>" data-toggle="dropdown" role="button">
                                    <span class="ico ico-dottedmore-o"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
<?php if ($this->_aVars['bCanPoke'] && User_Service_Privacy_Privacy ::instance()->hasAccess('' . $this->_aVars['aUser']['user_id'] . '' , 'poke.can_send_poke' )): ?>
                                    <li>
                                        <a class="inlinePopup" href="#" id="section_poke" onclick="$Core.box('poke.poke', 400, 'user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>'); return false;">
                                            <i class="ico ico-smile-o"></i>
                                            <span class="" ><?php echo _p('poke', array('full_name' => '')); ?></span>
                                        </a>
                                    </li>
<?php endif; ?>

<?php if (Phpfox ::getUserParam('user.can_block_other_members') && isset ( $this->_aVars['aUser']['user_group_id'] ) && Phpfox ::getUserGroupParam('' . $this->_aVars['aUser']['user_group_id'] . '' , 'user.can_be_blocked_by_others' )): ?>
                                    <li>
                                        <a href="#?call=user.block&amp;height=120&amp;width=400&amp;user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>" class="inlinePopup js_block_this_user" title="<?php if ($this->_aVars['bIsBlocked']):  echo _p('unblock_this_user');  else:  echo _p('block_this_user');  endif; ?>"><span class="ico ico-ban mr-1"></span><?php if ($this->_aVars['bIsBlocked']):  echo _p('unblock_this_user');  else:  echo _p('block_this_user');  endif; ?></a>
                                    </li>
<?php endif; ?>

<?php if (Phpfox ::isAppActive('Core_Activity_Points') && Phpfox ::getUserParam('activitypoint.can_gift_activity_points')): ?>
                                    <li>
                                        <a href="#?call=core.showGiftPoints&amp;height=120&amp;width=400&amp;user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>" class="inlinePopup js_gift_points" title="<?php echo _p('gift_points'); ?>">
                                            <span class="ico ico-gift-o mr-1"></span>
<?php echo _p('gift_points'); ?>
                                        </a>
                                    </li>
<?php endif; ?>
<?php if (Phpfox ::isUser() && $this->_aVars['aUser']['user_id'] != Phpfox ::getUserId()): ?>
                                    <li>
                                        <a href="#?call=report.add&amp;height=220&amp;width=400&amp;type=user&amp;id=<?php echo $this->_aVars['aUser']['user_id']; ?>" class="inlinePopup" title="<?php echo _p('report_this_user'); ?>">
                                            <span class="ico ico-warning-o mr-1"></span>
<?php echo _p('report_this_user'); ?></a>
                                    </li>
<?php endif; ?>
<?php if (isset ( $this->_aVars['bShowRssFeedForUser'] )): ?>
                                        <li>
                                            <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl(''.$this->_aVars['aUser']['user_name'].'.rss'); ?>" class="no_ajax_link">
                                                <span class="ico ico-rss-o mr-1"></span>
<?php echo _p('subscribe_via_rss'); ?>
                                            </a>
                                        </li>
<?php endif; ?>
<?php if (Phpfox ::isModule('friend') && Phpfox ::getUserParam('friend.link_to_remove_friend_on_profile') && isset ( $this->_aVars['aUser']['is_friend'] ) && $this->_aVars['aUser']['is_friend'] === true): ?>
                                    <li class="item-delete">
                                        <a href="#" onclick="$Core.jsConfirm({}, function(){$.ajaxCall('friend.delete', 'friend_user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>&reload=1');}, function(){}); return false;">
                                            <span class="ico ico-close-circle-o mr-1"></span>
<?php echo _p('remove_friend'); ?>
                                        </a>
                                    </li>
<?php endif; ?>
<?php (($sPlugin = Phpfox_Plugin::get('profile.template_block_menu')) ? eval($sPlugin) : false); ?>
                                </ul>
                            </div>
<?php endif; ?>
				    </div>
<?php if (Phpfox ::getUserParam('user.can_feature')): ?>
                        <div class="btn-group dropup btn-gear">
                            <a class="btn" title="<?php echo _p('options'); ?>" data-toggle="dropdown">
                                <span class="ico ico-gear-o"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
<?php if (Phpfox ::getUserParam('user.can_feature')): ?>
                                    <li <?php if (! isset ( $this->_aVars['aUser']['is_featured'] ) || ( isset ( $this->_aVars['aUser']['is_featured'] ) && ! $this->_aVars['aUser']['is_featured'] )): ?> style="display:none;" <?php endif; ?> class="user_unfeature_member">
                                        <a href="#" title="<?php echo _p('un_feature_this_member'); ?>" onclick="$(this).parent().hide(); $(this).parents('.dropdown-menu').find('.user_feature_member:first').show(); $.ajaxCall('user.feature', 'user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>&amp;feature=0&amp;type=1&reload=1'); return false;"><span class="ico ico-diamond-o mr-1"></span><?php echo _p('unfeature'); ?></a>
                                    </li>
                                    <li <?php if (isset ( $this->_aVars['aUser']['is_featured'] ) && $this->_aVars['aUser']['is_featured']): ?> style="display:none;" <?php endif; ?> class="user_feature_member">
                                        <a href="#" title="<?php echo _p('feature_this_member'); ?>" onclick="$(this).parent().hide(); $(this).parents('.dropdown-menu').find('.user_unfeature_member:first').show(); $.ajaxCall('user.feature', 'user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>&amp;feature=1&amp;type=1&reload=1'); return false;"><span class="ico ico-diamond-o mr-1"></span><?php echo _p('feature'); ?></a>
                                    </li>
<?php endif; ?>
                            </ul>
                        </div>
<?php endif; ?>
<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div class="profiles-menu set_to_fixed" data-class="profile_menu_is_fixed">
	<ul data-component="menu">
		<div class="overlay"></div>
		<li class="profile-image-holder hidden">
            <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aUser']['user_name']); ?>">
<?php echo $this->_aVars['sProfileImage']; ?>
            </a>
		</li>
		<li>
			<a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aUser']['user_name']); ?>" class="<?php if ($this->_aVars['sModule'] == ''): ?>active<?php endif; ?>">
				<span class="ico ico-user-circle-o"></span>
<?php echo _p('profile'); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl(''.$this->_aVars['aUser']['user_name'].'.info'); ?>" class="<?php if ($this->_aVars['sModule'] == 'info'): ?>active<?php endif; ?>">
				<span class="ico ico-user1-text-o"></span>
<?php echo _p('info'); ?>
			</a>
		</li>
<?php if (Phpfox ::isModule('friend')): ?>
		    <li class="">
				<a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl(''.$this->_aVars['aUser']['user_name'].'.friend'); ?>" class="<?php if ($this->_aVars['sModule'] == 'friend'): ?>active<?php endif; ?>">
					<span class="ico ico-user1-two-o"></span>
					<span>
<?php if ($this->_aVars['aUser']['total_friend'] > 0): ?>
						<span><?php echo $this->_aVars['aUser']['total_friend']; ?></span>
<?php endif; ?>
<?php echo _p('friends'); ?>
					</span>
				</a>
			</li>
<?php endif; ?>
<?php if ($this->_aVars['aProfileLinks']): ?>
<?php if (count((array)$this->_aVars['aProfileLinks'])):  foreach ((array) $this->_aVars['aProfileLinks'] as $this->_aVars['aProfileLink']): ?>
                <li class="">
                    <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aProfileLink']['url']); ?>" class="ajax_link <?php if (isset ( $this->_aVars['aProfileLink']['is_selected'] )): ?>active<?php endif; ?>">
<?php if (! empty ( $this->_aVars['aProfileLink']['icon_class'] )): ?>
                            <span class="<?php echo $this->_aVars['aProfileLink']['icon_class']; ?> mr-1"></span>
<?php else: ?>
                            <span class="ico ico-box-o mr-1"></span>
<?php endif; ?>
                            <span>
<?php if (isset ( $this->_aVars['aProfileLink']['total'] )): ?>
                                <span class="badge_number_inline hide"><?php echo number_format($this->_aVars['aProfileLink']['total']); ?></span>
<?php endif; ?>
<?php echo $this->_aVars['aProfileLink']['phrase']; ?>
                            </span>
<?php if (isset ( $this->_aVars['aProfileLink']['total'] )): ?><span class="badge_number"><?php echo number_format($this->_aVars['aProfileLink']['total']); ?></span><?php endif; ?>
                    </a>
                </li>
<?php endforeach; endif; ?>
<?php endif; ?>
		<li class="dropdown dropdown-overflow hide explorer">
            <a data-toggle="dropdown" role="button">
                <span class="ico ico-caret-down"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
            </ul>
        </li>
	</ul>
    <?php
						Phpfox::getLib('template')->getBuiltFile('core.block.actions-buttons');
						?>
</div>

<div class="js_cache_check_on_content_block" style="display:none;"></div>
<div class="js_cache_profile_id" style="display:none;"><?php echo $this->_aVars['aUser']['user_id']; ?></div>
<div class="js_cache_profile_user_name" style="display:none;"><?php if (isset ( $this->_aVars['aUser']['user_name'] )):  echo $this->_aVars['aUser']['user_name'];  endif; ?></div>

