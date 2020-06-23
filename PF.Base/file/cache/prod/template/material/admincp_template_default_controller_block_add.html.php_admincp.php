<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 21, 2020, 11:26 pm */ ?>
<?php 

?>

<?php echo '
<script type="text/javascript">
	function changeBlockType(oObj)
	{
		$(\'.js_block_type\').hide();
		$(\'.js_block_type_id_\' + oObj.value).show();		
	}
</script>
'; ?>

<?php echo $this->_aVars['sCreateJs']; ?>
<form method="post" id="js_form" onsubmit="<?php echo $this->_aVars['sGetJsForm']; ?>" class="form" action="<?php echo Phpfox::getLib('phpfox.url')->makeUrl("admincp.block.add"); ?>">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title"><?php if ($this->_aVars['bIsEdit']): ?>Edit Block <strong>#<?php echo $this->_aVars['aForms']['block_id']; ?></strong><?php else: ?> Add Block <?php endif; ?></div>
    </div>
    <div class="panel-body">
<?php if ($this->_aVars['bIsEdit']): ?>
        <input type="hidden" name="block_id" value="<?php echo $this->_aVars['aForms']['block_id']; ?>" />
<?php endif; ?>
<?php if (! Phpfox ::getUserParam('admincp.can_view_product_options')): ?>
        <input type="hidden" name="val[product_id]" value="1" />
<?php endif; ?>
<?php if (Phpfox ::getUserParam('admincp.can_view_product_options')): ?>
<?php Phpfox::getBlock('admincp.product.form', array()); ?>
<?php endif; ?>
<?php Phpfox::getBlock('admincp.module.form', array('module_form_required' => false)); ?>
        <div class="form-group">
            <label for="title">
<?php if (( $this->_aVars['bIsEdit'] && isset ( $this->_aVars['aForms'] ) && $this->_aVars['aForms']['type_id'] == 5 )): ?>
<?php echo _p("App Callback"); ?>
<?php else: ?>
<?php echo _p('title'); ?>
<?php endif; ?>
            </label>
            <input class="form-control" id="title" type="text" name="val[title]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['title']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['title']) : (isset($this->_aVars['aForms']['title']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['title']) : '')); ?>
" size="30" />
        </div>
<?php if ($this->_aVars['bIsEdit'] && $this->_aVars['aForms']['type_id'] == 5): ?>
        <div><input type="hidden" name="val[type_id]" value="<?php echo $this->_aVars['aForms']['type_id']; ?>"></div>
<?php else: ?>
        <div class="form-group <?php if ($this->_aVars['bIsEdit']): ?>hide<?php endif; ?>">
            <label for="table_left"><?php echo _p('type'); ?></label>
            <select name="val[type_id]" onchange="return changeBlockType(this);" class="form-control">
                <option value="0"><?php echo _p('select'); ?>:</option>
                <option value="0"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('type_id') && in_array('type_id', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['type_id'])
								&& $aParams['type_id'] == '0')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['type_id'])
									&& !isset($aParams['type_id'])
									&& (($this->_aVars['aForms']['type_id'] == '0') || (is_array($this->_aVars['aForms']['type_id']) && in_array('0', $this->_aVars['aForms']['type_id']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('php_block_file'); ?></option>
                <option value="1"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('type_id') && in_array('type_id', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['type_id'])
								&& $aParams['type_id'] == '1')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['type_id'])
									&& !isset($aParams['type_id'])
									&& (($this->_aVars['aForms']['type_id'] == '1') || (is_array($this->_aVars['aForms']['type_id']) && in_array('1', $this->_aVars['aForms']['type_id']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('php_code'); ?></option>
                <option value="2"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('type_id') && in_array('type_id', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['type_id'])
								&& $aParams['type_id'] == '2')

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['type_id'])
									&& !isset($aParams['type_id'])
									&& (($this->_aVars['aForms']['type_id'] == '2') || (is_array($this->_aVars['aForms']['type_id']) && in_array('2', $this->_aVars['aForms']['type_id']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('html_code'); ?></option>
            </select>
        </div>
<?php endif; ?>
        <div class="form-group js_core_init_selectize_form_group">
            <label for="m_connection"><?php echo _p('connection_page'); ?></label>
            <select name="val[m_connection]" id="m_connection" class="form-control">
<?php if (! $this->_aVars['bIsEdit']): ?>
                <option value=""><?php echo _p('select'); ?>:</option>
<?php endif; ?>
                <option value=""><?php echo _p('none_site_wide'); ?></option>
<?php if (count((array)$this->_aVars['aControllers'])):  foreach ((array) $this->_aVars['aControllers'] as $this->_aVars['sName'] => $this->_aVars['aController']): ?>
                <optgroup label="<?php echo Phpfox::getLib('phpfox.locale')->translate($this->_aVars['sName'], 'module'); ?>">
<?php if (count((array)$this->_aVars['aController'])):  foreach ((array) $this->_aVars['aController'] as $this->_aVars['aCont']): ?>
                <option value="<?php echo $this->_aVars['aCont']['m_connection']; ?>"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('m_connection') && in_array('m_connection', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['m_connection'])
								&& $aParams['m_connection'] == $this->_aVars['aCont']['m_connection'])

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['m_connection'])
									&& !isset($aParams['m_connection'])
									&& (($this->_aVars['aForms']['m_connection'] == $this->_aVars['aCont']['m_connection']) || (is_array($this->_aVars['aForms']['m_connection']) && in_array($this->_aVars['aCont']['m_connection'], $this->_aVars['aForms']['m_connection']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
>
<?php echo _p('controller_'.$this->_aVars['aCont']['m_connection']); ?> (<?php echo $this->_aVars['aCont']['m_connection']; ?>)</option>
<?php endforeach; endif; ?>
                </optgroup>
<?php endforeach; endif; ?>
            </select>
<?php Phpfox::getBlock('help.popup', array('phrase' => 'admincp.block_add_connection')); ?>
        </div>
<?php if ($this->_aVars['bIsEdit'] && $this->_aVars['aForms']['type_id'] == 5): ?>
        <input type="hidden" name="val[component]" value="<?php echo $this->_aVars['aForms']['component']; ?>">
<?php else: ?>
        <div class="form-group js_core_init_selectize_form_group js_block_type js_block_type_id_0 <?php if ($this->_aVars['bIsEdit'] && $this->_aVars['aForms']['type_id'] > 0): ?>hide<?php endif; ?>">
            <label><?php echo _p('component'); ?></label>
            <select name="val[component]" id="component" class="form-control">
                <option value=""><?php echo _p('select'); ?>:</option>
<?php if (count((array)$this->_aVars['aComponents'])):  foreach ((array) $this->_aVars['aComponents'] as $this->_aVars['sName'] => $this->_aVars['aComponent']): ?>
                <optgroup label="<?php echo Phpfox::getLib('phpfox.locale')->translate($this->_aVars['sName'], 'module'); ?>">
<?php if (count((array)$this->_aVars['aComponent'])):  foreach ((array) $this->_aVars['aComponent'] as $this->_aVars['aComp']): ?>
                <option value="<?php echo $this->_aVars['sName']; ?>|<?php echo $this->_aVars['aComp']['component']; ?>"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('component') && in_array('component', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['component'])
								&& $aParams['component'] == $this->_aVars['sName'].'|'.$this->_aVars['aComp']['component'])

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['component'])
									&& !isset($aParams['component'])
									&& (($this->_aVars['aForms']['component'] == $this->_aVars['sName'].'|'.$this->_aVars['aComp']['component']) || (is_array($this->_aVars['aForms']['component']) && in_array($this->_aVars['sName'].'|'.$this->_aVars['aComp']['component'], $this->_aVars['aForms']['component']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
>-- <?php echo $this->_aVars['aComp']['component']; ?></option>
<?php endforeach; endif; ?>
                </optgroup>
<?php endforeach; endif; ?>
            </select>
<?php Phpfox::getBlock('help.popup', array('phrase' => 'admincp.block_add_component')); ?>
        </div>
<?php endif; ?>
        <div class="form-group">
            <label for="location"><?php echo _p('placement'); ?> <?php if (Phpfox ::isAdmin()): ?> <a href="#?call=theme.sample&amp;width=1300" class="inlinePopup" title="<?php echo _p('sample_layout'); ?>"><?php echo _p('view_sample_layout'); ?></a><?php endif; ?></label>
            <select name="val[location]" id="location" class="form-control">
<?php for ($this->_aVars['i'] = 1; $this->_aVars['i'] <= 12; $this->_aVars['i']++): ?>
                <option value="<?php echo $this->_aVars['i']; ?>"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));


if (isset($this->_aVars['aField']) && isset($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]) && !is_array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]))
							{
								$this->_aVars['aForms'][$this->_aVars['aField']['field_id']] = array($this->_aVars['aForms'][$this->_aVars['aField']['field_id']]);
							}

if (isset($this->_aVars['aForms'])
 && is_numeric('location') && in_array('location', $this->_aVars['aForms']))
							
{
								echo ' selected="selected" ';
							}

							if (isset($aParams['location'])
								&& $aParams['location'] == $this->_aVars['i'])

							{

								echo ' selected="selected" ';

							}

							else

							{

								if (isset($this->_aVars['aForms']['location'])
									&& !isset($aParams['location'])
									&& (($this->_aVars['aForms']['location'] == $this->_aVars['i']) || (is_array($this->_aVars['aForms']['location']) && in_array($this->_aVars['i'], $this->_aVars['aForms']['location']))))
								{
								 echo ' selected="selected" ';
								}
								else
								{
									echo "";
								}
							}
							?>
><?php echo _p('block_location_x', array('x' => $this->_aVars['i'])); ?></option>
<?php endfor; ?>
            </select>
<?php Phpfox::getBlock('help.popup', array('phrase' => 'admincp.block_add_placement')); ?>
        </div>

        <div class="form-group hide custom-radio-wrapper">
            <label for=""><?php echo _p('can_drag_drop'); ?></label>
            <label>
                <input type="radio" name="val[can_move]" value="1"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));
