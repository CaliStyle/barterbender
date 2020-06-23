<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 1, 2020, 8:34 pm */ ?>
<div id="ynauction_add" class="main_break">
    <div class="ynauction-hiddenblock">
        <input type="hidden" value="add" id="ynauction_pagename" name="ynauction_pagename">
    </div>
<?php if (isset ( $this->_aVars['invoice_id'] ) && ( int ) $this->_aVars['invoice_id'] > 0): ?>
    <div>
        <h3><?php echo _p('payment_methods'); ?></h3>
<?php Phpfox::getBlock('api.gateway.form', array()); ?>
    </div>
<?php else: ?>
    <div>
<?php echo $this->_aVars['sCreateJs']; ?>
        <form enctype="multipart/form-data" id="ynauction_add_auction_form" action="<?php echo $this->_aVars['sFormUrl']; ?>" class="ynauction-add-edit-form" method="post" onsubmit="<?php echo $this->_aVars['sGetJsForm']; ?>">
            <div class="ynauction-hiddenblock">
                <input type="hidden" value="<?php echo $this->_aVars['iDefaultFeatureFee']; ?>" id="ynauction_defaultfeaturefee">
                <input type="hidden" value="<?php echo $this->_aVars['iDefaultPublishFee']; ?>" id="ynauction_defaultpublishfee">
                <input type="hidden" value="<?php echo $this->_aVars['iRatioBuyItNowPrice']; ?>" id="ynauction_ratio_buyitnow_price">
            </div>
            <div>
                <div id="js_custom_privacy_input_holder">
<?php if ($this->_aVars['bIsEdit'] && empty ( $this->_aVars['sModule'] )): ?>
<?php Phpfox::getBlock('privacy.build', array('privacy_item_id' => $this->_aVars['aForms']['auction_id'],'privacy_module_id' => 'auction')); ?>
<?php endif; ?>
                </div>

                <input type="hidden" name="val[attachment]" class="js_attachment" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['attachment']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['attachment']) : (isset($this->_aVars['aForms']['attachment']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['attachment']) : '')); ?>
" />
                <input type="hidden" name="val[selected_categories]" id="js_selected_categories" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['selected_categories']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['selected_categories']) : (isset($this->_aVars['aForms']['selected_categories']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['selected_categories']) : '')); ?>
" />
<?php if (Phpfox ::getParam('core.force_https_secure_pages')): ?>
                <div><input id="force_https_secure_pages" type="hidden" name="force_https_secure_pages" value="https" /></div>
<?php else: ?>
                <div><input id="force_https_secure_pages" type="hidden" name="force_https_secure_pages" value="http" /></div>
<?php endif; ?>
<?php if (! empty ( $this->_aVars['sModule'] )): ?>
                <div><input type="hidden" name="module" value="<?php echo Phpfox::getLib('parse.output')->htmlspecialchars($this->_aVars['sModule']); ?>" /></div>
<?php endif; ?>
<?php if (! empty ( $this->_aVars['iItem'] )): ?>
                <div><input type="hidden" name="item" value="<?php echo Phpfox::getLib('parse.output')->htmlspecialchars($this->_aVars['iItem']); ?>" /></div>
<?php endif; ?>
<?php if ($this->_aVars['bIsEdit']): ?>
                <div><input type="hidden" name="val[auction_id]" value="<?php echo $this->_aVars['aForms']['auction_id']; ?>" /></div>
                <div><input type="hidden" name="val[auction_status]" value="<?php echo $this->_aVars['aForms']['auction_status']; ?>" /></div>
<?php endif; ?>

                <div id="js_auction_block_main" class="js_auction_block page_section_menu_holder">

                    <h3><?php echo _p('general_info'); ?></h3>
                    <div class="table form-group">
                        <div class="table_left">
                            <label for="name">*<?php echo _p('product_title'); ?>: </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[name]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['name']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['name']) : (isset($this->_aVars['aForms']['name']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['name']) : '')); ?>
" id="name" size="60" />
                        </div>
                    </div>

                    <div id="ynauction_categorylist" class="table form-group">
                        <div class="table_left">
                            <label for="category">*<?php echo _p('category'); ?>:</label>
                        </div>
                        <div class="table_right">
<?php echo $this->_aVars['sCategories']; ?>
                        </div>
                    </div>
                    <div class="table form-group">
                        <div id="ynauction_customfield_category">
                        </div>
                    </div>

<?php if (isset ( $this->_aVars['aUOMs'] ) && count ( $this->_aVars['aUOMs'] )): ?>
                    <div class="table form-group">
                        <div class="table_left">
                            <label for="uom">*<?php echo _p('uom'); ?>: </label>
                        </div>
                        <div class="table_right">
                            <select name="val[uom]" id="ynauction_uom" class="form-control">
<?php if (count((array)$this->_aVars['aUOMs'])):  foreach ((array) $this->_aVars['aUOMs'] as $this->_aVars['uom']): ?>
                                <option value="<?php echo $this->_aVars['uom']['uom_id']; ?>" <?php if (isset ( $this->_aVars['aForms']['uom'] ) && $this->_aVars['aForms']['uom'] == $this->_aVars['uom']['uom_id']): ?>selected<?php endif; ?>><?php echo $this->_aVars['uom']['title']; ?></option>
