<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php

 if (empty ( $this->_aVars['feed_entry_be'] )): ?>
<?php if (Phpfox ::isModule('report') && isset ( $this->_aVars['aFeed']['report_module'] ) && ! Phpfox ::getService('user.block')->isBlocked(null, $this->_aVars['aFeed']['user_id'] )): ?>
<?php $this->assign('empty', false); ?>
    <li class="ynfeed_feed_option"><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type=<?php echo $this->_aVars['aFeed']['report_module']; ?>&amp;id=<?php echo $this->_aVars['aFeed']['item_id']; ?>" class="inlinePopup activity_feed_report" title="<?php echo _p('report'); ?>">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
<?php echo _p('report'); ?></a>
    </li>
<?php endif;  else: ?>
<?php if ($this->_aVars['aFeed']['type_id'] == "user_status" && ( ( Phpfox ::getUserParam('feed.can_edit_own_user_status') && $this->_aVars['aFeed']['user_id'] == Phpfox ::getUserId()) || Phpfox ::getUserParam('feed.can_edit_other_user_status'))): ?>
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="<?php echo _p('edit_feed'); ?>"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('<?php echo _p('edit_feed'); ?>', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id=<?php echo $this->_aVars['aFeed']['feed_id'];  if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>&module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&item_id=<?php echo $this->_aVars['aFeed']['callback']['item_id'];  if (isset ( $this->_aVars['aFeed']['user_id'] )): ?>&user_id=<?php echo $this->_aVars['aFeed']['user_id'];  endif;  endif; ?>')); return false;">
                <i class="fa fa-pencil-square-o"></i> <?php echo _p('edit_feed'); ?></a></li>
<?php endif; ?>

<?php if ($this->_aVars['aFeed']['type_id'] == 'pages_comment' && $this->_aVars['aFeed']['parent_user_id'] != 0 && ( $this->_aVars['aFeed']['user_id'] == Phpfox ::getUserId() || ( Phpfox ::getService('pages')->isAdmin($this->_aVars['aFeed']['parent_user_id'])))): ?>
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="<?php echo _p('edit_feed'); ?>"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('<?php echo _p('edit_feed'); ?>', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id=<?php echo $this->_aVars['aFeed']['feed_id'];  if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>&module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&item_id=<?php echo $this->_aVars['aFeed']['callback']['item_id'];  if (isset ( $this->_aVars['aFeed']['user_id'] )): ?>&user_id=<?php echo $this->_aVars['aFeed']['user_id'];  endif;  endif; ?>')); return false;">
                <i class="fa fa-pencil-square-o"></i> <?php echo _p('edit_feed'); ?></a></li>
<?php endif; ?>

<?php if ($this->_aVars['aFeed']['type_id'] == 'groups_comment' && $this->_aVars['aFeed']['parent_user_id'] != 0 && ( $this->_aVars['aFeed']['user_id'] == Phpfox ::getUserId() || ( Phpfox ::getService('groups')->isAdmin($this->_aVars['aFeed']['parent_user_id'])))): ?>
    <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="<?php echo _p('edit_feed'); ?>"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('<?php echo _p('edit_feed'); ?>', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id=<?php echo $this->_aVars['aFeed']['feed_id'];  if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>&module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&item_id=<?php echo $this->_aVars['aFeed']['callback']['item_id'];  if (isset ( $this->_aVars['aFeed']['user_id'] )): ?>&user_id=<?php echo $this->_aVars['aFeed']['user_id'];  endif;  endif; ?>')); return false;">
            <i class="fa fa-pencil-square-o"></i> <?php echo _p('edit_feed'); ?></a></li>
<?php endif; ?>

<?php if ($this->_aVars['aFeed']['type_id'] == 'feed_comment' && ( $this->_aVars['aFeed']['user_id'] == Phpfox ::getUserId() || Phpfox ::isAdmin())): ?>
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="<?php echo _p('edit_feed'); ?>"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('<?php echo _p('edit_feed'); ?>', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id=<?php echo $this->_aVars['aFeed']['feed_id'];  if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>&module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&item_id=<?php echo $this->_aVars['aFeed']['callback']['item_id'];  if (isset ( $this->_aVars['aFeed']['user_id'] )): ?>&user_id=<?php echo $this->_aVars['aFeed']['user_id'];  endif;  endif; ?>')); return false;">
                <i class="fa fa-pencil-square-o"></i> <?php echo _p('edit_feed'); ?></a></li>
<?php endif; ?>

<?php if ($this->_aVars['aFeed']['type_id'] == 'event_comment' && $this->_aVars['aFeed']['user_id'] == Phpfox ::getUserId()): ?>
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="<?php echo _p('edit_feed'); ?>"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('<?php echo _p('edit_feed'); ?>', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id=<?php echo $this->_aVars['aFeed']['feed_id'];  if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>&module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&item_id=<?php echo $this->_aVars['aFeed']['callback']['item_id'];  if (isset ( $this->_aVars['aFeed']['user_id'] )): ?>&user_id=<?php echo $this->_aVars['aFeed']['user_id'];  endif;  endif; ?>')); return false;">
                <i class="fa fa-pencil-square-o"></i> <?php echo _p('edit_feed'); ?></a></li>
<?php endif; ?>

<?php (($sPlugin = Phpfox_Plugin::get('ynfeed.template_block_entry_2')) ? eval($sPlugin) : false); ?>

<?php if ($this->_aVars['aFeed']['is_tagged']): ?>
    <li class="ynfeed_feed_option"><a href="#" class="" title="<?php echo _p('remove_tag_from_feed'); ?>" onclick="$Core.ynfeed.removeTag('feed_id=<?php echo $this->_aVars['aFeed']['feed_id']; ?>&user_id=<?php echo $this->_aVars['aFeed']['user_id']; ?>&feed_item_id=<?php echo $this->_aVars['aFeed']['item_id']; ?>&feed_item_type=<?php echo $this->_aVars['aFeed']['type_id'];  if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>&module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&item_id=<?php echo $this->_aVars['aFeed']['callback']['item_id'];  if (isset ( $this->_aVars['aFeed']['user_id'] )):  endif;  endif; ?>');return false;">
            <i class="fa fa-pencil-square-o"></i> <?php echo _p('remove_tag_from_feed'); ?></a></li>
<?php endif; ?>

<?php if (Phpfox ::getUserId() && ( ( isset ( $this->_aVars['aFeed']['is_tagged'] ) && $this->_aVars['aFeed']['is_tagged'] ) || ( $this->_aVars['aFeed']['user_id'] == Phpfox ::getUserId() || $this->_aVars['aFeed']['parent_user_id'] == Phpfox ::getUserId()))): ?>
<?php if (isset ( $this->_aVars['aFeed']['is_noti_off'] ) && $this->_aVars['aFeed']['is_noti_off']): ?>
    <li class="ynfeed_feed_option" id="ynfeed_btn_turnoff_noti_feed_<?php echo $this->_aVars['aFeed']['feed_id']; ?>"><a href="#" class="" title="<?php echo _p('turnon_notifications_for_this_feed'); ?>" onclick="$Core.ynfeed.turnonNotification(<?php echo $this->_aVars['aFeed']['feed_id']; ?>, <?php echo $this->_aVars['aFeed']['item_id']; ?>, '<?php echo $this->_aVars['aFeed']['type_id']; ?>');return false;">
            <i class="fa fa-bell"></i> <?php echo _p('turnon_notifications_for_this_feed'); ?></a></li>
<?php else: ?>
    <li class="ynfeed_feed_option" id="ynfeed_btn_turnoff_noti_feed_<?php echo $this->_aVars['aFeed']['feed_id']; ?>"><a href="#" class="" title="<?php echo _p('turnoff_notifications_for_this_feed'); ?>" onclick="$Core.ynfeed.turnoffNotification(<?php echo $this->_aVars['aFeed']['feed_id']; ?>, <?php echo $this->_aVars['aFeed']['item_id']; ?>, '<?php echo $this->_aVars['aFeed']['type_id']; ?>');return false;">
            <i class="fa fa-bell-slash"></i> <?php echo _p('turnoff_notifications_for_this_feed'); ?></a></li>
<?php endif; ?>
<?php endif; ?>
<?php $this->assign('empty', true); ?>

<?php if (Phpfox ::isModule('report') && isset ( $this->_aVars['aFeed']['report_module'] ) && $this->_aVars['aFeed']['user_id'] != Phpfox ::getUserId() && ! Phpfox ::getService('user.block')->isBlocked(null, $this->_aVars['aFeed']['user_id'] )): ?>
<?php $this->assign('empty', false); ?>
        <li class="ynfeed_feed_option"><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type=<?php echo $this->_aVars['aFeed']['report_module']; ?>&amp;id=<?php echo $this->_aVars['aFeed']['item_id']; ?>" class="inlinePopup activity_feed_report" title="<?php echo _p('report_feed'); ?>">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
<?php echo _p('report_feed'); ?></a>
        </li>
<?php endif; ?>
<?php if (Phpfox ::getService('ynfeed.filter')->isSavedFilterEnabled()): ?>
<?php if (isset ( $this->_aVars['aFeed']['is_saved'] ) && $this->_aVars['aFeed']['is_saved']): ?>
    <li class="ynfeed_feed_option" id="ynfeed_btn_save_feed_<?php echo $this->_aVars['aFeed']['feed_id']; ?>">
        <a href="javascript:void(0);" class="" title="<?php echo _p('unsave_feed'); ?>" onclick="$.ajaxCall('ynfeed.unsave','<?php if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&<?php endif; ?>id=<?php echo $this->_aVars['aFeed']['feed_id']; ?>') ;return false;">
            <i class="fa fa-bookmark" aria-hidden="true"></i> <?php echo _p('unsave_feed'); ?>
        </a>
    </li>
<?php else: ?>
    <li class="ynfeed_feed_option" id="ynfeed_btn_save_feed_<?php echo $this->_aVars['aFeed']['feed_id']; ?>">
        <a href="javascript:void(0);" class="" title="<?php echo _p('save_feed'); ?>" onclick="$.ajaxCall('ynfeed.save', '<?php if (isset ( $this->_aVars['aFeed']['callback']['module'] )): ?>module=<?php echo $this->_aVars['aFeed']['callback']['module']; ?>&<?php endif;  if (isset ( $this->_aVars['aFeed']['callback']['table_prefix'] )): ?>table_prefix=<?php echo $this->_aVars['aFeed']['callback']['table_prefix']; ?>&<?php endif;  if (isset ( $this->_aVars['aFeed']['type_id'] )): ?>type=<?php echo $this->_aVars['aFeed']['type_id']; ?>&<?php endif; ?>id=<?php echo $this->_aVars['aFeed']['feed_id']; ?>'); return false;">
            <i class="fa fa-bookmark-o" aria-hidden="true"></i> <?php echo _p('save_feed'); ?>
        </a>
    </li>
<?php endif; ?>
<?php endif; ?>

<?php if (Phpfox ::getUserId() && ( Phpfox ::getUserId() != $this->_aVars['aFeed']['user_id'] )): ?>
    <li class="ynfeed_feed_option" id="ynfeed_btn_hide_feed_<?php echo $this->_aVars['aFeed']['feed_id']; ?>">
        <a href="javascript:void(0);" class="" title="<?php echo _p('hide_feed'); ?>" onclick="$Core.ynfeed.prepareHideFeed([<?php echo $this->_aVars['aFeed']['feed_id']; ?>], []); $.ajaxCall('ynfeed.hideFeed', 'id=' + <?php echo $this->_aVars['aFeed']['feed_id']; ?>); return false;">
            <i class="fa fa-eye-slash" aria-hidden="true"></i> <?php echo _p('hide_feed'); ?>
        </a>
    </li>

<?php if (Phpfox ::getUserBy('profile_page_id') == 0): ?>
    <li class="ynfeed_feed_option" id="ynfeed_btn_hide_feed_<?php echo $this->_aVars['aFeed']['feed_id']; ?>">
        <a href="javascript:void(0);" class="" title="<?php echo _p('hide_all_from_somebody_regular', array('somebody' => $this->_aVars['aFeed']['full_name'])); ?>" onclick="$Core.ynfeed.prepareHideFeed([], [<?php echo $this->_aVars['aFeed']['user_id']; ?>]); $.ajaxCall('ynfeed.hideAllFromUser', 'id=' + <?php echo $this->_aVars['aFeed']['user_id']; ?>); return false;">
            <i class="fa fa-eye-slash" aria-hidden="true"></i> <?php echo _p('hide_all_from_somebody', array('somebody' => $this->_aVars['aFeed']['full_name'])); ?>
        </a>
    </li>
<?php endif; ?>
<?php endif; ?>


<?php if (( ( defined ( 'PHPFOX_FEED_CAN_DELETE' ) ) || ( Phpfox ::getUserParam('feed.can_delete_own_feed') && $this->_aVars['aFeed']['user_id'] == Phpfox ::getUserId()) || Phpfox ::getUserParam('feed.can_delete_other_feeds') || ( $this->_aVars['aFeed']['parent_user_id'] == Phpfox ::getUserId()) )): ?>
    <li class="ynfeed_feed_option item_delete"><a href="#" class="" title="<?php echo _p('delete_feed'); ?>"
        onclick="$Core.jsConfirm({}, function(){$.ajaxCall('ynfeed.delete', 'TB_inline=1&amp;type=delete&amp;id=<?php echo $this->_aVars['aFeed']['feed_id'];  if (isset ( $this->_aVars['aFeedCallback']['module'] )): ?>&amp;module=<?php echo $this->_aVars['aFeedCallback']['module']; ?>&amp;item=<?php echo $this->_aVars['aFeedCallback']['item_id'];  endif; ?>&amp;type_id=<?php echo $this->_aVars['aFeed']['type_id']; ?>');}, function(){}); return false;">
            <i class="fa fa-trash"></i> <?php echo _p('delete_feed'); ?></a></li>
<?php endif;  endif; ?>
