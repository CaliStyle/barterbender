<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 21, 2020, 11:26 pm */ ?>
<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox LLC
 * @package 		Phpfox
 * @version 		$Id: form.html.php 977 2009-09-12 15:29:04Z phpFox LLC $
 */
 
 

?>
<div class="form-group <?php if (! PHPFOX_IS_TECHIE): ?>hide<?php endif; ?>">
	<label class="<?php if ($this->_aVars['bProductIsRequired']): ?>required<?php endif; ?>" for="<?php if (! $this->_aVars['bUseClass']): ?>product_id<?php endif; ?>"><?php echo _p('product'); ?></label>
    <select name="val[product_id]" class="form-control" <?php if ($this->_aVars['bUseClass']): ?>class<?php else: ?>id<?php endif; ?>="product_id">
<?php if (count((array)$this->_aVars['aProducts'])):  foreach ((array) $this->_aVars['aProducts'] as $this->_aVars['aProduct']): ?>
        <option value="<?php echo $this->_aVars['aProduct']['product_id']; ?>"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('product_id') && in_array('product_id', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['product_id'])
								&& $aParams['product_id'] == $this->_aVars['aProduct']['product_id'])

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['product_id'])
									&& !isset($aParams['product_id'])
									&& (($this->_aVars['aForms']['product_id'] == $this->_aVars['aProduct']['product_id']) || (is_array($this->_aVars['aForms']['product_id']) && in_array($this->_aVars['aProduct']['product_id'], $this->_aVars['aForms']['product_id']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo $this->_aVars['aProduct']['title']; ?></option>
<?php endforeach; endif; ?>
    </select>
</div>
