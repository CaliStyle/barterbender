<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 19, 2020, 4:44 am */ ?>
<?php 

?>
<script>
    var set_active = false, setting_group_class = '';
<?php if (( $this->_aVars['group_class'] )): ?>
    setting_group_class = '<?php echo $this->_aVars['group_class']; ?>';
<?php endif; ?>
    <?php echo '
    $Ready(function() {
        if (set_active) {
            return;
        }
        set_active = true;
        $(\'._is_app_settings\').show();
        $(\'.js-acp-header-section a[href*="admincp/setting/edit"]\').addClass(\'active\');
        if (setting_group_class) {
            $(\'.\' + setting_group_class + \':not(.is_option_class)\').show();
            $(\'.\' + setting_group_class + \'.is_option_class\').each(function() {
                var option_class = $(this).data(\'option-class\').split(\'=\'),
                    s_key = option_class[0],
                    s_value = option_class[1],
                    i = $(this),
                    t = $(\'.__data_option_\' + s_key + \'\');
                if (t.length) {
                    if (t.val() == s_value) {
                        i.show();
                    } else {
                        i.hide();
                    }
                }
            });
        }
    });
    '; ?>

</script>
<?php if (count ( $this->_aVars['aSettings'] )): ?>
    <form method="post" action="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('current'); ?>" enctype="multipart/form-data" class="on_change_submit">
        <div class="panel panel-default global-settings _is_app_settings">
            <div class="panel-heading">
                <div class="panel-title">
<?php echo _p('manage_settings'); ?>
<?php if (( $this->_aVars['admincp_help'] )): ?> <a href="<?php echo $this->_aVars['admincp_help']; ?>" target="_blank" class="pull-right" style="font-size: 20px;"><i class="fa fa-info-circle"></i></a><?php endif; ?>
                </div>
            </div>
            <div class="panel-body">
<?php if (count((array)$this->_aVars['aSettings'])):  foreach ((array) $this->_aVars['aSettings'] as $this->_aVars['var'] => $this->_aVars['aSetting']): ?>
                    <div id="<?php echo $this->_aVars['aSetting']['var_name']; ?>" class="form-group <?php if (isset ( $this->_aVars['aSetting']['is_danger'] ) && $this->_aVars['aSetting']['is_danger']): ?> has-warning <?php endif; ?> <?php if (! empty ( $this->_aVars['aSetting']['error'] )): ?>has-error<?php endif; ?> lines<?php if (! empty ( $this->_aVars['aSetting']['group_class'] )): ?> <?php echo $this->_aVars['aSetting']['group_class'];  endif;  if (! empty ( $this->_aVars['aSetting']['option_class'] )): ?> is_option_class<?php endif;  if (! empty ( $this->_aVars['group_class'] ) && ! empty ( $this->_aVars['aSetting']['group_class'] ) && $this->_aVars['group_class'] != $this->_aVars['aSetting']['group_class']): ?> hidden<?php endif; ?> clearfix"<?php if (! empty ( $this->_aVars['aSetting']['option_class'] )): ?> data-option-class="<?php echo $this->_aVars['aSetting']['option_class']; ?>"<?php endif; ?>>
<?php if (PHPFOX_DEBUG): ?>
                            <div class="pull-right">
                                <input readonly type="text" name="val[order][<?php echo $this->_aVars['aSetting']['var_name']; ?>]" value="<?php echo $this->_aVars['aSetting']['ordering']; ?>" class="input_xs_readonly" onclick="this.select();" size="2" />
                                <input readonly type="text" name="param<?php echo $this->_aVars['aSetting']['var_name']; ?>" value="<?php echo $this->_aVars['aSetting']['module_id']; ?>.<?php echo $this->_aVars['aSetting']['var_name']; ?>" class="input_xs_readonly" onclick="this.select();" />
                            </div>
<?php endif; ?>
                        <label class="setting-title"><?php echo _p($this->_aVars['aSetting']['setting_title']); ?></label>
<?php if (isset ( $this->_aVars['aSetting']['is_danger'] ) && $this->_aVars['aSetting']['is_danger']): ?>
                            <div class="alert alert-warning alert-labeled">
                                <div class="alert-labeled-row">
                                    <p class="alert-body alert-body-right alert-labelled-cell">
                                        <strong><?php echo _p("Warning"); ?></strong>
<?php echo _p("This is an important setting. Select a wrong option here can break the site or affect some features. If you are at all unsure about which option to configure, use the default value or contact us for support"); ?>.
                                    </p>
                                </div>
                            </div>
<?php endif; ?>
                        <div class="clear"></div>
<?php if ($this->_aVars['aSetting']['is_file_config'] == false): ?>
<?php if ($this->_aVars['aSetting']['type_id'] == 'multi_text'): ?>
<?php if (count((array)$this->_aVars['aSetting']['values'])):  foreach ((array) $this->_aVars['aSetting']['values'] as $this->_aVars['mKey'] => $this->_aVars['sDropValue']): ?>
                                    <div class="p_4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo $this->_aVars['mKey']; ?></span>
                                            <input class="form-control change_warning" type="text" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>][<?php echo $this->_aVars['mKey']; ?>]" value="<?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['sDropValue']); ?>"  />
                                        </div>
                                    </div>
<?php endforeach; endif; ?>
<?php elseif ($this->_aVars['aSetting']['type_id'] == 'currency'): ?>
<?php Phpfox::getBlock('core.currency', array('currency_field_name' => 'val[value]['.$this->_aVars['aSetting']['var_name'].']','value_actual' => $this->_aVars['aSetting']['values'])); ?>
<?php elseif ($this->_aVars['aSetting']['type_id'] == 'large_string' || $this->_aVars['aSetting']['type_id'] == 'big_string'): ?>
                                <textarea cols="60" rows="8" class="form-control change_warning" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]"><?php echo Phpfox::getLib('parse.output')->htmlspecialchars($this->_aVars['aSetting']['value_actual']); ?></textarea>
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'string' ) || $this->_aVars['aSetting']['type_id'] == 'input:text'): ?>
                                <div>
                                    <input type="text" class="form-control change_warning" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]" value="<?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aSetting']['value_actual']); ?>" size="40" />
                                </div>
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'password' )): ?>
                                <div>
                                    <input class="form-control change_warning" type="password" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]" value="<?php echo $this->_aVars['aSetting']['value_actual']; ?>" size="40" autocomplete="off" />
                                </div>
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'drop' )): ?>
                                <div>
                                    <input type="hidden" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>][real]" value="<?php echo $this->_aVars['aSetting']['value_actual']; ?>" size="40" />
                                </div>
                                <select name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>][value]" class="form-control change_warning">
