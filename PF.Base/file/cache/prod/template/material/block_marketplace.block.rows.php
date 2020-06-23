<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>
<?php
    
?>
<article class="<?php if ($this->_aVars['aListing']['is_sponsor']): ?>is_sponsored <?php endif;  if ($this->_aVars['aListing']['is_featured']): ?>is_featured <?php endif; ?> <?php if ($this->_aVars['aListing']['hasPermission']): ?>has-action<?php endif; ?>" id="js_mp_item_holder_<?php echo $this->_aVars['aListing']['listing_id']; ?>">
    <div class="item-outer">
        <div class="item-media">
            <a href="<?php echo $this->_aVars['aListing']['url']; ?>" class="mp_listing_image"
            style="background-image: url(
<?php if (! empty ( $this->_aVars['aListing']['image_path'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aListing']['server_id'],'title' => $this->_aVars['aListing']['title'],'path' => 'marketplace.url_image','file' => $this->_aVars['aListing']['image_path'],'suffix' => '_400_square','return_url' => true)); ?>
<?php else: ?>
<?php echo Phpfox::getParam('marketplace.marketplace_default_photo'); ?>
<?php endif; ?>
            )" >
            </a>
            <div class="flag_style_parent">
<?php if (isset ( $this->_aVars['sListingView'] ) && $this->_aVars['sListingView'] == 'my' && $this->_aVars['aListing']['view_id'] == 1): ?>
                    <div class="sticky-label-icon sticky-pending-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-clock-o"></i>
                    </div>
<?php endif; ?>
<?php if ($this->_aVars['aListing']['is_sponsor']): ?>
                    <div class="sticky-label-icon sticky-sponsored-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-sponsor"></i>
                    </div>
<?php endif; ?>
<?php if ($this->_aVars['aListing']['is_featured']): ?>
                    <div class="sticky-label-icon sticky-featured-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-diamond"></i>
                    </div>
<?php endif; ?>
            </div>
<?php if ($this->_aVars['bShowModerator']): ?>
                <div class="moderation_row">
                    <label class="item-checkbox">
                       <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="<?php echo $this->_aVars['aListing']['listing_id']; ?>" id="check<?php echo $this->_aVars['aListing']['listing_id']; ?>" />
                       <i class="ico ico-square-o"></i>
                   </label>
                </div>
<?php endif; ?>
            <div class="item-info">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aListing'],'suffix' => '_50_square')); ?>
                <div class="item-info-author ml-1">
                    <div><?php echo _p("By"); ?> <?php echo '<span class="user_profile_link_span" id="js_user_name_link_' . $this->_aVars['aListing']['user_name'] . '">' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aListing']['user_id']) ? '' : '<a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aListing']['user_name'], ((empty($this->_aVars['aListing']['user_name']) && isset($this->_aVars['aListing']['profile_page_id'])) ? $this->_aVars['aListing']['profile_page_id'] : null))) . '">') . '' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getService('user')->getCurrentName($this->_aVars['aListing']['user_id'], $this->_aVars['aListing']['full_name'])), 50, '...') . '' . (Phpfox::getService('user.block')->isBlocked(null, $this->_aVars['aListing']['user_id']) ? '' : '</a>') . '</span>'; ?></div>
                    <div><?php echo Phpfox::getLib('date')->convertTime($this->_aVars['aListing']['time_stamp']); ?></div>
                </div>
            </div>
        </div>
        <div class="item-inner">
            <div class="item-title ">
                <a href="<?php echo $this->_aVars['aListing']['url']; ?>">
<?php echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aListing']['title']), 100, '...'), 25); ?>
                </a>
<?php if ($this->_aVars['aListing']['view_id'] == '2'): ?>
                    <span class="marketplace_item_sold">(<?php echo _p('sold'); ?>)</span>
<?php endif; ?>
            </div>
            <div class="item-price">
<?php if ($this->_aVars['aListing']['price'] == '0.00'): ?>
                    <span class="free"><?php echo _p('free'); ?></span>
<?php else: ?>
<?php echo Phpfox::getService('core.currency')->getCurrency($this->_aVars['aListing']['price'], $this->_aVars['aListing']['currency_id']); ?>
<?php endif; ?>
            </div>
<?php if (! empty ( $this->_aVars['aListing']['country_iso'] ) || ! empty ( $this->_aVars['aListing']['location'] )): ?>
                <div class="item-minor-info item-location">
                    <div class="item-text-label">
<?php echo _p('location'); ?>:
                    </div>
                    <div class="item-text-info">
<?php if (! empty ( $this->_aVars['aListing']['country_iso'] )): ?>
                            <a class="js_hover_title" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('marketplace', array('location' => $this->_aVars['aListing']['country_iso'])); ?>">
<?php echo Phpfox::getService('core.country')->getCountry($this->_aVars['aListing']['country_iso']); ?>
                                <span class="js_hover_info">
<?php echo $this->_aVars['aListing']['location']; ?>
                                </span>
                            </a>
<?php else: ?>
<?php echo $this->_aVars['aListing']['location']; ?>
<?php endif; ?>
                    </div>
                </div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['aListing']['categories'] ) && is_array ( $this->_aVars['aListing']['categories'] ) && count ( $this->_aVars['aListing']['categories'] )): ?>
                <div class="item-minor-info item-category">
                    <div class="item-text-label">
<?php echo _p('category'); ?>:
                    </div>
                    <div class="item-text-info">
<?php echo Phpfox::getService('core.category')->displayView($this->_aVars['aListing']['categories']); ?>
                    </div>
                </div>
<?php endif; ?>
            <div class="item-statistic">
                <span>
                    <span class="count"><?php echo Phpfox::getService('core.helper')->shortNumber($this->_aVars['aListing']['total_like']); ?></span>
<?php if ($this->_aVars['aListing']['total_like'] == 1):  echo _p('like__l');  else:  echo _p('likes__l');  endif; ?>
                </span>
                <span>
                    <span class="count"><?php echo Phpfox::getService('core.helper')->shortNumber($this->_aVars['aListing']['total_view']); ?></span>
<?php if ($this->_aVars['aListing']['total_view'] == 1):  echo _p('view__l');  else:  echo _p('views__l');  endif; ?>
                </span>
            </div>
<?php if ($this->_aVars['aListing']['hasPermission']): ?>
                <div class="item-option">
                    <div class="dropdown">
                        <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
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
    </div>
</article>
