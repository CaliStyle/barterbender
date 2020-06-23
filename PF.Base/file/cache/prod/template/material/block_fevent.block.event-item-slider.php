<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
<div class="item">
    <div class="p-item p-fevent-item">
        <div class="item-outer">
            <div class="p-item-media-wrapper p-margin-default p-fevent-item-media ">
                <a href="<?php if (! empty ( $this->_aVars['aItem']['sponsor_id'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl('ad.sponsor', array('view' => $this->_aVars['aItem']['sponsor_id']));  else:  echo Phpfox::permalink('fevent', $this->_aVars['aItem']['event_id'], $this->_aVars['aItem']['title'], false, null, (array) array (
));  endif; ?>" class="item-media-link">
                    <span class="item-media-src" style="background-image: url('<?php echo $this->_aVars['aItem']['image_path']; ?>');"></span>
                </a>
                <div class="p-fevent-label-status-container">
<?php if (! empty ( $this->_aVars['aItem']['d_repeat_time'] )): ?>
                    <div class="item-repeat">
                        <i class="ico ico-repeat-alt"></i>
                        <div class="item-title-hover">
<?php echo _p('repeat'); ?>: <?php echo $this->_aVars['aItem']['d_repeat_time']; ?>
                        </div>
                    </div>
<?php endif; ?>
                </div>
            </div>
            <div class="item-inner">
                <div class="item-inner-wrapper">
                    <div class="p-fevent-timer-component">
                        <span class="item-month"><?php echo Phpfox::getLib('phpfox.parse.output')->shorten($this->_aVars['aItem']['M_start_time'], 3); ?></span>
                        <span class="item-date"><?php echo $this->_aVars['aItem']['d_start_time']; ?></span>
                        <span class="item-time"><?php echo $this->_aVars['aItem']['short_start_time']; ?></span>
                    </div>
                    <div class="item-inner-info">
                        <h2 class="p-item-title ">
                            <a href="<?php if (! empty ( $this->_aVars['aItem']['sponsor_id'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl('ad.sponsor', array('view' => $this->_aVars['aItem']['sponsor_id']));  else:  echo Phpfox::permalink('fevent', $this->_aVars['aItem']['event_id'], $this->_aVars['aItem']['title'], false, null, (array) array (
));  endif; ?>" class="" >
                                <span><?php echo $this->_aVars['aItem']['title']; ?></span>
                            </a>
                        </h2>
                        <div class="item-info-wrapper">
                            <div class="item-side-info">
                                <div class="item-statistic-wrapper p-item-statistic p-seperate-dot-wrapper">
                                    <span class="p-seperate-dot-item item-guest p-fevent-slider-hide-on-grid">
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
<?php if ($this->_aVars['aItem']['total_like']): ?>
                                    <span class="p-seperate-dot-item item-like p-fevent-slider-hide-on-grid">
                                        <span><?php echo $this->_aVars['aItem']['total_like']; ?></span>
                                        <span class="p-text-lowercase">
<?php if ($this->_aVars['aItem']['total_like'] > 1): ?>
<?php echo _p('likes'); ?>
<?php else: ?>
<?php echo _p('like'); ?>
<?php endif; ?>
                                        </span>
                                    </span>
<?php endif; ?>
<?php if (( int ) $this->_aVars['aItem']['total_view'] > 0): ?>
                                    <span class="p-seperate-dot-item item-view">
                                        <span><?php echo $this->_aVars['aItem']['total_view']; ?></span>
                                        <span class="p-text-lowercase"><?php if (( int ) $this->_aVars['aItem']['total_view'] == 1):  echo _p('view');  else:  echo _p('views');  endif; ?></span>
                                    </span>
<?php endif; ?>

<?php if ($this->_aVars['aItem']['has_ticket']): ?>
                                    <span class="p-seperate-dot-item item-ticket p-fevent-slider-hide-on-list">
                                        <span class="item-ticket-title"><?php echo _p('fevent.ticket_price'); ?>:</span>
                                        <span class="item-ticket-number">
