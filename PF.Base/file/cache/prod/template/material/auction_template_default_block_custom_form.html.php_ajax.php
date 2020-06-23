<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 1, 2020, 8:34 pm */ ?>
<?php if (count ( $this->_aVars['aCustomFields'] )): ?>
<?php if (count((array)$this->_aVars['aCustomFields'])):  foreach ((array) $this->_aVars['aCustomFields'] as $this->_aVars['aField']): ?>
        <div class="form-group">
            <div class="table_left">
<?php if ($this->_aVars['aField']['is_required'] == 1): ?>*<?php endif;  echo _p($this->_aVars['aField']['phrase_var_name']); ?>
            </div>
            <div class="table_right">
<?php if ($this->_aVars['aField']['var_type'] == 'text'): ?>
                <input class="form-control" <?php if ($this->_aVars['aField']['is_required'] == 1): ?>data-isrequired="1"<?php else: ?>data-isrequired="0"<?php endif; ?> data-type="text" id="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>" type="text" name="val[custom][<?php echo $this->_aVars['aField']['field_id']; ?>]" maxlength="255" <?php if (isset ( $this->_aVars['aField']['value'] )): ?> value = "<?php echo $this->_aVars['aField']['value']; ?>" <?php endif; ?> />
                
<?php elseif ($this->_aVars['aField']['var_type'] == 'textarea'): ?>
                <textarea class="form-control" <?php if ($this->_aVars['aField']['is_required'] == 1): ?>data-isrequired="1"<?php else: ?>data-isrequired="0"<?php endif; ?> data-type="textarea" id="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>" cols="35" rows="4" name="val[custom][<?php echo $this->_aVars['aField']['field_id']; ?>]"><?php if (isset ( $this->_aVars['aField']['value'] )): ?> <?php echo $this->_aVars['aField']['value']; ?> <?php endif; ?></textarea>
                
<?php elseif ($this->_aVars['aField']['var_type'] == 'select'): ?>
                <select class="form-control" <?php if ($this->_aVars['aField']['is_required'] == 1): ?>data-isrequired="1"<?php else: ?>data-isrequired="0"<?php endif; ?> data-type="select" id="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>" name="val[custom][<?php echo $this->_aVars['aField']['field_id']; ?>][]"  >
<?php if (! $this->_aVars['aField']['is_required']): ?>
                    <option value=""><?php echo _p('directory.select'); ?>:</option>
<?php endif; ?>
<?php if (count((array)$this->_aVars['aField']['option'])):  foreach ((array) $this->_aVars['aField']['option'] as $this->_aVars['opId'] => $this->_aVars['opPhrase']): ?>
<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['selected'] => $this->_aVars['value_selected']): ?>
                            <option value="<?php echo $this->_aVars['opId']; ?>" <?php if ($this->_aVars['opId'] == $this->_aVars['selected']): ?> selected = "selected" <?php endif; ?> > <?php echo _p($this->_aVars['opPhrase']); ?></option> 
<?php endforeach; endif; ?>
<?php else: ?>
                            <option value="<?php echo $this->_aVars['opId']; ?>" > <?php echo _p($this->_aVars['opPhrase']); ?></option> 
<?php endif; ?>
<?php endforeach; endif; ?>

                </select>
                
<?php elseif ($this->_aVars['aField']['var_type'] == 'multiselect'): ?>
                <select class="form-control" <?php if ($this->_aVars['aField']['is_required'] == 1): ?>data-isrequired="1"<?php else: ?>data-isrequired="0"<?php endif; ?> data-type="multiselect" id="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>" name="val[custom][<?php echo $this->_aVars['aField']['field_id']; ?>][]" size="4" multiple="yes">
<?php if (count((array)$this->_aVars['aField']['option'])):  foreach ((array) $this->_aVars['aField']['option'] as $this->_aVars['opId'] => $this->_aVars['opPhrase']): ?>
                    <option value="<?php echo $this->_aVars['opId']; ?>"  

<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['selected'] => $this->_aVars['value_selected']): ?>
<?php if ($this->_aVars['opId'] == $this->_aVars['selected']): ?>
                                selected="selected" 
<?php endif; ?>
<?php endforeach; endif; ?>
<?php endif; ?>

                     ><?php echo _p($this->_aVars['opPhrase']); ?></option>
<?php endforeach; endif; ?>
                </select>
                
<?php elseif ($this->_aVars['aField']['var_type'] == 'checkbox'): ?>
<?php if (count((array)$this->_aVars['aField']['option'])):  foreach ((array) $this->_aVars['aField']['option'] as $this->_aVars['opId'] => $this->_aVars['opPhrase']): ?>
                    <div class="checkbox">
                        <label for="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>"><input <?php if ($this->_aVars['aField']['is_required'] == 1): ?>data-isrequired="1"<?php else: ?>data-isrequired="0"<?php endif; ?> data-type="checkbox" id="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>" type="checkbox" name="val[custom][<?php echo $this->_aVars['aField']['field_id']; ?>][]" value="<?php echo $this->_aVars['opId']; ?>" 
<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['checked'] => $this->_aVars['value_checked']): ?>
<?php if ($this->_aVars['opId'] == $this->_aVars['checked']): ?>
                                checked="checked" 
<?php endif; ?>
<?php endforeach; endif; ?>
<?php endif; ?>
                          /> <?php echo _p($this->_aVars['opPhrase']); ?></label></div>
<?php endforeach; endif; ?>
                
<?php elseif ($this->_aVars['aField']['var_type'] == 'radio'): ?>
<?php if (count((array)$this->_aVars['aField']['option'])):  foreach ((array) $this->_aVars['aField']['option'] as $this->_aVars['opId'] => $this->_aVars['opPhrase']): ?>
<?php if (isset ( $this->_aVars['aField']['value'] )): ?>
<?php if (count((array)$this->_aVars['aField']['value'])):  foreach ((array) $this->_aVars['aField']['value'] as $this->_aVars['checked'] => $this->_aVars['value_checked']): ?>
                     	<div class="radio">
                        <label for="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>"><input <?php if ($this->_aVars['aField']['is_required'] == 1): ?>data-isrequired="1"<?php else: ?>data-isrequired="0"<?php endif; ?> data-type="radio" id="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>" type="radio" name="val[custom][<?php echo $this->_aVars['aField']['field_id']; ?>][]" value="<?php echo $this->_aVars['opId']; ?>" <?php if ($this->_aVars['opId'] == $this->_aVars['checked']): ?> checked <?php endif; ?> /> <?php echo _p($this->_aVars['opPhrase']); ?></label></div>
<?php endforeach; endif; ?>
<?php else: ?>
                        <div class="radio">
                    	<label for="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>"><input <?php if ($this->_aVars['aField']['is_required'] == 1): ?>data-isrequired="1"<?php else: ?>data-isrequired="0"<?php endif; ?> data-type="radio" id="js_jp_cf_<?php echo $this->_aVars['aField']['field_id']; ?>" type="radio" name="val[custom][<?php echo $this->_aVars['aField']['field_id']; ?>][]" value="<?php echo $this->_aVars['opId']; ?>" /> <?php echo _p($this->_aVars['opPhrase']); ?></label></div>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php endif; ?>
            </div>
        </div>
<?php endforeach; endif;  endif; ?>

