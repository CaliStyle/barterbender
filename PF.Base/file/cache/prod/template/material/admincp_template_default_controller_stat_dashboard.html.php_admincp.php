<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<div id="admincp_stat" class="block">
    <div class="content stats-me">
<?php if (count((array)$this->_aVars['aItems'])):  foreach ((array) $this->_aVars['aItems'] as $this->_aVars['aItem']): ?>
        <div class="stat-item clearfix">
            <div class="item-outer">
                <div class="item-icon">
                    <span class="<?php echo $this->_aVars['aItem']['icon']; ?>"></span>
                </div>
                <div class="item-info">
                    <div class="item-number"><?php echo number_format($this->_aVars['aItem']['value']); ?></div>
                    <div class="item-text"><?php echo $this->_aVars['aItem']['phrase']; ?></div>
                </div>
            </div>
        </div>
<?php endforeach; endif; ?>
<?php if (count ( $this->_aVars['aRemainItems'] )): ?>
            <div class="stat-item clearfix js_admincp_stat_more">
                <div class="item-outer">
                    <div class="item-icon">
                        <span class="ico ico-angle-double-down"></span>
                    </div>
                    <div class="item-info">
                        <div class="item-text"><?php echo _p('more'); ?></div>
                    </div>
                </div>
            </div>
<?php endif; ?>

<?php if (count((array)$this->_aVars['aRemainItems'])):  foreach ((array) $this->_aVars['aRemainItems'] as $this->_aVars['aItem']): ?>
        <div class="stat-item clearfix hide">
            <div class="item-outer">
                <div class="item-icon">
                    <span class="<?php echo $this->_aVars['aItem']['icon']; ?>"></span>
                </div>
                <div class="item-info">
                    <div class="item-number"><?php echo number_format($this->_aVars['aItem']['value']); ?></div>
                    <div class="item-text"><?php echo $this->_aVars['aItem']['phrase']; ?></div>
                </div>
            </div>
        </div>
<?php endforeach; endif; ?>
        <div class="stat-item clearfix hide js_admincp_stat_less">
            <div class="item-outer">
                <div class="item-icon">
                    <span class="ico ico-angle-double-up"></span>
                </div>
                <div class="item-info">
                    <div class="item-text"><?php echo _p('less'); ?></div>
                </div>
            </div>
        </div>

    </div>
</div>
