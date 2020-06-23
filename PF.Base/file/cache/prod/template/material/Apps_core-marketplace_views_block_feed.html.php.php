<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php
	
?>

<div class="item-container market-app feed">
	<article>
			<div class="item-outer flex">
				<div class="item-media">
					<a href="<?php echo $this->_aVars['aListing']['url']; ?>" style="background-image: url(
<?php if (! empty ( $this->_aVars['aListing']['image_path'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aListing']['server_id'],'title' => $this->_aVars['aListing']['title'],'path' => 'marketplace.url_image','file' => $this->_aVars['aListing']['image_path'],'suffix' => '_200_square','return_url' => true)); ?>
<?php else: ?>
<?php echo Phpfox::getParam('marketplace.marketplace_default_photo'); ?>
<?php endif; ?>
					)"></a>
				</div>
				<div class="item-inner overflow">
					<a href="<?php echo $this->_aVars['aListing']['url']; ?>" class="item-title"><?php echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aListing']['title']), 100, '...'), 25); ?></a>
					<div class="item-price">
<?php if ($this->_aVars['aListing']['price'] == '0.00'): ?>
<?php echo _p('free'); ?>
<?php else: ?>
<?php echo Phpfox::getService('core.currency')->getSymbol($this->_aVars['aListing']['currency_id']);  echo number_format($this->_aVars['aListing']['price'], 2); ?>
<?php endif; ?>
					</div>
                    <div class="item-category">
<?php if (! empty ( $this->_aVars['aListing']['country_iso'] )): ?>
                                <a class="js_hover_title" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('marketplace', array('location' => $this->_aVars['aListing']['country_iso'])); ?>">
<?php echo Phpfox::getService('core.country')->getCountry($this->_aVars['aListing']['country_iso']); ?>
                                    <span class="js_hover_info">
<?php echo $this->_aVars['aListing']['location']; ?>
                                    </span>
                                </a>
<?php else: ?>
                                <a href="https://maps.google.com/?q=<?php echo $this->_aVars['aListing']['location']; ?>" target="_blank">
<?php echo $this->_aVars['aListing']['location']; ?>
                                </a>
<?php endif; ?>
                        &nbsp;.&nbsp;<span><?php echo _p('category'); ?>: <?php echo Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getService('core.category')->displayLinks($this->_aVars['aListing']['categories']), 64, '...'); ?></span>
					</div>
					<div class="item-description item_view_content"><?php echo Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('parse.output')->feedStrip(Phpfox::getLib('phpfox.parse.bbcode')->stripCode($this->_aVars['aListing']['mini_description'])), 55), 100); ?></div>
				</div>
		</div>
	</article>
</div>