<?php if (count((array)$this->_aVars['aSetting']['values']['values'])):  foreach ((array) $this->_aVars['aSetting']['values']['values'] as $this->_aVars['mKey'] => $this->_aVars['sDropValue']): ?>
                                        <option value="<?php echo $this->_aVars['sDropValue']; ?>" <?php if ($this->_aVars['aSetting']['values']['default'] == $this->_aVars['sDropValue']): ?>selected="selected"<?php endif; ?>>
<?php if (! empty ( $this->_aVars['sDropValue'] ) && ! stripos ( $this->_aVars['sDropValue'] , ' ' ) && ! stripos ( $this->_aVars['sDropValue'] , '.' )): ?>
<?php {$this->_aVars['sDropValue'] = strtolower($this->_aVars['sDropValue']);} ?>
<?php echo _p($this->_aVars['sDropValue']); ?>
<?php else: ?>
<?php echo _p($this->_aVars['sDropValue']); ?>
<?php endif; ?>
                                        </option>
<?php endforeach; endif; ?>
                                </select>
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'multi_checkbox' )): ?>
                                <input type="hidden" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>][]" value="core_multi_checkbox_off">
<?php if (count((array)$this->_aVars['aSetting']['values'])):  foreach ((array) $this->_aVars['aSetting']['values'] as $this->_aVars['mKey'] => $this->_aVars['sDropValue']): ?>
                                    <div class="custom-checkbox-wrapper">
                                        <label>
                                            <input type="checkbox" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>][]" value="<?php echo $this->_aVars['mKey']; ?>" <?php if (is_array ( $this->_aVars['aSetting']['value_actual'] ) && in_array ( $this->_aVars['mKey'] , $this->_aVars['aSetting']['value_actual'] )): ?>checked<?php endif; ?> />
                                            <span class="custom-checkbox"></span>
<?php echo _p($this->_aVars['sDropValue']); ?>
                                        </label>
                                    </div>
<?php endforeach; endif; ?>
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'drop_with_key' || $this->_aVars['aSetting']['type_id'] == 'select' )): ?>
                                <select name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]" class="form-control __data_option_<?php echo $this->_aVars['aSetting']['var_name']; ?> change_warning" data-rel="__data_option_<?php echo $this->_aVars['aSetting']['var_name']; ?>">
<?php if (count((array)$this->_aVars['aSetting']['values'])):  foreach ((array) $this->_aVars['aSetting']['values'] as $this->_aVars['mKey'] => $this->_aVars['sDropValue']): ?>
                                        <option value="<?php echo $this->_aVars['mKey']; ?>"<?php if ($this->_aVars['aSetting']['value_actual'] == $this->_aVars['mKey']): ?> selected="selected"<?php endif; ?>><?php echo _p($this->_aVars['sDropValue']); ?></option>
<?php endforeach; endif; ?>
                                </select>
<?php elseif ($this->_aVars['aSetting']['type_id'] == 'radio'): ?>
<?php if (count((array)$this->_aVars['aSetting']['values'])):  foreach ((array) $this->_aVars['aSetting']['values'] as $this->_aVars['mKey'] => $this->_aVars['sDropValue']): ?>
                                    <div class="custom-radio-wrapper">
                                        <label>
                                            <input class="change_warning" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]" type="radio" <?php if ($this->_aVars['aSetting']['value_actual'] == $this->_aVars['mKey']): ?> checked<?php endif; ?> value="<?php echo $this->_aVars['mKey']; ?>" />
                                            <span class="custom-radio"></span>
