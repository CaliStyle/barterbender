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
		<div class="block">
    <div class="">
        <ul class="ynauction-detailcheckinlist">
<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' )): ?>
                <li>
                    <a id="ynauction_detailcheckinlist_comparebutton" auctionid="<?php echo $this->_aVars['aAuction']['product_id']; ?>" href="javascript:void(0)" onclick="ynauction.click_ynauction_detailauction_comparebutton(this, <?php echo $this->_aVars['aAuction']['product_id']; ?>); return false;"><i class="fa fa-files-o"></i> <?php echo _p('add_to_compare'); ?></a>
                    <div style="display: none;">
                        <input type="checkbox" 
                            data-compareitemauctionid="<?php echo $this->_aVars['aAuction']['product_id']; ?>"
                            data-compareitemname="<?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aAuction']['name']); ?>"
                            data-compareitemlink="<?php echo Phpfox::permalink('auction.detail', $this->_aVars['aAuction']['product_id'], $this->_aVars['aAuction']['name'], false, null, (array) array (
)); ?>"
                            data-compareitemlogopath="<?php if (isset ( $this->_aVars['aAuction']['logo_path'] )):  echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aAuction']['server_id'],'path' => 'core.url_pic','file' => $this->_aVars['aAuction']['logo_path'],'suffix' => '_400','return_url' => true));  else: ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aAuction']['server_id'],'path' => '','file' => $this->_aVars['aAuction']['default_logo_path'],'suffix' => '','return_url' => true));  endif; ?>"
                            onclick="ynauction.clickCompareCheckbox(this);" 
                            class="ynauction-compare-checkbox"> <?php echo _p('add_to_compare'); ?>                       
                    </div>
                </li>
<?php endif; ?>
            
<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' )): ?>
                <li>
                    <a href="javascript:void(0)" onclick="tb_show('<?php echo _p('share'); ?>', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=feed&amp;url=<?php echo $this->_aVars['aAuction']['linkAuction']; ?>&amp;title=<?php echo $this->_aVars['aAuction']['titleAuction']; ?>&amp;feed_id=<?php echo $this->_aVars['aAuction']['product_id']; ?>&amp;is_feed_view=1&amp;sharemodule=auction')); return false;"><i class="fa fa-share"></i> <?php echo _p('share'); ?></a>
                </li>
<?php endif; ?>

<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' && Phpfox ::isUser() && $this->_aVars['aAuction']['user_id'] != Phpfox ::getUserId())): ?>
<?php if ($this->_aVars['aAuction']['bIsInWatchList']): ?>
                    <li><a href="javascript:void(0)" onclick="$.ajaxCall('auction.removeFromWatchList', 'item_id=<?php echo $this->_aVars['aAuction']['product_id']; ?>'); return false;"><i class="fa fa-arrow-right"></i> <?php echo _p('remove_from_watchlist'); ?></a></li>
<?php else: ?>
                    <li><a href="javascript:void(0)" onclick="$.ajaxCall('auction.addToWatchList', 'item_id=<?php echo $this->_aVars['aAuction']['product_id']; ?>'); return false;"><i class="fa fa-arrow-right"></i> <?php echo _p('add_to_watchlist'); ?></a></li>
<?php endif; ?>
<?php endif; ?>

<?php if (( $this->_aVars['aAuction']['product_status'] != 'draft' ) && ( $this->_aVars['aAuction']['user_id'] != Phpfox ::getUserId())): ?>
                <li><a href="javascript:void(0)" onclick="$Core.composeMessage({user_id: <?php echo $this->_aVars['aAuction']['user_id']; ?>}); return false;"><i class="fa fa-envelope"></i> <?php echo _p('message_owner'); ?></a></li>
<?php endif; ?>
            <li><a onclick="window.open('<?php echo Phpfox::permalink('auction.print', $this->_aVars['aAuction']['product_id'], $this->_aVars['aAuction']['name'], false, null, (array) array (
)); ?>','_blank');return false;" href="#"><i class="fa fa-print"></i> <?php echo _p('print'); ?></a></li>
        </ul>
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
