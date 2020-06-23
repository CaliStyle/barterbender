<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 20, 2020, 7:51 pm */ ?>
<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.advancedfooter.index'); ?>"><?php echo _p('Manage Menu'); ?></a>
<?php if (isset ( $this->_aVars['sParentCategory'] )): ?>
                » <?php echo $this->_aVars['sParentCategory']; ?>
<?php endif; ?>
        </div>
    </div>
    <div style="margin:10px;">
        !Important - Add Maximum 4 main level menus, as more numbers will not work incorrectly. <br/>
        Sub menus you may find in left cog icon under main level menus.<br/>
        Leave Link / Direct link empty if you do not want add link to main level menu
    </div>
    <div style="margin: 10px;">
        <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.advancedfooter.addmenu'); ?>" class="button btn btn-primary popup">
<?php echo _p('Add Menu'); ?>
        </a>
    </div>
<?php if (! empty ( $this->_aVars['aCategories'] )): ?>
    <table id="_sort" data-sort-url="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('advancedfooter.admincp.menu.order'); ?>" class="table table-admin">
        <thead>
        <tr>
            <th style="width:20px"></th>
            <th style="width:20px"></th>
            <th><?php echo _p('Name'); ?></th>
            <th><?php echo _p('Link'); ?></th>
            <th><?php echo _p('Direct Link'); ?></th>
            <th class="text-center" style="width:60px;"><?php echo _p('Active'); ?></th>
        </tr>
        </thead>
        <tbody>
<?php if (count((array)$this->_aVars['aCategories'])):  foreach ((array) $this->_aVars['aCategories'] as $this->_aVars['iKey'] => $this->_aVars['aCategory']): ?>
        <tr class="tr" data-sort-id="<?php echo $this->_aVars['aCategory']['category_id']; ?>">
            <td class="t_center">
                <i class="fa fa-sort"></i>
            </td>
            <td class="text-center">
                <a class="js_drop_down_link" title="Manage"></a>
                <div class="link_menu">
                    <ul>
                        <li><a class="popup" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.advancedfooter.addmenu', array('edit' => $this->_aVars['aCategory']['category_id'])); ?>"><?php echo _p('Edit'); ?></a></li>
<?php if (isset ( $this->_aVars['aCategory']['sub'] ) && ( $this->_aVars['iTotalSub'] = count ( $this->_aVars['aCategory']['sub'] ) )): ?>
                        <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.advancedfooter.index', array('sub' => $this->_aVars['aCategory']['category_id'])); ?>"><?php echo _p('Manage Sub Menus'); ?> <span class="badge" style="display: initial;"><?php echo $this->_aVars['iTotalSub']; ?></span></a></li>
<?php endif; ?>
                        <li>
                            <a class="popup" href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.advancedfooter.delete-menu', array('delete' => $this->_aVars['aCategory']['category_id'])); ?>"><?php echo _p('Delete'); ?></a>
                        </li>
                    </ul>
                </div>
            </td>
            <td>
<?php echo _p($this->_aVars['aCategory']['name']); ?>
            </td>
            <td>
<?php echo $this->_aVars['aCategory']['link']; ?>
            </td>
            <td>
<?php echo $this->_aVars['aCategory']['direct_link']; ?>
            </td>
            <td class="text-center on_off">
                <div class="js_item_is_active"<?php if (! $this->_aVars['aCategory']['is_active']): ?> style="display:none;"<?php endif; ?>>
                    <a href="#?call=advancedfooter.updateMenuActivity&amp;id=<?php echo $this->_aVars['aCategory']['category_id']; ?>&amp;active=0" class="js_item_active_link" title="<?php echo _p('Deactivate'); ?>"></a>
                </div>
                <div class="js_item_is_not_active"<?php if ($this->_aVars['aCategory']['is_active']): ?> style="display:none;"<?php endif; ?>>
                    <a href="#?call=advancedfooter.updateMenuActivity&amp;id=<?php echo $this->_aVars['aCategory']['category_id']; ?>&amp;active=1" class="js_item_active_link" title="<?php echo _p('Activate'); ?>"></a>
                </div>
            </td>
        </tr>
<?php endforeach; endif; ?>
        </tbody>
    </table>
<?php else: ?>
        <div style="margin:15px;">
<?php echo _p('There are no menu, you may add them'); ?>
        </div>
<?php endif; ?>
</div>