if (isset($this->_aVars['aForms']) && is_numeric('can_move') && in_array('can_move', $this->_aVars['aForms']) ){echo ' checked="checked"';}
if ((isset($aParams['can_move']) && $aParams['can_move'] == '1'))
{echo ' checked="checked" ';}
else
{
 if (isset($this->_aVars['aForms']) && isset($this->_aVars['aForms']['can_move']) && !isset($aParams['can_move']) && $this->_aVars['aForms']['can_move'] == '1')
 {
    echo ' checked="checked" ';}
 else
 {
 }
}
?> 
/>
                <span class="custom-radio"></span>
<?php echo _p('yes'); ?>
            </label>
            <label>
                <input type="radio" name="val[can_move]" value="0"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));
if (isset($this->_aVars['aForms']) && is_numeric('can_move') && in_array('can_move', $this->_aVars['aForms']) ){echo ' checked="checked"';}
if ((isset($aParams['can_move']) && $aParams['can_move'] == '0'))
{echo ' checked="checked" ';}
else
{
 if (isset($this->_aVars['aForms']) && isset($this->_aVars['aForms']['can_move']) && !isset($aParams['can_move']) && $this->_aVars['aForms']['can_move'] == '0')
 {
    echo ' checked="checked" ';}
 else
 {
 if (!isset($this->_aVars['aForms']) || ((isset($this->_aVars['aForms']) && !isset($this->_aVars['aForms']['can_move']) && !isset($aParams['can_move']))))
{
 echo ' checked="checked"';
}
 }
}
?> 
/>
                <span class="custom-radio"></span>
