<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php
	
?>

<?php if (( count ( $this->_aVars['aListings'] ) )): ?>
	<div class="item-container market-app listing" id="collection-item-listings">
<?php if (count((array)$this->_aVars['aListings'])):  $this->_aPhpfoxVars['iteration']['listings'] = 0;  foreach ((array) $this->_aVars['aListings'] as $this->_aVars['aListing']):  $this->_aPhpfoxVars['iteration']['listings']++; ?>

            <?php
						Phpfox::getLib('template')->getBuiltFile('marketplace.block.rows');
						?>
<?php endforeach; endif; ?>
	</div>
<?php if (!isset($this->_aVars['aPager'])): Phpfox::getLib('pager')->set(array('page' => Phpfox::getLib('request')->getInt('page'), 'size' => Phpfox::getLib('search')->getDisplay(), 'count' => Phpfox::getLib('search')->getCount())); endif;  $this->getLayout('pager');  elseif (! PHPFOX_IS_AJAX): ?>
<?php echo _p('no_marketplace_listings_found');  endif; ?>

<?php if ($this->_aVars['bShowModerator']): ?>
<?php Phpfox::getBlock('core.moderation');  endif; ?>
