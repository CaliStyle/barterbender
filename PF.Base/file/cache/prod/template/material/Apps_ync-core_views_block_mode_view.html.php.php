<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
<?php if (! empty ( $this->_aVars['aViewModes'] ) && count ( $this->_aVars['aViewModes'] ) > 1): ?>
    <div class="p-mode-view-container" data-mode-view-default="<?php echo $this->_aVars['sModeViewDefault']; ?>" data-mode-view-id="<?php echo $this->_aVars['sModeViewId']; ?>">
<?php if (count((array)$this->_aVars['aViewModes'])):  $this->_aPhpfoxVars['iteration']['viewmodes'] = 0;  foreach ((array) $this->_aVars['aViewModes'] as $this->_aVars['sMode'] => $this->_aVars['aViewMode']):  $this->_aPhpfoxVars['iteration']['viewmodes']++; ?>

            <span class="p-mode-view-btn <?php echo $this->_aVars['sMode']; ?>" data-mode="<?php echo $this->_aVars['sMode']; ?>" <?php if ($this->_aVars['aViewMode']['callback_js']): ?>data-callback-js="<?php echo $this->_aVars['aViewMode']['callback_js']; ?>"<?php endif; ?> <?php if ($this->_aVars['aViewMode']['callback_data']): ?>data-callback-data="<?php echo $this->_aVars['aViewMode']['callback_data']; ?>"<?php endif; ?> title="<?php echo _p($this->_aVars['aViewMode']['title']); ?>">
                <i class="ico ico-<?php echo $this->_aVars['aViewMode']['icon']; ?>"></i>
            </span>
<?php endforeach; endif; ?>
    </div>
<?php endif; ?>