<?php echo _p('no'); ?>
            </label>
        </div>

        <div class="form-group hide custom-radio-wrapper">
            <label for=""><?php echo _p('active'); ?></label>
            <label>
                <input type="radio" name="val[is_active]" value="1"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));
if (isset($this->_aVars['aForms']) && is_numeric('is_active') && in_array('is_active', $this->_aVars['aForms']) ){echo ' checked="checked"';}
if ((isset($aParams['is_active']) && $aParams['is_active'] == '1'))
{echo ' checked="checked" ';}
else
{
 if (isset($this->_aVars['aForms']) && isset($this->_aVars['aForms']['is_active']) && !isset($aParams['is_active']) && $this->_aVars['aForms']['is_active'] == '1')
 {
    echo ' checked="checked" ';}
 else
 {
 if (!isset($this->_aVars['aForms']) || ((isset($this->_aVars['aForms']) && !isset($this->_aVars['aForms']['is_active']) && !isset($aParams['is_active']))))
{
 echo ' checked="checked"';
}
 }
}
?> 
/>
                <span class="custom-radio"></span>
<?php echo _p('yes'); ?>
            </label>
            <label>
                <input type="radio" name="val[is_active]" value="0"<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val'));
if (isset($this->_aVars['aForms']) && is_numeric('is_active') && in_array('is_active', $this->_aVars['aForms']) ){echo ' checked="checked"';}
if ((isset($aParams['is_active']) && $aParams['is_active'] == '0'))
{echo ' checked="checked" ';}
else
{
 if (isset($this->_aVars['aForms']) && isset($this->_aVars['aForms']['is_active']) && !isset($aParams['is_active']) && $this->_aVars['aForms']['is_active'] == '0')
 {
    echo ' checked="checked" ';}
 else
 {
 }
}
?> 
/>
                <span class="custom-radio"></span>
