<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php

?>
<div class="block site-stat-container">
<?php if (count((array)$this->_aVars['aStats'])):  foreach ((array) $this->_aVars['aStats'] as $this->_aVars['sKey'] => $this->_aVars['aStatItem']): ?>
    <div class="site-stat-group">
        <div class="title">
<?php echo $this->_aVars['sKey']; ?>
        </div>
        <div class="content">
<?php if (count((array)$this->_aVars['aStatItem'])):  foreach ((array) $this->_aVars['aStatItem'] as $this->_aVars['aStat']): ?>
            <div class="info">
                <div class="info_left">
<?php echo $this->_aVars['aStat']['phrase']; ?>:
                </div>
                <div class="info_right">
<?php if (isset ( $this->_aVars['aStat']['link'] )): ?><a href="<?php echo $this->_aVars['aStat']['link']; ?>"><?php endif;  echo $this->_aVars['aStat']['value'];  if (isset ( $this->_aVars['aStat']['link'] )): ?></a><?php endif; ?>
                </div>
            </div>
<?php endforeach; endif; ?>
        </div>
    </div>
<?php endforeach; endif; ?>
</div>
