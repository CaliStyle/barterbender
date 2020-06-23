<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
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
		<form method="post" action="" id="fevent_subscribe">
    <input type="hidden" name="val[subscribe]" value="1" />
    <div class="p-fevent-subscribe-container">
        <div class="item-subscribe-outer">
            <div class="item-search-list">
    <div class="form-group">
        <div class="dropdown subscribe-categories" id="subscribe_categories">
            <span class="dropdown-toggle d-block form-control cursor-point" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><p class="subscribe-categories__text"><?php echo _p('fevent.select_categories'); ?></p> <i class="ico ico-caret-down subscribe-categories__icon"></i></span>
            <div class="dropdown-menu dropdown-menu-right yn-dropdown-not-hide">
<?php if (count((array)$this->_aVars['aCategories'])):  foreach ((array) $this->_aVars['aCategories'] as $this->_aVars['aCategory']): ?>
                    <li class="subscribe-categories__item ml--2 mr--2 pl-2 pr-2">
                        <label class="mb-0 d-block fw-normal cursor-point pt-1 pb-1 subscribe-categories__label">
                            <input type="checkbox" name="val[subscribe_categories]" class="category_checkbox" value="<?php echo $this->_aVars['aCategory']['category_id']; ?>">
                            <i class="ico ico-square-o"></i><span><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['aCategory']['name'])); ?></span>
                        </label>
                    </li>
<?php if (isset ( $this->_aVars['aCategory']['sub'] ) && count ( $this->_aVars['aCategory']['sub'] )): ?>
<?php if (count((array)$this->_aVars['aCategory']['sub'])):  foreach ((array) $this->_aVars['aCategory']['sub'] as $this->_aVars['aSubCategory']): ?>
                        <li class="subscribe-categories__item ml--2 mr--2 pl-2 pr-2 sub">
                            <label class="mb-0 d-block fw-normal cursor-point pt-1 pb-1 subscribe-categories__label">
                                <input type="checkbox" name="val[subscribe_categories]" class="category_checkbox" value="<?php echo $this->_aVars['aSubCategory']['category_id']; ?>">
                                <i class="ico ico-square-o"></i><span><?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['aSubCategory']['name'])); ?></span>
                            </label>
                        </li>
<?php endforeach; endif; ?>
<?php endif; ?>
<?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <div class="form-group ynfevent-form-location">
        <div class="input-group input-group">
            <input type="text" name="val[subscribe_location]"  id="fevent_subscribeblock_location" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['ynfevent_subscribeblock_location']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['ynfevent_subscribeblock_location']) : (isset($this->_aVars['aForms']['ynfevent_subscribeblock_location']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['ynfevent_subscribeblock_location']) : '')); ?>
" class="form-control" aria-describedby="fevent_checkin" placeholder="<?php echo _p('fevent.location'); ?>" />
            <span class="input-group-addon" id="fevent_checkin" onclick="fevent.getCurrentPositionForBlock('subscribe');"><i class="ico ico-checkin-o"></i></span>
        </div>

        <input type="hidden" data-inputid="subscribe_location_address" id="subscribe_location_address" name="val[subscribe_location_address]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['subscribe_location_address']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['subscribe_location_address']) : (isset($this->_aVars['aForms']['subscribe_location_address']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['subscribe_location_address']) : '')); ?>
">
        <input type="hidden" data-inputid="subscribe_location_address_lat" id="subscribe_location_address_lat" name="val[subscribe_location_address_lat]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['subscribe_location_address_lat']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['subscribe_location_address_lat']) : (isset($this->_aVars['aForms']['subscribe_location_address_lat']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['subscribe_location_address_lat']) : '')); ?>
">
        <input type="hidden" data-inputid="subscribe_location_address_lng" id="subscribe_location_address_lng" name="val[subscribe_location_address_lng]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['subscribe_location_address_lng']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['subscribe_location_address_lng']) : (isset($this->_aVars['aForms']['subscribe_location_address_lng']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['subscribe_location_address_lng']) : '')); ?>
">
    </div>

    <div class="form-group">
        <input type="text" name="val[subscribe_radius]"  id="subscribe_radius" class="form-control" placeholder="<?php echo _p('fevent.radius_mile'); ?>" />
    </div>

    <div class="form-group">
        <input type="text" name="val[email]" id="subscribe_email" value="<?php if (isset ( $this->_aVars['aEmail'] )):  echo $this->_aVars['aEmail'];  endif; ?>" class="form-control" placeholder="<?php echo _p('fevent.email'); ?>" />
    </div>
                </div>
                <div class="form-group item-action">
    <button type="button" value="<?php echo _p('fevent.subscribe'); ?>" class="btn btn-primary " id="fevent_subscribeblock_submit"><?php echo _p('fevent.subscribe'); ?></button>
                </div>
        </div>
    </div>

</form>

<?php echo '
    <script type="text/javascript">
        $Behavior.readyYnfeventSubscribeBlock = function() {
            fevent.initSubscribeBlock();
            $(\'#fevent_subscribeblock_submit\').click(function(){
                var categories = [];
                $.each($(\'.category_checkbox:checked\'), function(key, el) {
                    categories.push($(el).val());
                })

                $.ajaxCall(\'fevent.subscribeEvent\', \'email=\' +$(\'#subscribe_email\').val() +
                                                          \'&categories=\' +categories.join(\',\') +
                                                          \'&location_lat=\' +$(\'#subscribe_location_address_lat\').val() +
                                                          \'&location_lng=\' +$(\'#subscribe_location_address_lng\').val() +
                                                          \'&address=\' +$(\'#subscribe_location_address\').val() +
                                                          \'&radius=\' +$(\'#subscribe_radius\').val()
                                                          );
                return false;
            });
        };
    </script>
'; ?>




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
