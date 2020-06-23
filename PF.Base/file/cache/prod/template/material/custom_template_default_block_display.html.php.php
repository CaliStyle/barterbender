<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php

?>

<?php if (count((array)$this->_aVars['aCustomMain'])):  foreach ((array) $this->_aVars['aCustomMain'] as $this->_aVars['aCustom']): ?>
<?php if ($this->_aVars['sTemplate'] == 'info'): ?>
<?php Phpfox::getBlock('custom.block', array('data' => $this->_aVars['aCustom'],'template' => $this->_aVars['sTemplate'],'edit_user_id' => $this->_aVars['aUser']['user_id'])); ?>
<?php else: ?>
<?php Phpfox::getBlock('custom.block', array('data' => $this->_aVars['aCustom'],'template' => $this->_aVars['sTemplate'],'edit_user_id' => $this->_aVars['aUser']['user_id'])); ?>
<?php endif;  endforeach; endif; ?>

