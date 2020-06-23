<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php



?>
<div class="profile-block-action">
    <div class="item-inner">
        <div class="item-info-main">
            <div class="item-image">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aUser'],'suffix' => '_120_square')); ?>
            </div>
            <div class="item-info">
                <div class="item-title"><a href="<?php if (! empty ( $this->_aVars['aUser']['link'] )):  echo $this->_aVars['aUser']['link'];  else: ?>#<?php endif; ?>"><?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aUser']['full_name']); ?></a></div>
            </div>
<?php if ($this->_aVars['bIsFriend']): ?>
                <div class="dropdown item-btn-user">
                    <button class="btn btn-primary btn-round dropdown-toggle btn-icon" type="button" data-toggle="dropdown"><span class="ico ico-check"></span> <?php echo _p('friend'); ?> <span class="ico ico-caret-down"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a role="button" onclick="$Core.jsConfirm({}, function(){ $.ajaxCall('friend.delete', 'friend_user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>&reload=1');}, function(){}); return false;">
                                <span class="mr-1 ico ico-user2-del-o"></span>
<?php echo _p('remove_friend'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
<?php elseif ($this->_aVars['bIsRequest']): ?>
                <a href="javascript:void(0)" onclick="$.ajaxCall('friend.removePendingRequest', 'id=<?php echo $this->_aVars['aUser']['is_friend_request_id']; ?>','GET');" class="btn btn-sm btn-default btn-round">
                    <span class="ico ico-ban mr-1"></span>
<?php echo _p('cancel_request'); ?>
                </a>
<?php elseif (Phpfox ::getUserParam('friend.can_add_friends')): ?>
                <a href="#" onclick="return $Core.addAsFriend(<?php echo $this->_aVars['aUser']['user_id']; ?>);" title="<?php echo _p('add_as_friend'); ?>" class="btn btn-sm btn-primary btn-round add_as_friend_button">
                    <span class="mr-1 ico ico-user1-plus-o"></span> <?php echo _p('add_as_friend'); ?>
                </a>
<?php endif; ?>
        </div> 
    </div>
    <ul class="item-action">
<?php if (Phpfox ::getUserParam('user.can_block_other_members') && isset ( $this->_aVars['aUser']['user_group_id'] ) && Phpfox ::getUserGroupParam('' . $this->_aVars['aUser']['user_group_id'] . '' , 'user.can_be_blocked_by_others' )): ?>
            <li>
                <a href="#?call=user.block&amp;height=120&amp;width=400&amp;user_id=<?php echo $this->_aVars['aUser']['user_id']; ?>" class="inlinePopup js_block_this_user" title="<?php if ($this->_aVars['bIsBlocked']):  echo _p('unblock_this_user');  else:  echo _p('block_this_user');  endif; ?>">
                    <span class="ico ico-ban"></span>
<?php if ($this->_aVars['bIsBlocked']): ?>
<?php echo _p('unblock_this_user'); ?>
<?php else: ?>
<?php echo _p('block_this_user'); ?>
<?php endif; ?>
                </a>
            </li>
<?php endif; ?>
        <li>
            <a href="#?call=report.add&amp;height=220&amp;width=400&amp;type=user&amp;id=<?php echo $this->_aVars['aUser']['user_id']; ?>" class="inlinePopup" title="<?php echo _p('report_this_user'); ?>"><span class="ico ico-warning-o"></span><?php echo _p('report_this_user'); ?></a>
        </li>
<?php if (isset ( $this->_aVars['bShowRssFeedForUser'] )): ?>
            <li>
                <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl(''.$this->_aVars['aUser']['user_name'].'.rss'); ?>" class="no_ajax_link">
                    <span class="ico ico-rss-o"></span>
<?php echo _p('subscribe_via_rss'); ?>
                </a>
            </li>
<?php endif; ?>
    </ul>
</div>
