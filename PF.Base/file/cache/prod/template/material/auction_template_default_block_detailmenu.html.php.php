<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 1, 2020, 8:35 pm */ ?>
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
		
<div class="sub_section_menu subauction_section_menu">
	<ul>
<?php if (isset ( $this->_aVars['aModuleView']['overview'] ) && $this->_aVars['aModuleView']['overview']['is_show']): ?>
			<li <?php if ($this->_aVars['aModuleView']['overview']['active']): ?> class='active' <?php endif; ?>>
				<a href="<?php echo $this->_aVars['aModuleView']['overview']['link']; ?>" style="background-image: url(<?php echo $this->_aVars['core_path']; ?>module/auction/static/image/icon-detail-overview.png)">
<?php echo $this->_aVars['aModuleView']['overview']['module_phrase']; ?>
				</a>
			</li>
<?php endif; ?>

<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' ) && isset ( $this->_aVars['aModuleView']['shipping'] ) && $this->_aVars['aModuleView']['shipping']['is_show']): ?>
			<li <?php if ($this->_aVars['aModuleView']['shipping']['active']): ?> class='active' <?php endif; ?>>
				<a href="<?php echo $this->_aVars['aModuleView']['shipping']['link']; ?>" style="background-image: url(<?php echo $this->_aVars['core_path']; ?>module/auction/static/image/icon-detail-about-us.png)">
<?php echo $this->_aVars['aModuleView']['shipping']['module_phrase']; ?>
				</a>
			</li>
<?php endif; ?>
        
<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' ) && isset ( $this->_aVars['aModuleView']['bidhistory'] ) && $this->_aVars['aModuleView']['bidhistory']['is_show']): ?>
			<li <?php if ($this->_aVars['aModuleView']['bidhistory']['active']): ?> class='active' <?php endif; ?>>
				<a href="<?php echo $this->_aVars['aModuleView']['bidhistory']['link']; ?>" style="background-image: url(<?php echo $this->_aVars['core_path']; ?>module/auction/static/image/icon-1.jpg)">
<?php echo $this->_aVars['aModuleView']['bidhistory']['module_phrase']; ?>
				</a>
			</li>
<?php endif; ?>
        
<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' ) && ( $this->_aVars['aAuction']['user_id'] == Phpfox ::getUserId()) && isset ( $this->_aVars['aModuleView']['offerhistory'] ) && $this->_aVars['aModuleView']['offerhistory']['is_show']): ?>
			<li <?php if ($this->_aVars['aModuleView']['offerhistory']['active']): ?> class='active' <?php endif; ?>>
				<a href="<?php echo $this->_aVars['aModuleView']['offerhistory']['link']; ?>" style="background-image: url(<?php echo $this->_aVars['core_path']; ?>module/auction/static/image/icon-2.jpg)">
<?php echo $this->_aVars['aModuleView']['offerhistory']['module_phrase']; ?>
				</a>
			</li>
<?php endif; ?>
        
<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' ) && isset ( $this->_aVars['aModuleView']['chart'] ) && $this->_aVars['aModuleView']['chart']['is_show']): ?>
			<li <?php if ($this->_aVars['aModuleView']['chart']['active']): ?> class='active' <?php endif; ?>>
				<a href="<?php echo $this->_aVars['aModuleView']['chart']['link']; ?>" style="background-image: url(<?php echo $this->_aVars['core_path']; ?>module/auction/static/image/icon-3.jpg)">
<?php echo $this->_aVars['aModuleView']['chart']['module_phrase']; ?>
				</a>
			</li>
<?php endif; ?>
        
<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' ) && isset ( $this->_aVars['aModuleView']['activities'] ) && $this->_aVars['aModuleView']['activities']['is_show']): ?>
			<li <?php if ($this->_aVars['aModuleView']['activities']['active']): ?> class='active' <?php endif; ?>>
				<a href="<?php echo $this->_aVars['aModuleView']['activities']['link']; ?>" style="background-image: url(<?php echo $this->_aVars['core_path']; ?>module/auction/static/image/icon-detail-activities.png);">
<?php echo $this->_aVars['aModuleView']['activities']['module_phrase']; ?>
				</a>
			</li>
<?php endif; ?>

<?php if (count((array)$this->_aVars['aPagesModule'])):  foreach ((array) $this->_aVars['aPagesModule'] as $this->_aVars['aPage']): ?>
<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' )): ?>
				<li <?php if ($this->_aVars['aPage']['active']): ?> class='active' <?php endif; ?>>
					<a href="<?php echo $this->_aVars['aPage']['link']; ?>" style="background-image: url(<?php echo $this->_aVars['core_path']; ?>module/auction/static/image/icon-detail-overview.png)">
<?php echo $this->_aVars['aPage']['module_phrase']; ?>
					</a>				
				</li>
<?php endif; ?>
<?php endforeach; endif; ?>
	</ul>
</div>


<?php if (Phpfox ::isUser() && ( $this->_aVars['aAuction']['product_status'] != 'draft' )): ?>
<div class='ynauction-like'>
<?php if ($this->_aVars['isLiked']): ?>
		<a href="javascript:void(0)" class="btn btn-sm btn-primary"  onclick="$(this).parent().hide();$.ajaxCall('auction.deleteLike', 'type_id=auction&amp;item_id=<?php echo $this->_aVars['aAuction']['product_id']; ?>'); return false;">
<?php echo _p('unlike'); ?>
		</a>
<?php else: ?>
		<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="$(this).parent().hide();$.ajaxCall('auction.addLike', 'type_id=auction&amp;item_id=<?php echo $this->_aVars['aAuction']['product_id']; ?>'); return false;">
<?php echo _p('like'); ?>
		</a>
<?php endif; ?>
</div>
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