<?php if ($this->_aVars['aItem']['ticket_type'] == 'free'): ?>
                                                <span class="p-text-success fw-bold"><?php echo _p('free'); ?></span>
<?php else: ?>
<?php echo $this->_aVars['aItem']['ticket_price']; ?>
<?php endif; ?>
                                        </span>
                                    </span>
<?php endif; ?>
                                </div>
                                <div class="item-time-wrapper p-fevent-slider-hide-on-grid">
<?php if ($this->_aVars['isSlider']): ?>
                                    <div class="item-start p-text-info">
<?php echo $this->_aVars['aItem']['start_time_basic_information_time']; ?>
                                    </div>
                                    <div class="item-end">
<?php if (! in_array ( $this->_aVars['aItem']['d_type'] , array ( 'past' , 'ongoing' ) )):  echo _p('end'); ?>: <?php endif;  echo $this->_aVars['aItem']['end_time_basic_information_time']; ?>
                                    </div>
<?php else: ?>
<?php if (in_array ( $this->_aVars['dataSource'] , array ( 'ongoing' ) )): ?>
                                        <div class="item-end">
<?php echo _p('end'); ?>: <?php echo $this->_aVars['aItem']['end_time_basic_information_time']; ?>
                                        </div>
<?php else: ?>
                                        <div class="item-start p-text-info">
<?php echo $this->_aVars['aItem']['start_time_basic_information_time']; ?>
                                        </div>
<?php endif; ?>
<?php endif; ?>

                                </div>
                                <div class="item-ticket-price-listview p-fevent-slider-hide-on-grid">
                                    <div class="item-price">
<?php if ($this->_aVars['aItem']['has_ticket']): ?>
<?php if ($this->_aVars['aItem']['ticket_type'] == 'free'): ?>
                                                <span class="p-text-success fw-bold"><?php echo _p('free'); ?>/<?php echo _p('ticket'); ?></span>
<?php else: ?>
                                                <span class="p-text-warning fw-bold"><?php echo $this->_aVars['aItem']['ticket_price']; ?>/<?php echo _p('ticket'); ?></span>
<?php endif; ?>
<?php endif; ?>
                                    </div>
                                </div>
                                <div class="item-author-wrapper">
                                    <div class="item-author-image">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aItem'],'suffix' => '_50_square')); ?>
                                    </div>
                                    <div class="item-author-info">
                                        <span class="item-author">
                                            <span class="p-text-capitalize"><?php echo _p('fevent.by'); ?></span> <?php echo '<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aItem']['user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aItem']['user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aItem']['user_name'], ((empty($this->_aVars['aItem']['user_name']) && isset($this->_aVars['aItem']['profile_page_id'])) ? $this->_aVars['aItem']['profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aItem']['user_id'], $this->_aVars['aItem']['full_name'])), 0) . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aItem']['user_id']) ? '' : '</a>') . '</span>'; ?>
                                        </span>
                                        <span class="p-item-minor-info item-info"><?php echo $this->_aVars['aItem']['location'];  if ($this->_aVars['aItem']['address']): ?> <?php echo $this->_aVars['aItem']['address'];  endif;  if ($this->_aVars['aItem']['city']): ?> - <?php echo $this->_aVars['aItem']['city'];  endif; ?></span>
                                    </div>
                                </div>
                                <div class="item-description p-fevent-slider-hide-on-grid item_view_content">
<?php echo Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(strip_tags($this->_aVars['aItem']['description_parsed'])), 200, '...'); ?>
                                </div>
                            </div>
                            <div class="item-side-action">
                                <div class="p-fevent-member-list-component">
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
                                <div class="p-fevent-action-btn js_rsvp_action_list_<?php echo $this->_aVars['aItem']['event_id']; ?>"">
                                    <?php
						Phpfox::getLib('template')->getBuiltFile('fevent.block.rsvp-action');
						?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