<?php echo _p($this->_aVars['sDropValue']); ?>
                                        </label>
                                    </div>
<?php endforeach; endif; ?>
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'integer' )): ?>
                                <input class="form-control change_warning" type="text" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]" value="<?php echo $this->_aVars['aSetting']['value_actual']; ?>" size="40" onclick="this.select();" />
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'boolean' ) || $this->_aVars['aSetting']['type_id'] == 'input:radio'): ?>
                                <div class="item_is_active_holder">
                                    <span class="js_item_active item_is_active hide">
                                        <input class="change_warning" type="radio" value="1" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]"<?php if ($this->_aVars['aSetting']['value_actual'] == 1): ?> checked="checked"<?php endif; ?>>
                                    </span>
                                    <span class="js_item_active item_is_not_active hide">
                                        <input class="change_warning" type="radio" value="0" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>]"<?php if ($this->_aVars['aSetting']['value_actual'] != 1): ?> checked="checked"<?php endif; ?>>
                                    </span>
                                </div>
<?php elseif (( $this->_aVars['aSetting']['type_id'] == 'array' )): ?>
                                <div class="js_array_holder">
<?php if (is_array ( $this->_aVars['aSetting']['values'] )): ?>
<?php if (count((array)$this->_aVars['aSetting']['values'])):  foreach ((array) $this->_aVars['aSetting']['values'] as $this->_aVars['iKey'] => $this->_aVars['sValue']): ?>
                                            <div class="p_4" class="js_array<?php echo $this->_aVars['iKey']; ?>">
                                                <div class="input-group">
                                                    <input type="text" name="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>][]" value="<?php echo $this->_aVars['sValue']; ?>" size="120" class="form-control change_warning" />
                                                    <span class="input-group-btn">
                                                        <a class="btn btn-danger" data-cmd="admincp.site_setting_remove_input" data-rel="setting=<?php echo $this->_aVars['aSetting']['var_name']; ?>&value=<?php echo $this->_aVars['sValue']; ?>" title="<?php echo _p('remove'); ?>"><i class="fa fa-remove"></i> </a>
                                                    </span>
                                                </div>
                                            </div>
<?php endforeach; endif; ?>
<?php endif; ?>
                                    <div class="js_array_data"></div>
                                    <div class="js_array_count" style="display:none;"><?php if (isset ( $this->_aVars['iKey'] )):  echo $this->_aVars['iKey'];  endif; ?></div>
                                    <br />
                                    <div class="p_4">
                                        <div class="input-group">
                                            <input type="text" name="" placeholder="<?php echo _p('add_a_new_value', array('phpfox_squote' => true)); ?>" size="30" class="js_add_to_array form-control" />
                                            <span class="input-group-btn">
                                                <input type="button" value="<?php echo _p('add'); ?>" class="btn btn-primary" data-cmd="admincp.site_setting_add_input" data-rel="val[value][<?php echo $this->_aVars['aSetting']['var_name']; ?>][]" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
<?php endif; ?>
<?php else: ?>
<?php if (( $this->_aVars['aSetting']['type_id'] == 'drop_with_key' || $this->_aVars['aSetting']['type_id'] == 'select' )): ?>
                                <input type="hidden" class="__data_option_<?php echo $this->_aVars['aSetting']['var_name']; ?>" value="<?php echo $this->_aVars['aSetting']['value_actual']; ?>">
<?php endif; ?>
                            <div class="alert alert-info alert-labeled">
                                <div class="alert-labeled-row">
                                    <p class="alert-body alert-body-right alert-labelled-cell">
                                        <strong><?php echo _p("info"); ?></strong>
<?php echo _p('this_configuration_is_set_in_a_configuration_file'); ?>
                                    </p>
                                </div>
                            </div>
<?php endif; ?>
                        <p class="help-block">
<?php echo _p($this->_aVars['aSetting']['setting_info']); ?>
                        </p>
                    </div>
<?php endforeach; endif; ?>
<?php if ($this->_aVars['sGroupId'] == 'mail'): ?>
                    <div class="form-group lines">
                        <label><?php echo _p("Send a Test Email"); ?></label>
                        <input class="form-control" type="text" value="" name="val[email_send_test]" placeholder="<?php echo _p('To'); ?>"/>
                        <p class="help-block">
<?php echo _p("Type an email address here and then click Send Test to generate a test email"); ?>
                        </p>
                    </div>
<?php endif; ?>

<?php if (count ( $this->_aVars['aSettings'] )): ?>
                    <div class="form-group lines form-group-save-changes">
                        <button type="submit" class="btn btn-primary"><?php echo _p('Save Changes'); ?></button>
<?php if ($this->_aVars['sGroupId'] == 'mail'): ?>
                            <button type="submit" name="test" value="test" class="btn btn-primary"><?php echo _p('Test'); ?></button>
<?php endif; ?>
                    </div>
<?php endif; ?>
            </div>
        </div>
    
</form>

<?php else: ?>
    <div class="alert alert-empty"><?php echo _p('setting_group_avaliable_settings'); ?></div>
<?php endif; ?>

