<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php if (( $this->_aVars['is_trial_mode'] )): ?>
<div class="block">
    <div class="title"<?php if (( $this->_aVars['expires'] <= 2 )): ?> style="background:red; color:#fff;" <?php endif; ?>>
<?php echo _p('phpFox Trial'); ?>
    <a href="https://www.phpfox.com/" target="_blank" class="purchase_trial"><?php echo _p('purchased'); ?></a>
</div>
<div class="content">
    <div class="info">
        <div class="info_left">
<?php echo _p('Expires'); ?>:
        </div>
        <div class="info_right">
<?php if ($this->_aVars['expires'] == 0): ?>
<?php echo _p('today'); ?>
<?php else: ?>
<?php echo $this->_aVars['expires']; ?> <?php if (( $this->_aVars['expires'] == '1' )):  echo _p('day');  else:  echo _p('days');  endif; ?>
<?php endif; ?>
        </div>
    </div>
</div>
</div>
<?php endif; ?>
