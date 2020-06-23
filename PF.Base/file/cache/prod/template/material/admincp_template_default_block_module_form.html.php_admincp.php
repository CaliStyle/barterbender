<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 21, 2020, 11:26 pm */ ?>
<?php 
/*might not use*/
 

?>
<div class="form-group js_core_init_selectize_form_group"<?php if (! PHPFOX_IS_TECHIE): ?> style="display:none;"<?php endif; ?>>
	<label for="<?php if (! $this->_aVars['bUseClass']):  echo $this->_aVars['sModuleFormId'];  endif; ?>">*
<?php if ($this->_aVars['bModuleFormRequired']):  endif;  echo $this->_aVars['sModuleFormTitle']; ?>
    </label>
    <select name="val[<?php echo $this->_aVars['sModuleFormId']; ?>]" <?php if ($this->_aVars['bUseClass']): ?>class<?php else: ?>id<?php endif; ?>="<?php echo $this->_aVars['sModuleFormId']; ?>" class="form-control">
        <option value=""><?php echo $this->_aVars['sModuleFormValue']; ?></option>
<?php if (count((array)$this->_aVars['aModules'])):  foreach ((array) $this->_aVars['aModules'] as $this->_aVars['sModule'] => $this->_aVars['iModuleId']): ?>
            <option value="<?php echo $this->_aVars['sModule']; ?>"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric(''.$this->_aVars['sModuleFormId'].'') && in_array(''.$this->_aVars['sModuleFormId'].'', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams[''.$this->_aVars['sModuleFormId'].''])
								&& $aParams[''.$this->_aVars['sModuleFormId'].''] == $this->_aVars['sModule'])

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms'][''.$this->_aVars['sModuleFormId'].''])
									&& !isset($aParams[''.$this->_aVars['sModuleFormId'].''])
									&& (($this->_aVars['aForms'][''.$this->_aVars['sModuleFormId'].''] == $this->_aVars['sModule']) || (is_array($this->_aVars['aForms'][''.$this->_aVars['sModuleFormId'].'']) && in_array($this->_aVars['sModule'], $this->_aVars['aForms'][''.$this->_aVars['sModuleFormId'].'']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo Phpfox::getLib('phpfox.locale')->translate($this->_aVars['sModule'], 'module'); ?></option>
<?php endforeach; endif; ?>
    </select>
</div>
