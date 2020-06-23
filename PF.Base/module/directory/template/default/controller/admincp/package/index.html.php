<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<span id="yndirectory_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='manage_packages'}
        </div>
    </div>
    {if !count($aPackages)}
    <div class="alert alert-info">
        {_p var='no_packages_found'}
    </div>
    {else}
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="t_center w40"></th>
                    <th class="t_center">{phrase var='package_name'}</th>
                    <th class="t_center w220">{phrase var='valid_period'}</th>
                    <th class="t_center w220">{phrase var='package_fee'}</th>
                    <th class="t_center w60">{phrase var='action'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aPackages key=iKey item=aPackage}
            <tr class="{if $iKey%2 == 0 }tr{/if}">
                <td class="t_center w40">
                    <a href="#" class="js_drop_down_link" title="{_p var='Options'}"></a>
                    <div class="link_menu">
                        <ul>
                            <li><a href="{url link='admincp.directory.package.add'}id_{$aPackage.package_id}/">{phrase var='edit'}</a></li>
                            <li><a href="javascript:void(0);" onclick="package_index.confirmdelete({$aPackage.package_id}); return false;" onclick="$.ajaxCall('directory.deletepackage','id={$aPackage.package_id}')">{phrase var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>
                    {$aPackage.name}
                </td>
                <td class="t_center w220">
                    {if $aPackage.expire_type != 0}
                        {$aPackage.expire_number|number_format}
                    {/if}&nbsp;{if $aPackage.expire_type == 1}
                        {phrase var='day_s'}
                    {elseif $aPackage.expire_type == 2}
                        {phrase var='week_s'}
                    {elseif $aPackage.expire_type == 3}
                        {phrase var='month_s'}
                    {else}
                        {phrase var='never_expired'}
                    {/if}
                </td>

                <td class="t_right w220">
                    {$aPackage.fee}
                </td>

                <td class="t_center w60">
                    <div class="js_item_is_active" style="{if !$aPackage.active}display:none;{/if}">
                        <a href="#?call=directory.activepackage&amp;id={$aPackage.package_id}&amp;active=0"
                           class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active" style="{if $aPackage.active}display:none;{/if}">
                        <a href="#?call=directory.activepackage&amp;id={$aPackage.package_id}&amp;active=1" class="js_item_active_link"
                           title="{phrase var='activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <?php if ($this->getLayout('pager')): ?>
    <div class="panel-footer">
        {pager}
    </div>
    <?php endif; ?>
    {/if}
</div>