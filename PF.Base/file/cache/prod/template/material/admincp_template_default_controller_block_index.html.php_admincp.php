<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 21, 2020, 11:25 pm */ ?>
<?php 

?>

<form class="form" method="get">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group js_core_init_selectize_form_group">
                <label for="m_connection">
<?php echo _p('connection_page'); ?>
                </label>
                <select class="form-control" name="m_connection" id="m_connection">
<?php if (count((array)$this->_aVars['aBlocks'])):  foreach ((array) $this->_aVars['aBlocks'] as $this->_aVars['sUrl'] => $this->_aVars['aModule']): ?>
                    <option <?php if ($this->_aVars['sUrl'] == $this->_aVars['sConnection']): ?>selected<?php endif; ?> value="<?php if ($this->_aVars['sUrl'] == 'site_wide'): ?>site_wide<?php else:  echo $this->_aVars['sUrl'];  endif; ?>">
<?php if ($this->_aVars['sUrl'] == 'site_wide'):  echo _p('site_wide');  else: ?>
<?php echo _p('controller_'.$this->_aVars['sUrl']); ?> (<?php echo $this->_aVars['sUrl']; ?>)
<?php endif; ?>
                    </option>
<?php endforeach; endif; ?>
                </select>
            </div>
        </div>
    </div>

</form>

<div id="js_setting_block">
    <form method="post" class="form" action="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.user.group.add'); ?>" onsubmit="$('#js_setting_saved').html($.ajaxProcess('Saving')).show(); $(this).ajaxCall('user.updateSettings'); return false;">
<?php if (count((array)$this->_aVars['aModules'])):  foreach ((array) $this->_aVars['aModules'] as $this->_aVars['iBlock'] => $this->_aVars['aSubBlocks']): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
<?php echo _p('block_block_number', array('block_number' => $this->_aVars['iBlock'])); ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-admin js_drag_drop">
                        <thead>
                           <tr>
                               <th class="w60"><?php echo _p('id'); ?></th>
                               <th class="w30"></th>
                               <th class=""><?php echo _p('title'); ?></th>
                               <th class="w200"><?php echo _p('apps'); ?></th>
                               <th class="w100 text-center"><?php echo _p('active'); ?></th>
                               <th class="w80 t_center"><?php echo _p('settings'); ?></th>
                           </tr>
                        </thead>
                        <tbody>
<?php if (count((array)$this->_aVars['aSubBlocks'])):  foreach ((array) $this->_aVars['aSubBlocks'] as $this->_aVars['iKey'] => $this->_aVars['aBlock']): ?>
                        <tr>
                            <td><?php echo $this->_aVars['aBlock']['block_id']; ?></td>
                            <td class="drag_handle">
                                <input type="hidden" name="val[ordering][<?php echo $this->_aVars['aBlock']['block_id']; ?>]" value="<?php echo $this->_aVars['aBlock']['ordering']; ?>" />
                            </td>
                            <td>
                                <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.block.add', array('id' => $this->_aVars['aBlock']['block_id'],'m_connection' => $this->_aVars['sConnection'])); ?>">
<?php if (! empty ( $this->_aVars['aBlock']['title'] )): ?>
<?php echo $this->_aVars['aBlock']['title']; ?>
<?php else: ?>
<?php if ($this->_aVars['aBlock']['type_id'] > 0): ?>
<?php if ($this->_aVars['aBlock']['type_id'] == 1): ?>
<?php echo _p('php_code'); ?>
<?php else: ?>
<?php echo _p('html_code'); ?>
<?php endif; ?>
<?php else: ?>
<?php echo $this->_aVars['aBlock']['module_name']; ?>::<?php echo $this->_aVars['aBlock']['component']; ?>
<?php endif; ?>
<?php endif; ?>
                                </a>
                            </td>
                            <td class="w200">
<?php echo Phpfox::getLib('phpfox.locale')->translate($this->_aVars['aBlock']['module_name'], 'module'); ?>
                            </td>
                            <td class="on_off w100">
                                <div class="js_item_is_active"<?php if (! $this->_aVars['aBlock']['is_active']): ?> style="display:none;"<?php endif; ?>>
                                    <a href="#?call=admincp.updateBlockActivity&amp;id=<?php echo $this->_aVars['aBlock']['block_id']; ?>&amp;active=0" class="js_item_active_link" title="<?php echo _p('deactivate'); ?>"></a>
                                </div>
                                <div class="js_item_is_not_active"<?php if ($this->_aVars['aBlock']['is_active']): ?> style="display:none;"<?php endif; ?>>
                                    <a href="#?call=admincp.updateBlockActivity&amp;id=<?php echo $this->_aVars['aBlock']['block_id']; ?>&amp;active=1" class="js_item_active_link" title="<?php echo _p('activate'); ?>"></a>
                                </div>
                            </td>
                            <td class="text-center">
                                <a role="button" class="js_drop_down_link" title="<?php echo _p('manage'); ?>"></a>
                                <div class="link_menu">
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.block.add.', array('id' => $this->_aVars['aBlock']['block_id'],'m_connection' => $this->_aVars['sConnection'])); ?>"><?php echo _p('edit'); ?></a></li>
                                        <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.block.setting.', array('id' => $this->_aVars['aBlock']['block_id'],'m_connection' => $this->_aVars['sConnection'])); ?>"><?php echo _p('settings'); ?></a></li>
                                        <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.block.', array('delete' => $this->_aVars['aBlock']['block_id'],'m_connection' => $this->_aVars['sConnection'])); ?>" data-message="<?php echo _p('are_you_sure', array('phpfox_squote' => true)); ?>" class="sJsConfirm"><?php echo _p('delete'); ?></a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
<?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
<?php endforeach; endif; ?>
    
</form>

</div>
