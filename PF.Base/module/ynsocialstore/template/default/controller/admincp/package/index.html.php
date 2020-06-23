<?php

?>
<span id="ynsocialstore_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='ynsocialstore.manage_packages'}
        </div>
    </div>

    {if count($aPackages)}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="t_center w40"></th>
                    <th>{_p var='ynsocialstore.package_name'}</th>
                    <th class="t_center w120">{_p var='ynsocialstore.valid_period'}</th>
                    <th class="t_center w120">{_p var='ynsocialstore.package_fee'}</th>
                    <th class="t_center">{_p var='ynsocialstore.action'}</th>
                </tr>
            </thead>

            <tbody>
            {foreach from=$aPackages key=iKey item=aPackage}
                <tr id="ynsocialstore_{$aPackage.package_id}" class="ynsocialstore_row {if $iKey%2 == 0 } ynsocialstore_row_even_background{else} ynsocialstore_row_odd_background{/if}">
                    <td class="t_center w40">
                        <a href="#" class="js_drop_down_link" title="Options"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{url link='admincp.ynsocialstore.package.add'}id_{$aPackage.package_id}/">{_p var='ynsocialstore.edit'}</a></li>
                                {if !$aPackage.used }
                                <li><a href="javascript:void(0);" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('ynsocialstore.deletepackage', 'id={$aPackage.package_id}&reload=1');{r}, function(){l}{r}); return false;">{_p var='ynsocialstore.delete'}</a></li>
                                {/if}
                            </ul>
                        </div>
                    </td>

                    <td>
                        <a href="javascript:void(0);">
                            {$aPackage.name}
                        </a>
                    </td>

                    <td class="table_row_column">
                        {if $aPackage.expire_number == 0}
                            {_p var='ynsocialstore.never_expired'}
                        {else}
                            {$aPackage.expire_number} {_p var='ynsocialstore.day_s'}
                        {/if}
                    </td>

                    <td class="table_row_column">
                        {if $aPackage.fee == 0}
                            {_p var='Free'}
                        {else}
                            {$aPackage.fee|currency}
                        {/if}
                    </td>

                    <td class="on_off w80">
                        <div class="js_item_is_active"{if !$aPackage.active} style="display:none;"{/if}>
                            <a href="#?call=ynsocialstore.activepackage&amp;id={$aPackage.package_id}&amp;active=0" class="js_item_active_link" title="{_p var='ynsocialstore.show'}"></a>
                        </div>
                        <div class="js_item_is_not_active"{if $aPackage.active} style="display:none;"{/if}>
                            <a href="#?call=ynsocialstore.activepackage&amp;id={$aPackage.package_id}&amp;active=1" class="js_item_active_link" title="{_p var='ynsocialstore.hide'}"></a>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {pager}
    </div>
    <?php if ($this->getLayout('pager')): ?>
        <div class="panel-footer">
            {pager}
        </div>
    <?php endif; ?>
    {else}
    <div class="alert alert-info">
        {_p var='no_packages_found'}.
    </div>
    {/if}
</div>
