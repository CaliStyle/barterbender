<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php 
    
?>
<article>
    <div class="item-outer flex">
        <div class="item-media">
            <a href="<?php if (empty ( $this->_aVars['aMiniListing']['sponsor_id'] )):  echo Phpfox::permalink('marketplace', $this->_aVars['aMiniListing']['listing_id'], $this->_aVars['aMiniListing']['title'], false, null, (array) array (
));  else:  echo Phpfox::getLib('phpfox.url')->makeUrl('ad.sponsor', array('view' => $this->_aVars['aMiniListing']['sponsor_id']));  endif; ?>" class="mp_listing_image"
            style="background-image: url(
<?php if (! empty ( $this->_aVars['aMiniListing']['image_path'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aMiniListing']['server_id'],'title' => $this->_aVars['aMiniListing']['title'],'path' => 'marketplace.url_image','file' => $this->_aVars['aMiniListing']['image_path'],'suffix' => '_200_square','return_url' => true)); ?>
<?php else: ?>
<?php echo Phpfox::getParam('marketplace.marketplace_default_photo'); ?>
<?php endif; ?>
            )" >
            </a>
        </div>
        <div class="item-inner overflow">
            <a class="item-title" href="<?php if (empty ( $this->_aVars['aMiniListing']['sponsor_id'] )):  echo Phpfox::permalink('marketplace', $this->_aVars['aMiniListing']['listing_id'], $this->_aVars['aMiniListing']['title'], false, null, (array) array (
));  else:  echo Phpfox::getLib('phpfox.url')->makeUrl('ad.sponsor', array('view' => $this->_aVars['aMiniListing']['sponsor_id']));  endif; ?>"><?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aMiniListing']['title']); ?></a>
            <div class="item-price">
<?php if ($this->_aVars['aMiniListing']['price'] == '0.00'): ?>
<?php echo _p('free'); ?>
<?php else: ?>
<?php echo Phpfox::getService('core.currency')->getCurrency($this->_aVars['aMiniListing']['price'], $this->_aVars['aMiniListing']['currency_id']); ?>
<?php endif; ?>
            </div>
            <div class="item-statistic"><span><?php echo Phpfox::getService('core.helper')->shortNumber($this->_aVars['aMiniListing']['total_view']); ?></span> <?php if ($this->_aVars['aMiniListing']['total_view'] == 1):  echo _p('view__l');  else:  echo _p('views_lowercase');  endif; ?></div>
        </div>
    </div>
</article>
