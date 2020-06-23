<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php

 if ($this->_aVars['rsvpActionType'] == 'list'): ?>
<?php if (isset ( $this->_aVars['aItem']['rsvp_id'] ) && ( $this->_aVars['aItem']['is_invited'] || $this->_aVars['aItem']['rsvp_id'] != 0 )): ?>
    <div class="dropdown js_rsvp_content p-fevent-action-simple-dropdown-style-wrapper" data-id="<?php echo $this->_aVars['aItem']['event_id']; ?>" data-phrase="<?php echo _p('maybe_attending'); ?>">
        <a data-toggle="dropdown" class="btn  btn-default btn-icon btn-sm">
            <span class="txt-label js_text_label">
<?php if ($this->_aVars['aItem']['rsvp_id'] == 1): ?>
                        <i class="ico ico-check-circle mr-1"></i><span class="item-text"><?php echo _p('attending'); ?></span>
<?php elseif ($this->_aVars['aItem']['rsvp_id'] == 2 || ( ! isset ( $this->_aVars['aItem']['rsvp_id'] ) || ( ! $this->_aVars['aItem']['is_invited'] && $this->_aVars['aItem']['rsvp_id'] == 0 ) )): ?>
                        <i class="ico ico-star mr-1"></i><span class="item-text"><?php echo _p('maybe_attending'); ?></span>
<?php elseif ($this->_aVars['aItem']['rsvp_id'] == 3): ?>
                        <i class="ico ico-ban mr-1"></i><span class="item-text"><?php echo _p('not_attending'); ?></span>
<?php elseif ($this->_aVars['aItem']['rsvp_id'] == 0 && $this->_aVars['aItem']['is_invited']): ?>
                        <i class="ico ico-question-circle-o"></i><span class="item-text"><?php echo _p('confirm'); ?></span>
<?php endif; ?>
            </span>
            <i class="ico ico-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li role="button">
                <a data-toggle="event_rsvp" rel="1"  <?php if (isset ( $this->_aVars['aItem']['rsvp_id'] ) && $this->_aVars['aItem']['rsvp_id'] == 1): ?>class="is_active_image"<?php endif; ?>>
                    <i class="ico ico-check-circle-o mr-1"></i><span class="item-text"><?php echo _p('attending'); ?></span>
                </a>
            </li>
            <li role="button">
                <a data-toggle="event_rsvp" rel="2" <?php if (isset ( $this->_aVars['aItem']['rsvp_id'] ) && $this->_aVars['aItem']['rsvp_id'] == 2): ?>class="is_active_image"<?php endif; ?>>
                    <i class="ico ico-star-o mr-1"></i><span class="item-text"><?php echo _p('maybe_attending'); ?></span>
                </a>
            </li>
<?php if (! $this->_aVars['aItem']['is_invited']): ?>
            <li role="separator" class="divider"></li>
<?php endif; ?>
            <li role="button">
                <a data-toggle="event_rsvp" rel="<?php if ($this->_aVars['aItem']['is_invited']): ?>3<?php else: ?>0<?php endif; ?>" <?php if (isset ( $this->_aVars['aItem']['rsvp_id'] ) && $this->_aVars['aItem']['rsvp_id'] == 3 && $this->_aVars['aItem']['is_invited']): ?>class="is_active_image"<?php endif; ?>>
                    <i class="ico ico-ban mr-1"></i><span class="item-text"><?php echo _p('not_attending'); ?></span>
                </a>
            </li>
        </ul>
    </div>
<?php else: ?>
    <div class="js_rsvp_content p-fevent-action-simple-style-wrapper" data-id="<?php echo $this->_aVars['aItem']['event_id']; ?>">
        <a class="btn btn-default btn-sm" data-toggle="event_rsvp" rel="2"><i class="ico ico-star-o mr-1"></i><span class="item-text"><?php echo _p('maybe_attending'); ?></span></a>
    </div>
<?php endif;  else: ?>
<?php if ($this->_aVars['aEvent']['rsvp_id'] != 0): ?>
        <div class="item-event-option-dropdown-wrapper">
            <div class="dropdown">
                <div data-toggle="dropdown" class="btn btn-default btn-sm">
                    <div>
<?php if ($this->_aVars['aEvent']['rsvp_id'] == 1): ?>
                        <i class="ico ico-check-circle mr-1"></i><?php echo _p('attending'); ?>
<?php elseif ($this->_aVars['aEvent']['rsvp_id'] == 2): ?>
                        <i class="ico ico-star mr-1"></i><?php echo _p('maybe_attending'); ?>
<?php elseif ($this->_aVars['aEvent']['rsvp_id'] == 3): ?>
                        <i class="ico ico-ban mr-1"></i><?php echo _p('not_attending'); ?>
<?php endif; ?>
                    </div>
                    <i class="ico ico-caret-down ml-1"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li class="item-event-option <?php if ($this->_aVars['aEvent']['rsvp_id'] == 1): ?>active<?php endif; ?>" data-rsvp="1">
                        <a href="javascript:void(0);"><i class="ico ico-check-circle-o"></i><?php echo _p('attending'); ?></a>
                    </li>
                    <li class="item-event-option <?php if ($this->_aVars['aEvent']['rsvp_id'] == 2): ?>active<?php endif; ?>" data-rsvp="2">
                        <a href="javascript:void(0);"><i class="ico ico-star-o"></i><?php echo _p('maybe_attending'); ?></a>
                    </li>
<?php if ($this->_aVars['aEvent']['is_invited']): ?>
                    <li class="item-event-option <?php if ($this->_aVars['aEvent']['rsvp_id'] == 3): ?>active<?php endif; ?>" data-rsvp="3">
                        <a href="javascript:void(0);"><i class="ico ico-ban"></i><?php echo _p('not_attending'); ?></a>
                    </li>
<?php else: ?>
                    <li role="separator" class="divider"></li>
                    <li class="item-event-option" data-rsvp="0">
                        <a href="javascript:void(0);"><?php echo _p('cancel'); ?></a>
                    </li>
<?php endif; ?>
                </ul>
            </div>
        </div>
<?php else: ?>
        <div class="item-event-option-wrapper <?php if ($this->_aVars['aEvent']['is_invited']): ?>has-invite<?php endif; ?>">
            <div class="item-event-option attending" data-rsvp="1">
                <span class="btn btn-sm btn-default btn-icon"><i class="ico ico-check-circle-o"></i><?php echo _p('attending'); ?></span>
            </div>

            <div class="item-event-option maybe_attending" data-rsvp="2">
                <span class="btn btn-sm btn-default btn-icon"><i class="ico ico-star-o"></i><?php echo _p('maybe_attending'); ?></span>
            </div>

<?php if ($this->_aVars['aEvent']['is_invited']): ?>
            <div class="item-event-option not_attending" data-rsvp="3">
                <span class="btn btn-sm btn-default btn-icon"><i class="ico ico-ban"></i><?php echo _p('not_attending'); ?></span>
            </div>
<?php endif; ?>
        </div>
<?php endif;  endif; ?>

