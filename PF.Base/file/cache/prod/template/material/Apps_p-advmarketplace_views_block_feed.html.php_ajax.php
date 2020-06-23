<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 22, 2020, 5:14 pm */ ?>
<?php

?>

<?php if (! isset ( $this->_aVars['sHidden'] )):  $this->assign('sHidden', '');  endif; ?>

<?php if (( isset ( $this->_aVars['sHeader'] ) && ( ! PHPFOX_IS_AJAX || isset ( $this->_aVars['bPassOverAjaxCall'] ) || isset ( $this->_aVars['bIsAjaxLoader'] ) ) ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>

<div class="<?php echo $this->_aVars['sHidden']; ?> block<?php if (( defined ( 'PHPFOX_IN_DESIGN_MODE' ) ) && ( ! isset ( $this->_aVars['bCanMove'] ) || ( isset ( $this->_aVars['bCanMove'] ) && $this->_aVars['bCanMove'] == true ) )): ?> js_sortable<?php endif;  if (isset ( $this->_aVars['sCustomClassName'] )): ?> <?php echo $this->_aVars['sCustomClassName'];  endif; ?>"<?php if (isset ( $this->_aVars['sBlockBorderJsId'] )): ?> id="js_block_border_<?php echo $this->_aVars['sBlockBorderJsId']; ?>"<?php endif;  if (defined ( 'PHPFOX_IN_DESIGN_MODE' ) && Phpfox_Module ::instance()->blockIsHidden('js_block_border_' . $this->_aVars['sBlockBorderJsId'] . '' )): ?> style="display:none;"<?php endif; ?> data-toggle="<?php echo $this->_aVars['sToggleWidth']; ?>">
<?php if (! empty ( $this->_aVars['sHeader'] ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>
		<div class="title <?php if (defined ( 'PHPFOX_IN_DESIGN_MODE' )): ?>js_sortable_header<?php endif; ?>">
<?php if (isset ( $this->_aVars['sBlockTitleBar'] )): ?>
<?php echo $this->_aVars['sBlockTitleBar']; ?>
<?php endif; ?>
<?php if (( isset ( $this->_aVars['aEditBar'] ) && Phpfox ::isUser())): ?>
			<div class="js_edit_header_bar">
				<a href="#" title="<?php echo _p('edit_this_block'); ?>" onclick="$.ajaxCall('<?php echo $this->_aVars['aEditBar']['ajax_call']; ?>', 'block_id=<?php echo $this->_aVars['sBlockBorderJsId'];  if (isset ( $this->_aVars['aEditBar']['params'] )):  echo $this->_aVars['aEditBar']['params'];  endif; ?>'); return false;">
					<span class="ico ico-pencilline-o"></span>
				</a>
			</div>
<?php endif; ?>
<?php if (empty ( $this->_aVars['sHeader'] )): ?>
<?php echo $this->_aVars['sBlockShowName']; ?>
<?php else: ?>
<?php echo $this->_aVars['sHeader']; ?>
<?php endif; ?>
		</div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['aEditBar'] )): ?>
	<div id="js_edit_block_<?php echo $this->_aVars['sBlockBorderJsId']; ?>" class="edit_bar hidden"></div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['aMenu'] ) && count ( $this->_aVars['aMenu'] )): ?>
<?php unset($this->_aVars['aMenu']); ?>
<?php endif; ?>
	<div class="content"<?php if (isset ( $this->_aVars['sBlockJsId'] )): ?> id="js_block_content_<?php echo $this->_aVars['sBlockJsId']; ?>"<?php endif; ?>>
<?php endif; ?>
		<?php

?>

<div class="advmarketplace-app advmarketplace-feed-item core-feed-item js_listing_item_<?php echo $this->_aVars['aListing']['listing_id']; ?>">
    <div class="item-outer">
        <div class="item-media">
            <a class="item-media-src" href="<?php echo $this->_aVars['aListing']['bookmark_url']; ?>" target="_blank" style="background-image: url(
<?php if (! empty ( $this->_aVars['aListing']['image_path'] )): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aListing']['server_id'],'title' => $this->_aVars['aListing']['title'],'path' => 'advancedmarketplace.url_pic','file' => $this->_aVars['aListing']['image_path'],'suffix' => '','return_url' => true)); ?>
<?php else: ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('path' => 'core.path_actual','file' => 'PF.Site/Apps/p-advmarketplace/assets/image/default/no-image.png','return_url' => true)); ?>
<?php endif; ?>
            )"></a>
        </div>¡

        <div class="item-inner">
            <div class="item-title">
                <a href="<?php echo $this->_aVars['aListing']['bookmark_url']; ?>" class="core-feed-title line-2"><?php echo Phpfox::getLib('phpfox.parse.output')->split(Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aListing']['title']), 100, '...'), 25); ?></a>
            </div>
            <div class="item-info-minor">
                <div class="core-feed-minor p-seperate-dot-wrapper">
                    <div class="p-seperate-dot-item">
<?php if (! empty ( $this->_aVars['aListing']['city'] )): ?> <?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aListing']['city']); ?>, <?php endif;  if (! empty ( $this->_aVars['aListing']['country_child_id'] )): ?> <?php echo Phpfox::getService('core.country')->getChild($this->_aVars['aListing']['country_child_id']); ?>, <?php endif;  echo Phpfox::getService('core.country')->getCountry($this->_aVars['aListing']['country_iso']); ?>
                    </div>
                    <div class="p-seperate-dot-item">
                        <span class="category-title"><?php echo _p('category'); ?>:</span> <?php echo Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getService('core.category')->displayLinks($this->_aVars['aListing']['categories']), 64, '...'); ?>
                    </div>
                </div>
            </div>
            <div class="item-price">
