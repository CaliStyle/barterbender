<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:46 pm */ ?>
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
 
 if (isset ( $this->_aVars['aCategories'] )): ?>
	<ul class="action" id="auction_category_menus">
<?php if (count((array)$this->_aVars['aCategories'])):  foreach ((array) $this->_aVars['aCategories'] as $this->_aVars['aCategory']): ?>
        <li class="main_category_item <?php if ($this->_aVars['aCategory']['category_id'] == $this->_aVars['iCurrentCategoryId']): ?> active <?php endif; ?>">
<?php if (Phpfox ::isPhrase($this->_aVars['aCategory']['title'])): ?>
<?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
<?php else: ?>
<?php $this->assign('value_name', Phpfox::getLib('locale')->convert($this->_aVars['aCategory']['title'])); ?>
<?php endif; ?>
            <a href="<?php echo Phpfox::permalink('auction.category', $this->_aVars['aCategory']['category_id'], $this->_aVars['value_name'], false, null, (array) array (
)); ?>">
<?php if (isset ( $this->_aVars['aCategory']['url_photo'] ) && $this->_aVars['aCategory']['url_photo']): ?>
                    <img src="<?php echo $this->_aVars['aCategory']['url_photo']; ?>" height="16">
<?php elseif (isset ( $this->_aVars['aCategory']['class_category_item'] )): ?>
                    <span class="category_item_<?php echo $this->_aVars['aCategory']['class_category_item']; ?>"></span>
<?php endif; ?>
                <span class=""><?php echo $this->_aVars['value_name']; ?></span>
                <span class="toggle fa fa-chevron-right"></span>
            </a>
<?php if ($this->_aVars['aCategory']['sub_category']): ?>
                <div style="display: none;" class="auction_sub_category_items">
                    <ul>
                    	<?php
                    		$sub1Limit = 4;
							$sub1Count = 0;
						?>
<?php if (count((array)$this->_aVars['aCategory']['sub_category'])):  foreach ((array) $this->_aVars['aCategory']['sub_category'] as $this->_aVars['aSubCategory']): ?>
                        	<?php $sub1Count++;
                        	if($sub1Count <= $sub1Limit) :?>
	                            <li class="main_sub_category_item <?php if ($this->_aVars['aSubCategory']['category_id'] == $this->_aVars['iCurrentCategoryId']): ?> active <?php endif; ?>">
<?php if (Phpfox ::isPhrase($this->_aVars['aSubCategory']['title'])): ?>
<?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
<?php else: ?>
<?php $this->assign('value_name', Phpfox::getLib('locale')->convert($this->_aVars['aSubCategory']['title'])); ?>
<?php endif; ?>
	                                <a href="<?php echo Phpfox::permalink('auction.category', $this->_aVars['aSubCategory']['category_id'], $this->_aVars['value_name'], false, null, (array) array (
)); ?>">
	                                    <span class="ynmenu-icon" style="background-image: url('<?php echo $this->_aVars['aSubCategory']['url_photo']; ?>');"></span>
	                                    <span class="ynmenu-text have-child"><?php echo $this->_aVars['value_name']; ?></span>
	                                </a>
<?php if ($this->_aVars['aSubCategory']['sub_category']): ?>
	                                    <ul class="auction_sub_sub_category_items">
<?php if (count((array)$this->_aVars['aSubCategory']['sub_category'])):  foreach ((array) $this->_aVars['aSubCategory']['sub_category'] as $this->_aVars['aSubSubCategory']): ?>
<?php if (Phpfox ::isPhrase($this->_aVars['aSubSubCategory']['title'])): ?>
<?php $this->_aVars['value_name'] = _p($this->_aVars['aSubSubCategory']['title']) ?>
<?php else: ?>
<?php $this->assign('value_name', Phpfox::getLib('locale')->convert($this->_aVars['aSubSubCategory']['title'])); ?>
<?php endif; ?>
	                                            <li <?php if ($this->_aVars['aSubSubCategory']['category_id'] == $this->_aVars['iCurrentCategoryId']): ?> class="active" <?php endif; ?>>
	                                                <a href="<?php echo Phpfox::permalink('auction.category', $this->_aVars['aSubSubCategory']['category_id'], $this->_aVars['value_name'], false, null, (array) array (
)); ?>">
	                                                    <span class="ynmenu-icon" style="background-image: url('<?php echo $this->_aVars['aSubSubCategory']['url_photo']; ?>');"></span>
	                                                    <span class="ynmenu-text have-child"><?php echo $this->_aVars['value_name']; ?></span>
	                                                </a>
	                                            </li>
<?php endforeach; endif; ?>
	                                    </ul>
<?php endif; ?>
	                            </li>
<?php endif;?>
<?php endforeach; endif; ?>
                    </ul>
                    <div class="view_all_categories"><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('auction.categories'); ?>"><?php echo _p('view_all_categories'); ?></a></div>
                </div>
                
<?php endif; ?>
        </li>
<?php endforeach; endif; ?>
        <li class="main_category_item all_category_item">
            <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('auction.categories'); ?>">
                <span class=""><?php echo _p('all_categories'); ?></span>
            </a>
        </li>
	</ul>

	<?php echo '
	<script>
		$Behavior.initAuctionCategoriesMenu = function(){
			$(\'#auction_category_menus > li.main_category_item\').hover(
			function(){
				$(this).children(\'.auction_sub_category_items\').show(\'fast\');
			},
			function () {
				$(this).children(\'.auction_sub_category_items\').hide(\'fast\');
			});
		}
	</script>
	'; ?>


<?php endif; ?>



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
