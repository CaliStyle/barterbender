<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 20, 2020, 7:50 pm */ ?>
<?php

?>
<table class="table table-admin" id="list_apps">
    <thead>
    <tr>
        <th class="w30"></th>
        <th id="app_column_index" class="sortable" onclick="$Core.sortTable(this, 'list_apps');">
<?php echo _p("name"); ?>
        </th>
        <th class="w120 sortable" onclick="$Core.sortTable(this, 'list_apps');"><?php echo _p("version"); ?></th>
        <th class="w120 sortable" onclick="$Core.sortTable(this, 'list_apps');"><?php echo _p("latest"); ?></th>
        <th class="sortable" onclick="$Core.sortTable(this, 'list_apps');"><?php echo _p("author"); ?></th>
        <th class="w80 text-center"><?php echo _p("Active"); ?></th>
        <th class="w80 text-center"><?php echo _p('settings'); ?></th>
    </tr>
    </thead>
    <tbody>
<?php if (count((array)$this->_aVars['apps'])):  foreach ((array) $this->_aVars['apps'] as $this->_aVars['app']): ?>
            <tr>
                <td><?php if ($this->_aVars['app']['is_active']): ?><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.app', array('id' => $this->_aVars['app']['id'])); ?>"><?php endif;  echo $this->_aVars['app']['icon'];  if ($this->_aVars['app']['is_active']): ?></a><?php endif; ?>
                </td>
                <td>
<?php if ($this->_aVars['app']['is_active']): ?><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.app', array('id' => $this->_aVars['app']['id'])); ?>"><?php endif; ?>
<?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['app']['name']); ?>
<?php if ($this->_aVars['app']['is_active']): ?></a><?php endif; ?>
                </td>
                <td>
<?php if ($this->_aVars['app']['is_phpfox_default']): ?>
<?php echo _p('core'); ?>
<?php else: ?>
<?php echo $this->_aVars['app']['version']; ?>
<?php endif; ?>
                </td>
                <td>
<?php if ($this->_aVars['app']['is_phpfox_default']): ?>
<?php echo _p('core'); ?>
<?php elseif (! empty ( $this->_aVars['app']['latest_version'] )): ?>
<?php echo $this->_aVars['app']['latest_version']; ?>
<?php endif; ?>
<?php if (isset ( $this->_aVars['app']['have_new_version'] ) && $this->_aVars['app']['have_new_version']): ?>
                    <br />
                    <a href="<?php echo $this->_aVars['app']['have_new_version']; ?>">
<?php echo _p('upgrade_now'); ?>
                    </a>
<?php endif; ?>
                </td>
                <td>
<?php if (! empty ( $this->_aVars['app']['publisher_url'] )): ?>
                    <a href="<?php echo $this->_aVars['app']['publisher_url']; ?>" target="_blank">
<?php endif; ?>
<?php echo $this->_aVars['app']['publisher']; ?>
<?php if (! empty ( $this->_aVars['app']['publisher_url'] )): ?>
                    </a>
<?php endif; ?>
                </td>
                <td class="on_off">
<?php if ($this->_aVars['app']['allow_disable']): ?>
                        <div class="js_item_is_active <?php if (! $this->_aVars['app']['is_active']): ?>hide<?php endif; ?>">
                            <a href="#?call=admincp.updateModuleActivity&amp;id=<?php echo $this->_aVars['app']['id']; ?>&amp;active=0" class="js_item_active_link" title="<?php echo _p('deactivate'); ?>"></a>
                        </div>
                        <div class="js_item_is_not_active <?php if ($this->_aVars['app']['is_active']): ?>hide<?php endif; ?>">
                            <a href="#?call=admincp.updateModuleActivity&amp;id=<?php echo $this->_aVars['app']['id']; ?>&amp;active=1" class="js_item_active_link" title="<?php echo _p('activate'); ?>"></a>
                        </div>
<?php endif; ?>
                </td>
                <td class="text-center">
<?php if (( ! $this->_aVars['app']['is_module'] && $this->_aVars['bIsTechie'] ) || ( ! $this->_aVars['app']['is_core'] && $this->_aVars['app']['allow_disable'] )): ?>
                        <a class="js_drop_down_link" role="button"></a>
                        <div class="link_menu">
                            <ul class="dropdown-menu dropdown-menu-right">
<?php if (! $this->_aVars['app']['is_module'] && $this->_aVars['bIsTechie']): ?>
                                    <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.app', array('id' => $this->_aVars['app']['id'],'verify' => 1,'home' => 1)); ?>"><?php echo _p("re-validation"); ?></a></li>
                                    <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.app', array('id' => $this->_aVars['app']['id'],'export' => 1)); ?>"><?php echo _p("Export"); ?></a></li>
<?php endif; ?>
<?php if (! $this->_aVars['app']['is_core'] && $this->_aVars['app']['allow_disable']): ?>
                                    <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.app', array('id' => $this->_aVars['app']['id'],'uninstall' => 'yes')); ?>" data-message="<?php echo _p('are_you_sure', array('phpfox_squote' => true)); ?>" class="sJsConfirm"><?php echo _p('uninstall'); ?></a></li>
<?php endif; ?>
                            </ul>
                        </div>
<?php endif; ?>
                </td>
            </tr>
<?php endforeach; endif; ?>
    </tbody>
</table>

