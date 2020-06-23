<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 22, 2020, 5:14 pm */ ?>
<?php

?>
<article class="p-fevent-app core-feed-item" data-url="<?php echo $this->_aVars['link']; ?>">
    <div class="item-media-banner">
        <a class="item-media" href="<?php echo $this->_aVars['link']; ?>">
            <span class="item-media-src" style="background-image: url('<?php if (! empty ( $this->_aVars['aItem']['image_path'] )):  echo Phpfox::getLib('phpfox.image.helper')->display(array('return_url' => true,'server_id' => $this->_aVars['aItem']['server_id'],'title' => $this->_aVars['aItem']['title'],'path' => 'event.url_image','file' => $this->_aVars['aItem']['image_path']));  else:  echo $this->_aVars['defaultImage'];  endif; ?>');"  alt="<?php echo $this->_aVars['aItem']['title']; ?>"></span>
        </a>
    </div>
    <div class="item-outer">
        <div class="item-calendar">
            <div class="item-date"><?php echo $this->_aVars['aItem']['d_day']; ?></div>
            <div class="item-month"><?php echo $this->_aVars['aItem']['d_month']; ?></div>
        </div>
        <div class="item-inner">
            <div class="item-title">
                <a href="<?php echo $this->_aVars['link']; ?>" class="core-feed-title line-1"><?php echo $this->_aVars['aItem']['title']; ?></a>
            </div>
            <div class="item-wrapper-info">
                <div class="item-side-left">
<?php if (! empty ( $this->_aVars['location'] )): ?>
                    <div class="item-location core-feed-description line-1">
<?php echo $this->_aVars['location']; ?>
                    </div>
<?php endif; ?>
                    <div class="item-info core-feed-description">
                        <span class="item-time">
<?php echo $this->_aVars['aItem']['date_formatted']; ?>
                        </span>
                        <span class="item-total-guest">
                            <a href="javascript:void(0);" data-event-id="<?php echo $this->_aVars['aItem']['event_id']; ?>" data-text="<?php echo _p('guest_list'); ?>" onclick="P_AdvEvent.showTabAttendingPeople(this); return false;">
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
                            </a>
                        </span>
                    </div>
                </div>
                <div class="item-side-right">
                    <div class="item-action js_rsvp_action_list_<?php echo $this->_aVars['aItem']['event_id']; ?>">
                        <?php
						Phpfox::getLib('template')->getBuiltFile('fevent.block.rsvp-action');
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