<?php if ($this->_aVars['aListing']['price'] == '0.00'): ?>
                    <div class="p-text-success"><?php echo _p('free'); ?></div>
<?php else: ?>
                    <div class="p-text-warning"><?php echo $this->_aVars['aListing']['listing_price']; ?></div>
<?php endif; ?>
            </div>
<?php if (empty ( $this->_aVars['isEmbed'] )): ?>
            <div class="item-action">
                <button class="btn btn-default btn-icon p-advmarketplace-item-wishlist-action js_wishlist_btn <?php if (! empty ( $this->_aVars['aListing']['is_wishlist'] )): ?>checked<?php endif; ?>" data-id="<?php echo $this->_aVars['aListing']['listing_id']; ?>" data-wishlist="<?php if ($this->_aVars['aListing']['is_wishlist']): ?>0<?php else: ?>1<?php endif; ?>" data-feed="1" onclick="appAdvMarketplace.processWishlist(this); return false;"><i class="ico ico-heart-o"></i> <span class="js_wishlist_text"><?php if (! empty ( $this->_aVars['aListing']['is_wishlist'] )):  echo _p('added_to_wish_list_replacement');  else:  echo _p('advancedmarketplace_add_to_wishlist');  endif; ?></span></span></button>
            </div>
<?php endif; ?>
        </div>
    </div>
</div>




<?php if (( isset ( $this->_aVars['sHeader'] ) && ( ! PHPFOX_IS_AJAX || isset ( $this->_aVars['bPassOverAjaxCall'] ) || isset ( $this->_aVars['bIsAjaxLoader'] ) ) ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>
	</div>
<?php if (isset ( $this->_aVars['aFooter'] ) && count ( $this->_aVars['aFooter'] )): ?>
	<div class="bottom">
<?php if (count ( $this->_aVars['aFooter'] ) == 1): ?>
<?php if (count((array)$this->_aVars['aFooter'])):  $this->_aPhpfoxVars['iteration']['block'] = 0;  foreach ((array) $this->_aVars['aFooter'] as $this->_aVars['sPhrase'] => $this->_aVars['sLink']):  $this->_aPhpfoxVars['iteration']['block']++; ?>

<?php if ($this->_aVars['sLink'] == '#'): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif','class' => 'ajax_image')); ?>
<?php endif; ?>
<?php if (is_array ( $this->_aVars['sLink'] )): ?>
            <a class="btn btn-block <?php if (! empty ( $this->_aVars['sLink']['class'] )): ?> <?php echo $this->_aVars['sLink']['class'];  endif; ?>" href="<?php if (! empty ( $this->_aVars['sLink']['link'] )):  echo $this->_aVars['sLink']['link'];  else: ?>#<?php endif; ?>" <?php if (! empty ( $this->_aVars['sLink']['attr'] )):  echo $this->_aVars['sLink']['attr'];  endif; ?> id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
<?php else: ?>
            <a class="btn btn-block" href="<?php echo $this->_aVars['sLink']; ?>" id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php else: ?>
		<ul>
<?php if (count((array)$this->_aVars['aFooter'])):  $this->_aPhpfoxVars['iteration']['block'] = 0;  foreach ((array) $this->_aVars['aFooter'] as $this->_aVars['sPhrase'] => $this->_aVars['sLink']):  $this->_aPhpfoxVars['iteration']['block']++; ?>

				<li id="js_block_bottom_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"<?php if ($this->_aPhpfoxVars['iteration']['block'] == 1): ?> class="first"<?php endif; ?>>
<?php if ($this->_aVars['sLink'] == '#'): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif','class' => 'ajax_image')); ?>
<?php endif; ?>
					<a href="<?php echo $this->_aVars['sLink']; ?>" id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
				</li>
<?php endforeach; endif; ?>
		</ul>
<?php endif; ?>
	</div>
<?php endif; ?>
</div>
<?php endif;  unset($this->_aVars['sHeader'], $this->_aVars['sComponent'], $this->_aVars['aFooter'], $this->_aVars['sBlockBorderJsId'], $this->_aVars['bBlockDisableSort'], $this->_aVars['bBlockCanMove'], $this->_aVars['aEditBar'], $this->_aVars['sDeleteBlock'], $this->_aVars['sBlockTitleBar'], $this->_aVars['sBlockJsId'], $this->_aVars['sCustomClassName'], $this->_aVars['aMenu']); ?>