<?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>
<?php endif; ?>
                    <div class="table form-group">
                        <div class="table_left">
                            <label for="quantity">*<?php echo _p('quantity'); ?>: </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[quantity]" id="ynauction_quantity" value="<?php if (isset ( $this->_aVars['aForms']['quantity'] ) && isset ( $this->_aVars['aForms']['quantity'] )):  echo $this->_aVars['aForms']['quantity'];  endif; ?>" size="40" />
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="description"><?php echo _p('description'); ?></label>
                        </div>
                        <div class="table_right">
                            <div class="editor_holder"><?php echo Phpfox::getLib('phpfox.editor')->get('description', array (
  'id' => 'description',
));  Phpfox::getBlock('PHPfox_Twemoji_Awesome.share', array('id'=> 'description')); ?></div>
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="shipping">*<?php echo _p('shipping_payment'); ?></label>
                        </div>
                        <div class="table_right">
                            <div class="editor_holder"><?php echo Phpfox::getLib('phpfox.editor')->get('shipping', array (
  'id' => 'shipping',
));  Phpfox::getBlock('PHPfox_Twemoji_Awesome.share', array('id'=> 'shipping')); ?></div>
                        </div>
                    </div>

                    <h3><?php echo _p('price'); ?></h3>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="reserve_price">*<?php echo _p('reserve_price'); ?> (<?php echo $this->_aVars['aCurrentCurrencies']['0']['currency_id']; ?>): </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[reserve_price]" id="ynauction_reserve_price" value="<?php if (isset ( $this->_aVars['aForms']['reserve_price'] ) && isset ( $this->_aVars['aForms']['reserve_price'] )):  echo $this->_aVars['aForms']['reserve_price'];  endif; ?>" size="40" />
                            <div class="extra_info">
                                <input type="checkbox" id ="auction_add_checkbok" name="val[hide_reserve_price]" <?php if (isset ( $this->_aVars['aForms'] ) && isset ( $this->_aVars['aForms']['is_hide_reserve_price'] ) && $this->_aVars['aForms']['is_hide_reserve_price']): ?>checked<?php endif; ?>  > <?php echo _p('hide'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="buynow_price">*<?php echo _p('buy_now_price'); ?> (<?php echo $this->_aVars['aCurrentCurrencies']['0']['currency_id']; ?>): </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[buynow_price]" id="ynauction_buynow_price" value="<?php if (isset ( $this->_aVars['aForms']['buynow_price'] ) && isset ( $this->_aVars['aForms']['buynow_price'] )):  echo $this->_aVars['aForms']['buynow_price'];  endif; ?>" size="40" />
                        </div>
                    </div>


                    <div class="table form-group-follow">
<?php Phpfox::getBlock('core.upload-form', array('type' => 'auction_logo','current_photo' => '')); ?>
                    </div>

                    <h3><?php echo _p('availability'); ?></h3>

                    <div class="table form-group">
                        <div class="table_left">
                            *<?php echo _p('start_date'); ?>:
                        </div>
                        <div class="table_right">
                            <div class="ync_start_time" style="position: relative;">
                                <div class="form-inline select_date"><div class="js_datepicker_core_start_time"><span class="js_datepicker_holder"><div style="display:none;"><select  name="val[start_time_month]" id="start_time_month" class="form-control js_datepicker_month">
			<option value="1"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '1')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '1') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('1', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('1' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'January' : _p('january')); ?></option>
			<option value="2"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '2')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '2') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('2', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('2' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'February' : _p('february')); ?></option>
			<option value="3"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '3')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '3') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('3', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('3' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'March' : _p('march')); ?></option>
			<option value="4"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '4')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '4') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('4', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('4' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'April' : _p('april')); ?></option>
			<option value="5"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '5')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '5') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('5', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('5' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'May' : _p('may')); ?></option>
			<option value="6"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '6')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '6') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('6', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('6' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'June' : _p('june')); ?></option>
			<option value="7"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '7')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '7') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('7', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('7' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'July' : _p('july')); ?></option>
			<option value="8"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '8')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '8') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('8', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('8' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'August' : _p('august')); ?></option>
			<option value="9"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '9')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '9') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('9', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('9' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'September' : _p('september')); ?></option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '10') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('10', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('10' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'October' : _p('october')); ?></option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '11') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('11', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('11' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'November' : _p('november')); ?></option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_month') && in_array('start_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_month'])
								&& $aParams['start_time_month'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_month'])
									&& !isset($aParams['start_time_month'])
									&& (($this->_aVars['aForms']['start_time_month'] == '12') || (is_array($this->_aVars['aForms']['start_time_month']) && in_array('12', $this->_aVars['aForms']['start_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_month']) ? ('12' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'December' : _p('december')); ?></option>
		</select>
<span class="field_separator"> / </span>		<select name="val[start_time_day]" id="start_time_day" class="form-control js_datepicker_day">
			<option value="1"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '1')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '1') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('1', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('1' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>1</option>
			<option value="2"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '2')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '2') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('2', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('2' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>2</option>
			<option value="3"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '3')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '3') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('3', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('3' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>3</option>
			<option value="4"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '4')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '4') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('4', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('4' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>4</option>
			<option value="5"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '5')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '5') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('5', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('5' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>5</option>
			<option value="6"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '6')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '6') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('6', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('6' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>6</option>
			<option value="7"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '7')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '7') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('7', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('7' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>7</option>
			<option value="8"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '8')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '8') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('8', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('8' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>8</option>
			<option value="9"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '9')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '9') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('9', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('9' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>9</option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '10') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('10', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('10' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>10</option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '11') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('11', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('11' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>11</option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '12') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('12', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('12' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>12</option>
			<option value="13"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '13')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '13') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('13', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('13' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>13</option>
			<option value="14"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '14')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '14') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('14', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('14' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>14</option>
			<option value="15"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '15')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '15') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('15', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('15' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>15</option>
			<option value="16"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '16')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '16') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('16', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('16' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>16</option>
			<option value="17"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '17')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '17') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('17', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('17' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>17</option>
			<option value="18"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '18')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '18') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('18', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('18' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>18</option>
			<option value="19"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '19')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '19') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('19', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('19' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>19</option>
			<option value="20"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '20')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '20') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('20', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('20' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>20</option>
			<option value="21"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '21')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '21') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('21', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('21' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>21</option>
			<option value="22"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '22')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '22') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('22', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('22' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>22</option>
			<option value="23"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '23')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '23') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('23', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('23' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>23</option>
			<option value="24"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '24')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '24') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('24', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('24' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>24</option>
			<option value="25"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '25')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '25') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('25', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('25' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>25</option>
			<option value="26"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '26')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '26') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('26', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('26' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>26</option>
			<option value="27"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '27')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '27') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('27', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('27' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>27</option>
			<option value="28"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '28')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '28') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('28', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('28' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>28</option>
			<option value="29"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '29')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '29') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('29', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('29' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>29</option>
			<option value="30"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '30')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '30') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('30', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('30' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>30</option>
			<option value="31"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_day') && in_array('start_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_day'])
								&& $aParams['start_time_day'] == '31')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_day'])
									&& !isset($aParams['start_time_day'])
									&& (($this->_aVars['aForms']['start_time_day'] == '31') || (is_array($this->_aVars['aForms']['start_time_day']) && in_array('31', $this->_aVars['aForms']['start_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_day']) ? ('31' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>31</option>
		</select>
<span class="field_separator"> / </span><?php $aYears = range(2020, 2030);   $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); ?>		<select name="val[start_time_year]" id="start_time_year" class="form-control js_datepicker_year">
<?php foreach ($aYears as $iYear): ?>			<option value="<?php echo $iYear; ?>"<?php echo ((isset($aParams['start_time_year']) && $aParams['start_time_year'] == $iYear) ? ' selected="selected"' : (!isset($this->_aVars['aForms']['start_time_year']) ? ($iYear == Phpfox::getTime('Y') ? ' selected="selected"' : '') : ($this->_aVars['aForms']['start_time_year'] == $iYear ? ' selected="selected"' : ''))); ?>><?php echo $iYear; ?></option>
<?php endforeach; ?>		</select>
</div><input type="text" name="js_start_time__datepicker" value="<?php if (isset($aParams['start_time_month'])):  echo $aParams['start_time_month'] . '/';  echo $aParams['start_time_day'] . '/';  echo $aParams['start_time_year'];  elseif (isset($this->_aVars['aForms'])):  if (isset($this->_aVars['aForms']['start_time_month'])):  switch(Phpfox::getParam("core.date_field_order")){  case "DMY":  echo $this->_aVars['aForms']['start_time_day'] . '/';  echo $this->_aVars['aForms']['start_time_month'] . '/';  echo $this->_aVars['aForms']['start_time_year'];  break;  case "MDY":  echo $this->_aVars['aForms']['start_time_month'] . '/';  echo $this->_aVars['aForms']['start_time_day'] . '/';  echo $this->_aVars['aForms']['start_time_year'];  break;  case "YMD":  echo $this->_aVars['aForms']['start_time_year'] . '/';  echo $this->_aVars['aForms']['start_time_month'] . '/';  echo $this->_aVars['aForms']['start_time_day'];  break;  }  endif;  else:  switch(Phpfox::getParam("core.date_field_order")){	case "DMY": echo Phpfox::getTime('j') . '/' . Phpfox::getTime('n') . '/' . Phpfox::getTime('Y'); break;	case "MDY": echo Phpfox::getTime('n') . '/' . Phpfox::getTime('j') . '/' . Phpfox::getTime('Y'); break;	case "YMD": echo Phpfox::getTime('Y') . '/' . Phpfox::getTime('n') . '/' . Phpfox::getTime('j'); break;} endif; ?>" class="form-control js_date_picker" /><div class="js_datepicker_image"></div></span> <span class="form-inline js_datepicker_selects"><span class="select-date-label">at</span>		<select class="form-control" name="val[start_time_hour]" id="start_time_hour">
			<option value="00"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '00')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '00') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('00', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('00' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>00</option>
			<option value="01"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '01')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '01') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('01', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('01' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>01</option>
			<option value="02"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '02')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '02') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('02', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('02' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>02</option>
			<option value="03"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '03')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '03') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('03', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('03' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>03</option>
			<option value="04"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '04')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '04') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('04', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('04' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>04</option>
			<option value="05"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '05')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '05') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('05', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('05' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>05</option>
			<option value="06"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '06')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '06') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('06', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('06' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>06</option>
			<option value="07"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '07')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '07') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('07', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('07' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>07</option>
			<option value="08"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '08')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '08') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('08', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('08' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>08</option>
			<option value="09"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '09')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '09') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('09', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('09' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>09</option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '10') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('10', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('10' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>10</option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '11') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('11', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('11' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>11</option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '12') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('12', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('12' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>12</option>
			<option value="13"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '13')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '13') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('13', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('13' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>13</option>
			<option value="14"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '14')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '14') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('14', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('14' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>14</option>
			<option value="15"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '15')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '15') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('15', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('15' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>15</option>
			<option value="16"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '16')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '16') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('16', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('16' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>16</option>
			<option value="17"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '17')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '17') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('17', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('17' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>17</option>
			<option value="18"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '18')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '18') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('18', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('18' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>18</option>
			<option value="19"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '19')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '19') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('19', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('19' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>19</option>
			<option value="20"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '20')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '20') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('20', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('20' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>20</option>
			<option value="21"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '21')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '21') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('21', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('21' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>21</option>
			<option value="22"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '22')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '22') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('22', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('22' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>22</option>
			<option value="23"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_hour') && in_array('start_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_hour'])
								&& $aParams['start_time_hour'] == '23')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_hour'])
									&& !isset($aParams['start_time_hour'])
									&& (($this->_aVars['aForms']['start_time_hour'] == '23') || (is_array($this->_aVars['aForms']['start_time_hour']) && in_array('23', $this->_aVars['aForms']['start_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_hour']) ? ('23' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>23</option>
		</select><span class="select-date-separator">:</span>
		<select class="form-control" name="val[start_time_minute]" id="start_time_minute">
			<option value="00"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '00')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '00') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('00', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('00' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>00</option>
			<option value="01"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '01')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '01') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('01', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('01' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>01</option>
			<option value="02"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '02')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '02') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('02', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('02' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>02</option>
			<option value="03"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '03')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '03') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('03', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('03' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>03</option>
			<option value="04"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '04')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '04') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('04', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('04' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>04</option>
			<option value="05"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '05')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '05') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('05', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('05' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>05</option>
			<option value="06"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '06')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '06') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('06', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('06' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>06</option>
			<option value="07"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '07')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '07') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('07', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('07' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>07</option>
			<option value="08"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '08')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '08') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('08', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('08' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>08</option>
			<option value="09"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '09')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '09') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('09', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('09' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>09</option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '10') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('10', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('10' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>10</option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '11') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('11', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('11' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>11</option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '12') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('12', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('12' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>12</option>
			<option value="13"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '13')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '13') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('13', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('13' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>13</option>
			<option value="14"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '14')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '14') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('14', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('14' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>14</option>
			<option value="15"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '15')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '15') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('15', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('15' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>15</option>
			<option value="16"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '16')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '16') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('16', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('16' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>16</option>
			<option value="17"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '17')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '17') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('17', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('17' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>17</option>
			<option value="18"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '18')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '18') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('18', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('18' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>18</option>
			<option value="19"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '19')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '19') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('19', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('19' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>19</option>
			<option value="20"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '20')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '20') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('20', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('20' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>20</option>
			<option value="21"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '21')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '21') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('21', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('21' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>21</option>
			<option value="22"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '22')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '22') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('22', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('22' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>22</option>
			<option value="23"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '23')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '23') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('23', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('23' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>23</option>
			<option value="24"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '24')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '24') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('24', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('24' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>24</option>
			<option value="25"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '25')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '25') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('25', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('25' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>25</option>
			<option value="26"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '26')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '26') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('26', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('26' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>26</option>
			<option value="27"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '27')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '27') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('27', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('27' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>27</option>
			<option value="28"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '28')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '28') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('28', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('28' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>28</option>
			<option value="29"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '29')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '29') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('29', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('29' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>29</option>
			<option value="30"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '30')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '30') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('30', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('30' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>30</option>
			<option value="31"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '31')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '31') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('31', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('31' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>31</option>
			<option value="32"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '32')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '32') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('32', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('32' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>32</option>
			<option value="33"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '33')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '33') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('33', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('33' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>33</option>
			<option value="34"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '34')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '34') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('34', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('34' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>34</option>
			<option value="35"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '35')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '35') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('35', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('35' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>35</option>
			<option value="36"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '36')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '36') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('36', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('36' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>36</option>
			<option value="37"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '37')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '37') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('37', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('37' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>37</option>
			<option value="38"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '38')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '38') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('38', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('38' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>38</option>
			<option value="39"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '39')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '39') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('39', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('39' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>39</option>
			<option value="40"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '40')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '40') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('40', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('40' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>40</option>
			<option value="41"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '41')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '41') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('41', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('41' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>41</option>
			<option value="42"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '42')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '42') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('42', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('42' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>42</option>
			<option value="43"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '43')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '43') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('43', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('43' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>43</option>
			<option value="44"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '44')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '44') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('44', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('44' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>44</option>
			<option value="45"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '45')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '45') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('45', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('45' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>45</option>
			<option value="46"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '46')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '46') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('46', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('46' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>46</option>
			<option value="47"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '47')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '47') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('47', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('47' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>47</option>
			<option value="48"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '48')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '48') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('48', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('48' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>48</option>
			<option value="49"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '49')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '49') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('49', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('49' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>49</option>
			<option value="50"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '50')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '50') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('50', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('50' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>50</option>
			<option value="51"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '51')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '51') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('51', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('51' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>51</option>
			<option value="52"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '52')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '52') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('52', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('52' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>52</option>
			<option value="53"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '53')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '53') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('53', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('53' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>53</option>
			<option value="54"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '54')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '54') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('54', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('54' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>54</option>
			<option value="55"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '55')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '55') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('55', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('55' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>55</option>
			<option value="56"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '56')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '56') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('56', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('56' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>56</option>
			<option value="57"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '57')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '57') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('57', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('57' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>57</option>
			<option value="58"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '58')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '58') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('58', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('58' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>58</option>
			<option value="59"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('start_time_minute') && in_array('start_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['start_time_minute'])
								&& $aParams['start_time_minute'] == '59')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['start_time_minute'])
									&& !isset($aParams['start_time_minute'])
									&& (($this->_aVars['aForms']['start_time_minute'] == '59') || (is_array($this->_aVars['aForms']['start_time_minute']) && in_array('59', $this->_aVars['aForms']['start_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['start_time_minute']) ? ('59' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>59</option>
		</select>
</span></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            *<?php echo _p('end_date'); ?>:
                        </div>
                        <div class="table_right">
                            <div class="ync_end_time" style="position: relative;">
                                <div class="form-inline select_date"><div class="js_datepicker_core_end_time"><span class="js_datepicker_holder"><div style="display:none;"><select  name="val[end_time_month]" id="end_time_month" class="form-control js_datepicker_month">
			<option value="1"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '1')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '1') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('1', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('1' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'January' : _p('january')); ?></option>
			<option value="2"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '2')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '2') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('2', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('2' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'February' : _p('february')); ?></option>
			<option value="3"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '3')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '3') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('3', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('3' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'March' : _p('march')); ?></option>
			<option value="4"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '4')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '4') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('4', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('4' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'April' : _p('april')); ?></option>
			<option value="5"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '5')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '5') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('5', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('5' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'May' : _p('may')); ?></option>
			<option value="6"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '6')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '6') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('6', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('6' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'June' : _p('june')); ?></option>
			<option value="7"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '7')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '7') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('7', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('7' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'July' : _p('july')); ?></option>
			<option value="8"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '8')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '8') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('8', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('8' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'August' : _p('august')); ?></option>
			<option value="9"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '9')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '9') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('9', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('9' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'September' : _p('september')); ?></option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '10') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('10', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('10' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'October' : _p('october')); ?></option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '11') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('11', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('11' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'November' : _p('november')); ?></option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_month') && in_array('end_time_month', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_month'])
								&& $aParams['end_time_month'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_month'])
									&& !isset($aParams['end_time_month'])
									&& (($this->_aVars['aForms']['end_time_month'] == '12') || (is_array($this->_aVars['aForms']['end_time_month']) && in_array('12', $this->_aVars['aForms']['end_time_month']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_month']) ? ('12' == Phpfox::getTime('n') ? ' selected="selected"' : '') : ''); ?>><?php echo (defined('PHPFOX_INSTALLER') ? 'December' : _p('december')); ?></option>
		</select>
<span class="field_separator"> / </span>		<select name="val[end_time_day]" id="end_time_day" class="form-control js_datepicker_day">
			<option value="1"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '1')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '1') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('1', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('1' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>1</option>
			<option value="2"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '2')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '2') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('2', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('2' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>2</option>
			<option value="3"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '3')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '3') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('3', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('3' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>3</option>
			<option value="4"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '4')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '4') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('4', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('4' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>4</option>
			<option value="5"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '5')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '5') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('5', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('5' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>5</option>
			<option value="6"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '6')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '6') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('6', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('6' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>6</option>
			<option value="7"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '7')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '7') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('7', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('7' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>7</option>
			<option value="8"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '8')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '8') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('8', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('8' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>8</option>
			<option value="9"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '9')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '9') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('9', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('9' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>9</option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '10') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('10', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('10' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>10</option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '11') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('11', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('11' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>11</option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '12') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('12', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('12' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>12</option>
			<option value="13"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '13')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '13') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('13', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('13' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>13</option>
			<option value="14"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '14')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '14') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('14', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('14' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>14</option>
			<option value="15"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '15')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '15') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('15', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('15' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>15</option>
			<option value="16"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '16')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '16') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('16', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('16' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>16</option>
			<option value="17"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '17')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '17') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('17', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('17' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>17</option>
			<option value="18"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '18')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '18') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('18', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('18' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>18</option>
			<option value="19"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '19')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '19') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('19', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('19' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>19</option>
			<option value="20"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '20')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '20') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('20', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('20' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>20</option>
			<option value="21"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '21')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '21') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('21', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('21' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>21</option>
			<option value="22"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '22')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '22') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('22', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('22' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>22</option>
			<option value="23"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '23')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '23') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('23', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('23' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>23</option>
			<option value="24"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '24')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '24') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('24', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('24' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>24</option>
			<option value="25"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '25')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '25') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('25', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('25' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>25</option>
			<option value="26"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '26')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '26') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('26', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('26' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>26</option>
			<option value="27"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '27')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '27') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('27', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('27' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>27</option>
			<option value="28"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '28')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '28') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('28', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('28' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>28</option>
			<option value="29"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '29')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '29') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('29', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('29' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>29</option>
			<option value="30"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '30')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '30') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('30', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('30' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>30</option>
			<option value="31"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_day') && in_array('end_time_day', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_day'])
								&& $aParams['end_time_day'] == '31')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_day'])
									&& !isset($aParams['end_time_day'])
									&& (($this->_aVars['aForms']['end_time_day'] == '31') || (is_array($this->_aVars['aForms']['end_time_day']) && in_array('31', $this->_aVars['aForms']['end_time_day']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_day']) ? ('31' == Phpfox::getTime('j') ? ' selected="selected"' : '') : ''); ?>>31</option>
		</select>
<span class="field_separator"> / </span><?php $aYears = range(2020, 2030);   $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); ?>		<select name="val[end_time_year]" id="end_time_year" class="form-control js_datepicker_year">
<?php foreach ($aYears as $iYear): ?>			<option value="<?php echo $iYear; ?>"<?php echo ((isset($aParams['end_time_year']) && $aParams['end_time_year'] == $iYear) ? ' selected="selected"' : (!isset($this->_aVars['aForms']['end_time_year']) ? ($iYear == Phpfox::getTime('Y') ? ' selected="selected"' : '') : ($this->_aVars['aForms']['end_time_year'] == $iYear ? ' selected="selected"' : ''))); ?>><?php echo $iYear; ?></option>
<?php endforeach; ?>		</select>
</div><input type="text" name="js_end_time__datepicker" value="<?php if (isset($aParams['end_time_month'])):  echo $aParams['end_time_month'] . '/';  echo $aParams['end_time_day'] . '/';  echo $aParams['end_time_year'];  elseif (isset($this->_aVars['aForms'])):  if (isset($this->_aVars['aForms']['end_time_month'])):  switch(Phpfox::getParam("core.date_field_order")){  case "DMY":  echo $this->_aVars['aForms']['end_time_day'] . '/';  echo $this->_aVars['aForms']['end_time_month'] . '/';  echo $this->_aVars['aForms']['end_time_year'];  break;  case "MDY":  echo $this->_aVars['aForms']['end_time_month'] . '/';  echo $this->_aVars['aForms']['end_time_day'] . '/';  echo $this->_aVars['aForms']['end_time_year'];  break;  case "YMD":  echo $this->_aVars['aForms']['end_time_year'] . '/';  echo $this->_aVars['aForms']['end_time_month'] . '/';  echo $this->_aVars['aForms']['end_time_day'];  break;  }  endif;  else:  switch(Phpfox::getParam("core.date_field_order")){	case "DMY": echo Phpfox::getTime('j') . '/' . Phpfox::getTime('n') . '/' . Phpfox::getTime('Y'); break;	case "MDY": echo Phpfox::getTime('n') . '/' . Phpfox::getTime('j') . '/' . Phpfox::getTime('Y'); break;	case "YMD": echo Phpfox::getTime('Y') . '/' . Phpfox::getTime('n') . '/' . Phpfox::getTime('j'); break;} endif; ?>" class="form-control js_date_picker" /><div class="js_datepicker_image"></div></span> <span class="form-inline js_datepicker_selects"><span class="select-date-label">at</span>		<select class="form-control" name="val[end_time_hour]" id="end_time_hour">
			<option value="00"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '00')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '00') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('00', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('00' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>00</option>
			<option value="01"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '01')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '01') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('01', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('01' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>01</option>
			<option value="02"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '02')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '02') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('02', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('02' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>02</option>
			<option value="03"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '03')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '03') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('03', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('03' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>03</option>
			<option value="04"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '04')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '04') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('04', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('04' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>04</option>
			<option value="05"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '05')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '05') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('05', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('05' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>05</option>
			<option value="06"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '06')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '06') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('06', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('06' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>06</option>
			<option value="07"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '07')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '07') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('07', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('07' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>07</option>
			<option value="08"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '08')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '08') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('08', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('08' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>08</option>
			<option value="09"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '09')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '09') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('09', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('09' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>09</option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '10') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('10', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('10' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>10</option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '11') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('11', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('11' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>11</option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '12') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('12', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('12' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>12</option>
			<option value="13"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '13')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '13') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('13', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('13' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>13</option>
			<option value="14"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '14')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '14') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('14', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('14' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>14</option>
			<option value="15"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '15')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '15') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('15', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('15' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>15</option>
			<option value="16"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '16')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '16') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('16', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('16' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>16</option>
			<option value="17"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '17')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '17') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('17', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('17' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>17</option>
			<option value="18"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '18')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '18') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('18', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('18' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>18</option>
			<option value="19"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '19')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '19') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('19', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('19' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>19</option>
			<option value="20"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '20')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '20') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('20', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('20' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>20</option>
			<option value="21"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '21')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '21') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('21', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('21' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>21</option>
			<option value="22"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '22')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '22') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('22', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('22' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>22</option>
			<option value="23"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_hour') && in_array('end_time_hour', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_hour'])
								&& $aParams['end_time_hour'] == '23')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_hour'])
									&& !isset($aParams['end_time_hour'])
									&& (($this->_aVars['aForms']['end_time_hour'] == '23') || (is_array($this->_aVars['aForms']['end_time_hour']) && in_array('23', $this->_aVars['aForms']['end_time_hour']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_hour']) ? ('23' == Phpfox::getTime('H') ? ' selected="selected"' : '') : ''); ?>>23</option>
		</select><span class="select-date-separator">:</span>
		<select class="form-control" name="val[end_time_minute]" id="end_time_minute">
			<option value="00"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '00')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '00') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('00', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('00' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>00</option>
			<option value="01"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '01')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '01') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('01', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('01' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>01</option>
			<option value="02"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '02')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '02') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('02', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('02' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>02</option>
			<option value="03"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '03')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '03') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('03', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('03' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>03</option>
			<option value="04"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '04')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '04') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('04', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('04' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>04</option>
			<option value="05"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '05')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '05') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('05', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('05' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>05</option>
			<option value="06"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '06')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '06') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('06', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('06' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>06</option>
			<option value="07"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '07')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '07') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('07', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('07' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>07</option>
			<option value="08"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '08')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '08') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('08', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('08' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>08</option>
			<option value="09"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '09')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '09') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('09', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('09' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>09</option>
			<option value="10"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '10')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '10') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('10', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('10' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>10</option>
			<option value="11"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '11')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '11') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('11', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('11' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>11</option>
			<option value="12"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '12')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '12') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('12', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('12' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>12</option>
			<option value="13"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '13')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '13') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('13', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('13' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>13</option>
			<option value="14"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '14')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '14') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('14', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('14' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>14</option>
			<option value="15"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '15')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '15') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('15', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('15' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>15</option>
			<option value="16"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '16')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '16') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('16', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('16' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>16</option>
			<option value="17"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '17')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '17') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('17', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('17' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>17</option>
			<option value="18"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '18')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '18') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('18', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('18' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>18</option>
			<option value="19"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '19')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '19') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('19', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('19' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>19</option>
			<option value="20"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '20')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '20') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('20', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('20' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>20</option>
			<option value="21"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '21')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '21') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('21', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('21' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>21</option>
			<option value="22"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '22')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '22') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('22', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('22' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>22</option>
			<option value="23"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '23')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '23') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('23', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('23' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>23</option>
			<option value="24"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '24')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '24') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('24', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('24' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>24</option>
			<option value="25"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '25')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '25') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('25', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('25' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>25</option>
			<option value="26"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '26')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '26') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('26', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('26' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>26</option>
			<option value="27"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '27')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '27') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('27', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('27' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>27</option>
			<option value="28"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '28')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '28') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('28', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('28' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>28</option>
			<option value="29"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '29')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '29') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('29', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('29' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>29</option>
			<option value="30"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '30')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '30') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('30', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('30' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>30</option>
			<option value="31"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '31')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '31') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('31', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('31' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>31</option>
			<option value="32"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '32')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '32') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('32', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('32' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>32</option>
			<option value="33"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '33')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '33') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('33', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('33' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>33</option>
			<option value="34"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '34')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '34') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('34', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('34' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>34</option>
			<option value="35"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '35')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '35') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('35', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('35' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>35</option>
			<option value="36"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '36')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '36') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('36', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('36' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>36</option>
			<option value="37"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '37')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '37') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('37', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('37' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>37</option>
			<option value="38"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '38')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '38') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('38', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('38' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>38</option>
			<option value="39"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '39')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '39') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('39', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('39' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>39</option>
			<option value="40"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '40')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '40') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('40', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('40' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>40</option>
			<option value="41"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '41')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '41') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('41', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('41' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>41</option>
			<option value="42"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '42')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '42') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('42', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('42' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>42</option>
			<option value="43"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '43')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '43') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('43', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('43' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>43</option>
			<option value="44"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '44')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '44') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('44', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('44' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>44</option>
			<option value="45"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '45')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '45') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('45', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('45' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>45</option>
			<option value="46"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '46')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '46') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('46', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('46' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>46</option>
			<option value="47"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '47')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '47') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('47', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('47' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>47</option>
			<option value="48"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '48')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '48') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('48', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('48' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>48</option>
			<option value="49"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '49')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '49') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('49', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('49' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>49</option>
			<option value="50"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '50')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '50') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('50', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('50' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>50</option>
			<option value="51"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '51')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '51') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('51', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('51' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>51</option>
			<option value="52"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '52')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '52') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('52', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('52' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>52</option>
			<option value="53"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '53')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '53') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('53', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('53' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>53</option>
			<option value="54"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '54')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '54') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('54', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('54' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>54</option>
			<option value="55"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '55')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '55') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('55', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('55' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>55</option>
			<option value="56"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '56')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '56') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('56', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('56' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>56</option>
			<option value="57"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '57')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '57') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('57', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('57' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>57</option>
			<option value="58"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '58')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '58') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('58', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('58' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>58</option>
			<option value="59"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('end_time_minute') && in_array('end_time_minute', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['end_time_minute'])
								&& $aParams['end_time_minute'] == '59')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['end_time_minute'])
									&& !isset($aParams['end_time_minute'])
									&& (($this->_aVars['aForms']['end_time_minute'] == '59') || (is_array($this->_aVars['aForms']['end_time_minute']) && in_array('59', $this->_aVars['aForms']['end_time_minute']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							 echo (!isset($this->_aVars['aForms']['end_time_minute']) ? ('59' == Phpfox::getTime('i') ? ' selected="selected"' : '') : ''); ?>>59</option>
		</select>
</span></div></div>
                            </div>
                        </div>
                    </div>

                    <div id="ynauction_customfield" class="table form-group">
                        <h3><?php echo _p('addition_information'); ?></h3>
                        <div id="ynauction_customfield_user">
<?php if (isset ( $this->_aVars['aForms'] ) && isset ( $this->_aVars['aForms']['all_customfield_user'] ) && count ( $this->_aVars['aForms']['all_customfield_user'] )): ?>
<?php if (count((array)$this->_aVars['aForms']['all_customfield_user'])):  foreach ((array) $this->_aVars['aForms']['all_customfield_user'] as $this->_aVars['keyall_customfield_user'] => $this->_aVars['itemall_customfield_user']): ?>
                            <div class="table_right">
                                <div class="ynauction-customfield-user form-group">
                                    <div class="table_left">
                                        <label><?php echo _p('title'); ?>: </label>
                                    </div>
                                    <div class="table_right">
                                        <input class="form-control" type="text" name="val[customfield_user_title][]" size="60" value="<?php echo $this->_aVars['itemall_customfield_user']['usercustomfield_title']; ?>" />
                                        <div class="extra_info">
<?php if ($this->_aVars['keyall_customfield_user'] == 0): ?>
                                            <a id="ynauction_add" href="javascript:void(0)" onclick="ynauction.appendPredefined(this,'customfield_user'); return false;">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'misc/add.png','class' => 'v_middle')); ?>
                                            </a>
                                            <a id="ynauction_delete" style="display: none;" href="javascript:void(0)" onclick="ynauction.removePredefined(this,'customfield_user'); return false;">
                                                <img src="<?php echo $this->_aVars['corepath']; ?>module/auction/static/image/delete.png" class="v_middle"/>
                                            </a>
<?php else: ?>
                                            <a id="ynauction_delete" href="javascript:void(0)" onclick="ynauction.removePredefined(this,'customfield_user'); return false;">
                                                <img src="<?php echo $this->_aVars['corepath']; ?>module/auction/static/image/delete.png" class="v_middle"/>
                                            </a>
<?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="table_left">
                                        <label><?php echo _p('content'); ?>: </label>
                                    </div>
                                    <div class="table_right">
                                        <input class="form-control" type="text" name="val[customfield_user_content][]" size="60" value="<?php echo $this->_aVars['itemall_customfield_user']['usercustomfield_content']; ?>" />
                                    </div>
                                </div>
                            </div>
<?php endforeach; endif; ?>
<?php else: ?>
                            <div class="ynauction-customfield-user">
                                <div class="table_left">
                                    <label><?php echo _p('title'); ?>: </label>
                                </div>
                                <div class="table_right">
                                    <input class="form-control" type="text" name="val[customfield_user_title][]" size="60" />
                                    <div class="extra_info">
                                        <a id="ynauction_add" href="javascript:void(0)" onclick="ynauction.appendPredefined(this,'customfield_user'); return false;">
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'misc/add.png','class' => 'v_middle')); ?>
                                        </a>
                                        <a id="ynauction_delete" style="display: none;" href="javascript:void(0)" onclick="ynauction.removePredefined(this,'customfield_user'); return false;">
                                            <img src="<?php echo $this->_aVars['corepath']; ?>module/auction/static/image/delete.png" class="v_middle"/>
                                        </a>
                                    </div>
                                </div>
                                <div class="table_left">
                                    <label><?php echo _p('content'); ?>: </label>
                                </div>
                                <div class="table_right">
                                    <input class="form-control" type="text" name="val[customfield_user_content][]" size="60" />
                                </div>
                            </div>
<?php endif; ?>
                        </div>
                    </div>

<?php if (empty ( $this->_aVars['sModule'] ) && Phpfox ::isModule('privacy')): ?>
                    <h3><?php echo _p('privacy'); ?></h3>
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label for="view_privacy"><?php echo _p('view_privacy'); ?>:</label>
                        </div>
                        <div class="table_right">
<?php Phpfox::getBlock('privacy.form', array('privacy_name' => 'privacy','privacy_info' => 'auction.control_who_can_see_this_auction','privacy_no_custom' => true)); ?>
                        </div>
                    </div>
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label for="photo_privacy"><?php echo _p('photo_privacy'); ?>:</label>
                        </div>
                        <div class="table_right">
<?php Phpfox::getBlock('privacy.form', array('privacy_name' => 'privacy_photo','privacy_info' => 'auction.control_who_can_see_photos_of_this_auction','privacy_no_custom' => true)); ?>
                        </div>
                    </div>
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label for="video_privacy"><?php echo _p('video_privacy'); ?>:</label>
                        </div>
                        <div class="table_right">
<?php Phpfox::getBlock('privacy.form', array('privacy_name' => 'privacy_video','privacy_info' => 'auction.control_who_can_see_videos_of_this_auction','privacy_no_custom' => true)); ?>
                        </div>
                    </div>
<?php endif; ?>
                    <div class="table form-group-follow">
                        <div class="table_left"></div>
                        <div class="table_right">
                            <input type="checkbox" name="val[is_receive_notification]" <?php if (isset ( $this->_aVars['aForms'] ) && isset ( $this->_aVars['aForms']['receive_notification_someone_bid'] ) && $this->_aVars['aForms']['receive_notification_someone_bid']): ?>checked<?php endif; ?> > <?php echo _p('receive_notification_when_anyone_bid_this_auction'); ?>
                        </div>
                    </div>
                    <div class="table form-group-follow">
                        <div class="table_right">
                            <label><?php echo _p('publishing_fee_new'); ?>: <?php echo $this->_aVars['iDefaultPublishFee']; ?> <?php echo $this->_aVars['aCurrentCurrencies']['0']['currency_id']; ?></label>
                        </div>
                    </div>
<?php if (Phpfox ::getUserParam('auction.can_feature_auction')): ?>
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label><?php echo _p('feature'); ?>:</label>
                        </div>
                        <div class="table_right">
<?php echo _p('feature_this_auction_for'); ?> <input id="ynauction_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10"> <?php echo _p('day_s_with'); ?> <input id="ynauction_feature_fee_total" type="text" value="0" size="10" readonly /> <?php echo $this->_aVars['aCurrentCurrencies']['0']['currency_id']; ?>
                            <div class="extra_info">(<?php echo _p('fee_to_feature_auction_feature_fee_currency_id_for_1_day', array('feature_fee' => $this->_aVars['iDefaultFeatureFee'],'currency_id' => $this->_aVars['aCurrentCurrencies']['0']['currency_id'])); ?>)</div>
                        </div>
                    </div>
<?php endif; ?>
                    <div class="table form-group-follow">
                        <div class="table_left"></div>
                        <div class="table_right">
                            <label><?php echo _p('total_fee_new'); ?>: <span id="ynauction_text_defaultpublishfee"><?php echo $this->_aVars['iDefaultPublishFee']; ?></span> <?php echo $this->_aVars['aCurrentCurrencies']['0']['currency_id']; ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table_clear">
                <button id="ynauction_submit" type="submit" class="btn btn-sm btn-primary" name="val[draft]" onclick="$Core.reloadPage();"><?php echo _p('save'); ?></button>
                <button id="ynauction_submit" type="submit" class="btn btn-sm btn-primary" name="val[publish]" onclick="$Core.reloadPage();"><?php echo _p('publish'); ?></button>
                <button id="ynauction_submit" onclick="location.href='<?php echo $this->_aVars['sBackUrl']; ?>';" type="button" class="btn btn-sm btn-default" name="val[cancel]"><?php echo _p('cancel'); ?></button>
            </div>
        
</form>

    </div>
<?php if (Phpfox ::getParam('core.display_required')): ?>
    <div class="table_clear">
        * <?php echo _p('core.required_fields'); ?>
    </div>
<?php endif; ?>
<?php endif; ?>
</div>

<?php if (! isset ( $this->_aVars['invoice_id'] )): ?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
<?php endif; ?>

<?php if (PHPFOX_IS_AJAX):  echo '
<script type="text/javascript">
    $Behavior.globalInit();
    ynauction.initAdd();
</script>
'; ?>

<?php endif; ?>



