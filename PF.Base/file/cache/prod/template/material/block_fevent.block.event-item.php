<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
<div class="p-item p-fevent-item <?php if (! empty ( $this->_aVars['aItem']['attending_statistic']['people'] )): ?>has-list-member<?php endif; ?>">
    <div class="item-outer">
<?php if (! empty ( $this->_aVars['canDoModeration'] )): ?>
        <div class="moderation_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="<?php echo $this->_aVars['aItem']['event_id']; ?>" id="check<?php echo $this->_aVars['aItem']['event_id']; ?>" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
<?php endif; ?>
        <div class="p-item-media-wrapper p-margin-default p-fevent-item-media ">
            <a href="<?php if (! empty ( $this->_aVars['aItem']['sponsor_id'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl('ad.sponsor', array('view' => $this->_aVars['aItem']['sponsor_id']));  else:  echo Phpfox::permalink('fevent', $this->_aVars['aItem']['event_id'], $this->_aVars['aItem']['title'], false, null, (array) array (
));  endif; ?>" class="item-media-link">
                <span class="item-media-src" style="background-image: url('<?php echo $this->_aVars['aItem']['image_path']; ?>');"></span>
<?php if (! $this->_aVars['hideStatus']): ?>
                <div class="p-fevent-label-status-container">
<?php if ($this->_aVars['aItem']['d_type'] == 'past'): ?>
                    <span class="p-label-status solid danger ">
                            <span class="p-text-uppercase"><?php echo _p('end'); ?></span>
                        </span>
<?php endif; ?>
<?php if ($this->_aVars['aItem']['d_type'] == 'ongoing'): ?>
                    <span class="p-label-status solid success ">
                            <span class="p-text-uppercase"><?php echo _p('ongoing'); ?></span>
                         </span>
<?php endif; ?>
<?php if ($this->_aVars['aItem']['d_type'] == 'upcoming' && false): ?>
                    <span class="p-label-status solid warning ">
                            <span class="p-text-uppercase"><?php echo _p('upcoming'); ?></span>
                        </span>
<?php endif; ?>
                </div>
<?php endif; ?>


                <div class="p-item-flag-wrapper js_status_icon_<?php echo $this->_aVars['aItem']['event_id']; ?>">
                    <?php
						Phpfox::getLib('template')->getBuiltFile('fevent.block.status-icon');
						?>
                </div>

            </a>
        </div>
        <div class="item-inner">
        	<div class="p-fevent-item-time-listing">
        		<div class="item-start p-text-info">
<?php echo $this->_aVars['aItem']['date_formatted']; ?>
                </div>
        	</div>
        	<div class="p-fevent-item-title-wrapper">
        		<h4 class="p-item-title truncate-2">
                    <a href="<?php if (! empty ( $this->_aVars['aItem']['sponsor_id'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl('ad.sponsor', array('view' => $this->_aVars['aItem']['sponsor_id']));  else:  echo Phpfox::permalink('fevent', $this->_aVars['aItem']['event_id'], $this->_aVars['aItem']['title'], false, null, (array) array (
));  endif; ?>" class="">
                        <span><?php echo $this->_aVars['aItem']['title']; ?></span>
                    </a>
                </h4>
        		<div class="item-side-action p-fevent-item-option-wrapper">
                    <div class="p-fevent-action-member-wrapper">
	                    <div class="p-fevent-action-btn js_rsvp_action_list_<?php echo $this->_aVars['aItem']['event_id']; ?>">
	                        <?php
						Phpfox::getLib('template')->getBuiltFile('fevent.block.rsvp-action');
						?>
	                    </div>
	                    <div class="p-fevent-member-list-component p-fevent-listing-hidden-on-grid p-hidden-side-block">
<?php if (! empty ( $this->_aVars['aItem']['attending_statistic']['people'] )): ?>
<?php if (count((array)$this->_aVars['aItem']['attending_statistic']['people'])):  foreach ((array) $this->_aVars['aItem']['attending_statistic']['people'] as $this->_aVars['attending_person']): ?>
                                <div class="item-member">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['attending_person'],'suffix' => '_200_square')); ?>
                                </div>
<?php endforeach; endif; ?>
<?php endif; ?>
<?php if (! empty ( $this->_aVars['aItem']['attending_statistic']['other_people'] )): ?>
	                        <div class="item-more">
	                            <a href="javascript:void(0);" onclick="P_AdvEvent.showTabAttendingPeople(this); return false;" data-event-id="<?php echo $this->_aVars['aItem']['event_id']; ?>" data-text="<?php echo _p('fevent.friend_list'); ?>" data-statistic="1">+<?php echo $this->_aVars['aItem']['attending_statistic']['other_people']; ?></a>
	                        </div>
<?php endif; ?>
	                    </div>
	                </div>