<?php echo _p('no'); ?>
            </label>
<?php Phpfox::getBlock('help.popup', array('phrase' => 'admincp.block_add_active')); ?>
        </div>

        <div class="js_block_type js_block_type_id_1 js_block_type_id_2 <?php if ($this->_aVars['bIsEdit'] && ( $this->_aVars['aForms']['type_id'] == 0 || $this->_aVars['aForms']['type_id'] == 5 )): ?> hide<?php endif; ?>">
            <div class="form-group">
                <label for="source_code"><?php echo _p('php_html_code_optional'); ?></label>
                <textarea class="form-control" name="val[source_code]" rows="8" id="source_code"><?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['source_code']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['source_code']) : (isset($this->_aVars['aForms']['source_code']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['source_code']) : '')); ?>
</textarea>
            </div>
        </div>

        <div class="form-group">
            <label for=""><?php echo _p('allow_access'); ?></label>
<?php if (count((array)$this->_aVars['aUserGroups'])):  foreach ((array) $this->_aVars['aUserGroups'] as $this->_aVars['aUserGroup']): ?>
            <div class="custom-checkbox-wrapper">
                <label>
                    <input type="checkbox" name="val[allow_access][]" value="<?php echo $this->_aVars['aUserGroup']['user_group_id']; ?>"<?php if (isset ( $this->_aVars['aAccess'] ) && is_array ( $this->_aVars['aAccess'] )):  if (! in_array ( $this->_aVars['aUserGroup']['user_group_id'] , $this->_aVars['aAccess'] )): ?> checked="checked" <?php endif;  else: ?> checked="checked" <?php endif; ?>/>
                    <span class="custom-checkbox"></span>
<?php echo Phpfox::getLib('phpfox.parse.output')->clean(Phpfox::getLib('locale')->convert($this->_aVars['aUserGroup']['title'])); ?>
                </label>
            </div>
<?php endforeach; endif; ?>
<?php Phpfox::getBlock('help.popup', array('phrase' => 'admincp.block_add_access')); ?>
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" value="_submit" class="btn btn-primary"><?php echo _p('submit'); ?></button>
    </div>
</div>

</form>

