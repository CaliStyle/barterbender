<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php 
	 
?>

<div class="market-app detail-extra-info">
    <div class="item-info-price">
        <span class="" itemprop="price">
<?php if ($this->_aVars['aListing']['price'] == '0.00'): ?>
                <span class="free"><?php echo _p('free'); ?></span>
<?php else: ?>
                <span class="<?php if ($this->_aVars['aListing']['view_id'] == '2'): ?>sold<?php endif; ?>">
<?php echo Phpfox::getService('core.currency')->getCurrency($this->_aVars['aListing']['price'], $this->_aVars['aListing']['currency_id']); ?>
                </span>
<?php endif; ?>
        </span>
    </div>
    <div class="item-info-statistic">
        <div class="item-stat">
           <span><?php echo number_format($this->_aVars['aListing']['total_like']); ?></span> <?php if ($this->_aVars['aListing']['total_like'] == 1):  echo _p('like_lowercase');  else:  echo _p('likes_lowercase');  endif; ?>
        </div>
        <div class="item-stat">
           <span><?php echo number_format($this->_aVars['aListing']['total_view']); ?></span> <?php if ($this->_aVars['aListing']['total_view'] == 1):  echo _p('view_lowercase');  else:  echo _p('views_lowercase');  endif; ?>
        </div>
    </div>
<?php if (Phpfox ::isUser() && $this->_aVars['aListing']['user_id'] != Phpfox ::getUserId()): ?>
       <div class="item-action-contact">
            <div class="item-action-list">
<?php if ($this->_aVars['aListing']['view_id'] == '2'): ?>
                    <div class="btn item-soldout">
<?php echo _p('sold'); ?>
                    </div>
<?php endif; ?>
<?php if (( ( $this->_aVars['aListing']['is_sell'] || $this->_aVars['aListing']['allow_point_payment'] ) && $this->_aVars['aListing']['view_id'] != '2' && $this->_aVars['aListing']['price'] != '0.00' )): ?>
                    <form method="post" action="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('marketplace.purchase'); ?>" class="form">
                        <div><input type="hidden" name="id" value="<?php echo $this->_aVars['aListing']['listing_id']; ?>" /></div>
                        <button type="submit" value="<?php echo _p('buy_it_now'); ?>" class="btn btn-primary fw-bold item-buynow">
<?php echo _p('buy_now'); ?></button>
                    
</form>

<?php endif; ?>
<?php if ($this->_aVars['aListing']['canContactSeller']): ?>
                    <button class="btn btn-default" onclick="$Core.marketplace.contactSeller({id: <?php echo $this->_aVars['aListing']['user_id']; ?>, listing_id: <?php echo $this->_aVars['aListing']['listing_id']; ?>, module_id: 'marketplace'}); return false;">
                        <i class="ico ico-user3-next-o mr-1"></i><?php echo _p('contact_seller'); ?>
                    </button>
<?php endif; ?>
            </div>
       </div>
<?php endif; ?>
    <div class="item-info-author">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aListing'],'suffix' => '_50_square')); ?>
        <div class="item-detail-author">
            <div><?php echo _p("By"); ?> <?php echo '<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aListing']['user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aListing']['user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aListing']['user_name'], ((empty($this->_aVars['aListing']['user_name']) && isset($this->_aVars['aListing']['profile_page_id'])) ? $this->_aVars['aListing']['profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aListing']['user_id'], $this->_aVars['aListing']['full_name'])), 50, '...') . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aListing']['user_id']) ? '' : '</a>') . '</span>'; ?></div>
            <div><?php echo _p("posted_on"); ?> <?php echo Phpfox::getLib('date')->convertTime($this->_aVars['aListing']['time_stamp']); ?></div>
        </div>
<?php if ($this->_aVars['aListing']['hasPermission']): ?>
        <div class="item-detail-main-action">
            <div class="dropdown">
                <span role="button" data-toggle="dropdown" class="item_bar_action">
                    <i class="ico ico-gear-o"></i>
                </span>
                <ul class="dropdown-menu dropdown-menu-right">
                    <?php
						Phpfox::getLib('template')->getBuiltFile('marketplace.block.menu');
						?>
                </ul>
            </div>
        </div>
<?php endif; ?>
    </div>
<?php if (! empty ( $this->_aVars['aListing']['mini_description'] )): ?>
        <div class="item-info-short-desc">
<?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aListing']['mini_description']); ?>
        </div>
<?php endif; ?>
<?php if (! empty ( $this->_aVars['aListing']['location'] )): ?>
        <div class="item-info-location">
            <span class="item-label"><?php echo _p("location"); ?>:</span>
            <span>
                <a href="https://maps.google.com/?q=<?php echo $this->_aVars['aListing']['location']; ?>" target="_blank"><?php echo $this->_aVars['aListing']['location']; ?></a>
            </span>
        </div>
<?php endif; ?>
<?php if (is_array ( $this->_aVars['aListing']['categories'] ) && count ( $this->_aVars['aListing']['categories'] )): ?>
        <div class="item-info-categories">
            <span class="item-label"><?php echo _p("Categories"); ?>:</span>
<?php echo Phpfox::getService('core.category')->displayView($this->_aVars['aListing']['categories']); ?>
        </div>
<?php endif; ?>
</div>