<?php if ($this->_aVars['aItem']['can_do_action']): ?>
                    <div class="dropdown p-hidden-side-block">
                        <span class="p-option-button dropdown-toggle" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?php
						Phpfox::getLib('template')->getBuiltFile('fevent.block.action-link');
						?>
                        </ul>
                    </div>
<?php endif; ?>
                </div>
        	</div>

            <div class="p-fevent-item-info-wrapper p-seperate-dot-wrapper-inline p-item-minor-info">
<?php if ($this->_aVars['aItem']['has_ticket']): ?>
                <span class="p-seperate-dot-item-inline item-ticket ">
                    <span class="item-ticket-title p-text-capitalize"><?php echo _p('fevent.ticket_price'); ?>:</span>
                    <span class="item-ticket-number">
<?php if ($this->_aVars['aItem']['ticket_type'] == 'free'): ?>
<?php echo _p('free'); ?>
<?php else: ?>
<?php echo $this->_aVars['aItem']['ticket_price']; ?>
<?php endif; ?>
                    </span>
                </span>
<?php endif; ?>
                <span class="p-seperate-dot-item-inline p-seperate-dot-wrapper-inline item-wrapper-truncate">
	            	<span class="p-seperate-dot-item-inline item-member p-item-statistic">
<?php if (empty ( $this->_aVars['aItem']['attending_statistic']['total_friend_attending'] )): ?>
                            <span><?php echo $this->_aVars['aItem']['attending_statistic']['total_attending']; ?></span>
                            <span class="p-text-lowercase">
<?php if ($this->_aVars['aItem']['attending_statistic']['total_attending'] == 1): ?>
<?php echo _p('fevent.person'); ?>
<?php else: ?>
<?php echo _p('fevent.people'); ?>
<?php endif; ?>
                            </span>
<?php else: ?>
                            <span><?php echo $this->_aVars['aItem']['attending_statistic']['total_friend_attending']; ?></span>
                            <span class="p-text-lowercase">
<?php if ($this->_aVars['aItem']['attending_statistic']['total_friend_attending'] == 1): ?>
<?php echo _p('fevent_friend'); ?>
<?php else: ?>
<?php echo _p('fevent_friends'); ?>
<?php endif; ?>
                            </span>
<?php if (! empty ( $this->_aVars['aItem']['attending_statistic']['total_other_people_attending'] )): ?>
<?php echo _p('and'); ?><span> <?php echo $this->_aVars['aItem']['attending_statistic']['total_other_people_attending']; ?></span>
                            <span class="p-text-lowercase">
<?php if ($this->_aVars['aItem']['attending_statistic']['total_other_people_attending'] == 1): ?>
<?php echo _p('fevent_other'); ?>
<?php else: ?>
<?php echo _p('fevent_others'); ?>
<?php endif; ?>
                            </span>
<?php endif; ?>
<?php endif; ?>
	                </span>
<?php if (! empty ( $this->_aVars['aItem']['location_parsed'] )): ?>
                    <span class="p-seperate-dot-item-inline item-info p-fevent-listing-hidden-on-list">
	                    <span class="item-info-location"><?php echo $this->_aVars['aItem']['location_parsed']; ?></span>
                    </span>
<?php endif; ?>
	            </span>
            </div>

            <!-- //duplicate info for responsive layout -->
            <div class="p-item-description p-fevent-listing-hidden-on-grid p-fevent-item-description truncate-2 ">
<?php if (! empty ( $this->_aVars['aItem']['location_parsed'] )): ?>
                <span class="item-info-location"><?php echo $this->_aVars['aItem']['location_parsed']; ?></span>
<?php endif; ?>
<?php if (! empty ( $this->_aVars['aItem']['description_parsed'] )): ?>
                <span class="item-info-desc item_view_content"> - <?php echo Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(strip_tags($this->_aVars['aItem']['description_parsed'])), 200, '...'); ?></span>
<?php endif; ?>
            </div>
            <div class="item-author-wrapper p-item-minor-info p-hidden-side-block">
                <div class="item-author-info p-seperate-dot-wrapper">
                    <span class="item-author p-seperate-dot-item">
	                    <span class="p-text-capitalize"><?php echo _p('fevent.by'); ?></span> <?php echo '<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aItem']['user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aItem']['user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aItem']['user_name'], ((empty($this->_aVars['aItem']['user_name']) && isset($this->_aVars['aItem']['profile_page_id'])) ? $this->_aVars['aItem']['profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aItem']['user_id'], $this->_aVars['aItem']['full_name'])), 0) . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aItem']['user_id']) ? '' : '</a>') . '</span>'; ?>
                    </span>
<?php if (( int ) $this->_aVars['aItem']['isrepeat'] != -1): ?>
                    <span class="item-repeat-status p-seperate-dot-item">
<?php echo _p('repeat'); ?>: <?php echo $this->_aVars['aItem']['repeat_title']; ?>
                    </span>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

