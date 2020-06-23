<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='manage_aj_packages'}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
		    <thead>
                <tr>
                    <th class="t_center w80"></th>
                    <th class="t_center">{phrase var='package_name'}</th>
                    <th class="t_center w200">{phrase var='apply_job_number_admincp'}</th>
                    <th class="t_center w200">{phrase var='valid_period'}</th>
                    <th class="t_center w200">{phrase var='package_fee'}</th>
                    <th class="t_center w80">{phrase var='action'}</th>
                </tr>
            </thead>
		    <tbody>
            {foreach from=$aPackages key=iKey item=aPackage}
                <tr id="jobposting_{$aPackage.package_id}" class="jobposting_row {if $iKey%2 == 0 } jobposting_row_even_background{else} jobposting_row_odd_background{/if}">
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="Options"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{url link='admincp.jobposting.applyjobpackage.add'}id_{$aPackage.package_id}/">{phrase var='edit'}</a></li>
                                <li><a href="javascript:void(0);" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('jobposting.deleteapplyjobpackage','id={$aPackage.package_id}'){r});">{phrase var='delete'}</a></li>
                            </ul>
                        </div>
                    </td>

                    <td>
                        <a href="#">
                            {$aPackage.name}
                        </a>
                    </td>

                    <td class="t_center">
                        {$aPackage.apply_number}
                    </td>

                    <td class="t_center">
                        {if $aPackage.expire_type!=0}{$aPackage.expire_number}{/if} {if $aPackage.expire_type==1}{phrase var='day_s'}{elseif $aPackage.expire_type==2}{phrase var='week_s'}{elseif $aPackage.expire_type==3}{phrase var='month_s'}{else}{phrase var='never_expired'}{/if}
                    </td>

                    <td class="t_right">
                        {if $aPackage.fee == 0}
                            {_p var='free'}
                        {else}
                            {$aPackage.fee|currency}
                        {/if}
                    </td>

                    <td class="on_off w80">
                        <div class="js_item_is_active" style="{if !$aPackage.active}display:none;{/if}">
                            <a href="#?call=jobposting.activepapplyjobackage&amp;id={$aPackage.package_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aPackage.active}display:none;{/if}">
                            <a href="#?call=jobposting.activepapplyjobackage&amp;id={$aPackage.package_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
	    </table>
    </div>
</div>
{pager}
