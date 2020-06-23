<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php

?>

<div class="ynfeed-share-list">
    <ul class="dropdown-menu ynfeed-dropdown-share-list">
        <li>
            <a href="javascript:void(0);" class="ynfeed-share-share"><span class="ico ico-share-o"></span><?php echo _p('share'); ?>...</a>
        </li>
        <li role="separator" class="divider"></li>
<?php if (count((array)$this->_aVars['aShareServices'])):  $this->_aPhpfoxVars['iteration']['fkey'] = 0;  foreach ((array) $this->_aVars['aShareServices'] as $this->_aVars['sProvider'] => $this->_aVars['aService']):  $this->_aPhpfoxVars['iteration']['fkey']++; ?>

            <li<?php if ($this->_aPhpfoxVars['iteration']['fkey'] > 3): ?> style="display: none;"<?php endif; ?>>
                <a href="javascript:void(0);" class="ynfeed-share" data-surl="<?php echo $this->_aVars['aFeed']['feed_link']; ?>" data-provider="<?php echo $this->_aVars['sProvider']; ?>" data-title="<?php echo strip_tags($this->_aVars['aFeed']['feed_status']); ?>">
<?php if ($this->_aVars['sProvider'] == 'tumblr'): ?>
                        <span class="ico">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 24 24"  xml:space="preserve">
								<path class="st0" d="M15.2,16.8v-1.6c-0.5,0.4-1,0.5-1.6,0.5c-0.2,0-0.5-0.1-0.8-0.2c-0.2-0.1-0.3-0.3-0.3-0.4
									c-0.1-0.2-0.1-0.6-0.1-1.2v-2.6h2.4V9.6h-2.4V7H11c-0.1,0.6-0.2,1.1-0.4,1.3C10.4,8.7,10.2,9,9.9,9.2c-0.3,0.3-0.7,0.5-1,0.6v1.5H10
									v3.6c0,0.4,0.1,0.8,0.2,1.1c0.1,0.2,0.3,0.5,0.5,0.7c0.2,0.2,0.5,0.4,0.9,0.5c0.5,0.1,0.9,0.2,1.2,0.2c0.4,0,0.8,0,1.2-0.1
									C14.4,17.1,14.8,17,15.2,16.8"/>
								<path class="st0" d="M12,2.5c5.2,0,9.5,4.3,9.5,9.5s-4.3,9.5-9.5,9.5S2.5,17.2,2.5,12S6.8,2.5,12,2.5 M12,0.5
									C5.7,0.5,0.5,5.7,0.5,12c0,6.3,5.2,11.5,11.5,11.5c6.3,0,11.5-5.2,11.5-11.5C23.5,5.7,18.3,0.5,12,0.5L12,0.5z"/>
								<path d="M12,3.1"/>
								</svg></span><?php echo $this->_aVars['aService']['label']; ?>
<?php else: ?>
                    <span class="ico <?php echo $this->_aVars['aService']['icon']; ?>"></span><?php echo $this->_aVars['aService']['label']; ?>
<?php endif; ?>
                </a>
            </li>
<?php if ($this->_aPhpfoxVars['iteration']['fkey'] == 3): ?>
                <li>
                    <a class="ynfeed-share-more"><?php echo _p('more...'); ?></a>
                </li>
<?php endif; ?>
<?php endforeach; endif; ?>
    </ul>
</div>
