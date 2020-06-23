<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 4, 2020, 9:28 pm */ ?>
<?php



?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
<?php echo _p('categories'); ?>
        </div>
    </div>
    <div class="table-responsive flex-sortable">
        <table id="js_drag_drop" class="table table-bordered">
            <thead>
            <tr>
                <th class="w40"></th>
                <th class="w40"></th>
                <th><?php echo _p('name'); ?></th>
                <th class="t_center w60"><?php echo _p('active'); ?></th>
            </tr>
            </thead>
            <tbody>
<?php if (count((array)$this->_aVars['aCategories'])):  foreach ((array) $this->_aVars['aCategories'] as $this->_aVars['iKey'] => $this->_aVars['aCategory']): ?>
                <tr class="checkRow<?php if (is_int ( $this->_aVars['iKey'] / 2 )): ?> tr<?php else:  endif; ?>">
                    <td class="drag_handle"><input type="hidden" name="val[ordering][<?php echo $this->_aVars['aCategory']['category_id']; ?>]" value="<?php echo $this->_aVars['aCategory']['ordering']; ?>" /></td>
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="<?php echo _p('Manage'); ?>"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.ecommerce.category.add', array('id' => $this->_aVars['aCategory']['category_id'])); ?>"><?php echo _p('edit'); ?></a></li>
<?php if (isset ( $this->_aVars['aCategory']['categories'] ) && ( $this->_aVars['iTotalSub'] = count ( $this->_aVars['aCategory']['categories'] ) )): ?>
                                <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.ecommerce.category', array('sub' => $this->_aVars['aCategory']['category_id'])); ?>"><?php echo _p('manage_sub_categories_total', array('total' => $this->_aVars['iTotalSub'])); ?></a></li>
<?php endif; ?>

<?php if (! empty ( $this->_aVars['aCategory']['numberItems'] )): ?>
                                <li><a href="" class="jsWarning" data-title="<?php echo _p('notice'); ?>" data-message="<?php echo _p('you_can_not_delete_this_category_because_there_are_many_items_related_to_it'); ?>"><?php echo _p('delete'); ?></a></li>
<?php else: ?>
                                <li><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.ecommerce.category', array('delete' => $this->_aVars['aCategory']['category_id'])); ?>" class="sJsConfirm" data-message="<?php echo _p('are_you_sure'); ?>"><?php echo _p('delete'); ?></a></li>
<?php endif; ?>
                            </ul>
                        </div>
                    </td>
                    <td>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aCategory']['server_id'],'path' => 'core.url_pic','file' => $this->_aVars['aCategory']['image_path'],'suffix' => '_16')); ?>
                        &nbsp;
<?php if (Phpfox ::isPhrase($this->_aVars['aCategory']['title'])): ?>
<?php echo _p($this->_aVars['aCategory']['title']); ?>
<?php else: ?>
<?php echo Phpfox::getLib('locale')->convert($this->_aVars['aCategory']['title']); ?>
<?php endif; ?>
                    </td>
                    <td class="t_center">
                        <div class="js_item_is_active" style="<?php if (! $this->_aVars['aCategory']['is_active']): ?>display:none;<?php endif; ?>">
                        <a href="#?call=ecommerce.updateActivity&amp;id=<?php echo $this->_aVars['aCategory']['category_id']; ?>&amp;active=0&amp;sub=<?php if ($this->_aVars['bSubCategory']): ?>1<?php else: ?>0<?php endif; ?>" class="js_item_active_link" title="<?php echo _p('deactivate'); ?>"></a>
                        </div>
                        <div class="js_item_is_not_active" style="<?php if ($this->_aVars['aCategory']['is_active']): ?>display:none;<?php endif; ?>">
                        <a href="#?call=ecommerce.updateActivity&amp;id=<?php echo $this->_aVars['aCategory']['category_id']; ?>&amp;active=1&amp;sub=<?php if ($this->_aVars['bSubCategory']): ?>1<?php else: ?>0<?php endif; ?>" class="js_item_active_link" title="<?php echo _p('activate'); ?>"></a>
                        </div>
                    </td>
                </tr>
<?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo '
<script type="text/javascript">
    $Behavior.onInitCategory = function(){
        $(\'.jsWarning\').click(function() {
            var buttons = {};
            buttons[oTranslations[\'cancel\']] = {
                \'class\': \'button dont-unbind\',
                text: oTranslations[\'cancel\'],
                click: function() {
                    $(this).dialog("close");
                }
            };
            $(document.createElement(\'div\'))
                .attr({title: $(this).data(\'title\'), class: \'confirm\'})
                .html($(this).data(\'message\'))
                .dialog({
                    dialogClass: \'pf_js_confirm\',
                    close: function() {
                        $(this).remove();
                    },
                    buttons: buttons,
                    draggable: true,
                    modal: true,
                    resizable: false,
                    width: \'auto\'
                });
            return false;
        });
    }

</script>
'; ?>

