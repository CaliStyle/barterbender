<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
<?php
/**
 * [PHPFOX_HEADER]
 */


/**
 *
 *
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
 if (! PHPFOX_IS_AJAX): ?>
<?php Phpfox::getBlock('fevent.search', array()); ?>
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key=<?php echo $this->_aVars['apiKey']; ?>&libraries=places"></script>
<?php endif; ?>

<?php if (! $this->_aVars['bInHomepage']): ?>
<?php if (! count ( $this->_aVars['aEvents'] )): ?>
<?php if (! PHPFOX_IS_AJAX): ?>
    <div class="help-block">
<?php echo _p('fevent.no_events_found'); ?>
    </div>
<?php endif; ?>
<?php else: ?>
<?php if (! PHPFOX_IS_AJAX): ?>
    <div class="p-block">
        <div class="content">
<?php Phpfox::getBlock('ynccore.mode_view', array()); ?>
            <div class="p-listing-container p-fevent-listing-container col-4 p-mode-view has-list-one-column" data-mode-view="<?php echo $this->_aVars['sModeViewDefault']; ?>">
<?php endif; ?>
<?php if (count((array)$this->_aVars['aEvents'])):  $this->_aPhpfoxVars['iteration']['event'] = 0;  foreach ((array) $this->_aVars['aEvents'] as $this->_aVars['aItem']):  $this->_aPhpfoxVars['iteration']['event']++; ?>

                <?php
						Phpfox::getLib('template')->getBuiltFile('fevent.block.event-item');
						?>
<?php endforeach; endif; ?>
<?php if (!isset($this->_aVars['aPager'])): Phpfox::getLib('pager')->set(array('page' => Phpfox::getLib('request')->getInt('page'), 'size' => Phpfox::getLib('search')->getDisplay(), 'count' => Phpfox::getLib('search')->getCount())); endif;  $this->getLayout('pager'); ?>
<?php if (! PHPFOX_IS_AJAX && $this->_aVars['bShowModerator']): ?>
<?php Phpfox::getBlock('core.moderation'); ?>
<?php endif; ?>
<?php if (! PHPFOX_IS_AJAX): ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php endif;  endif; ?>

