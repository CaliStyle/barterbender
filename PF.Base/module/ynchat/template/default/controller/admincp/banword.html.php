<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link=$aBanFilter.url}">
    <div class="panel panel-default">
        <div class="panel-heading">
            {phrase var='ynchat.add_filter'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='ynchat.word'}:</label>
                <input type="text" name="find_value" value="" class="form-control" />
            </div>
            {if isset($aBanFilter.replace)}
            <div class="form-group">
                <label for="">{phrase var='ynchat.replacement'}:</label>
                <input type="text" name="replacement" value="" class="form-control"/>
            </div>
            {/if}
        </div>

        {*{module name='ban.form'}*}
        <div class="panel-footer">
            <input type="submit" value="{phrase var='ynchat.add'}" name="add_ban" class="button btn-primary" />
        </div>
    </div>
</form>

{if count($aFilters)}
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        {phrase var='ynchat.ban_filters'}
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="w60"></th>
                <th>{phrase var='ynchat.word'}</th>
                {if isset($aBanFilter.replace)}
                <th>{phrase var='ynchat.replacement'}</th>
                {/if}
                <th>{phrase var='ynchat.added_by'}</th>
                <th>{phrase var='ynchat.added_on'}</th>
                {*<th> Affects </th>*}
            </tr>
            </thead>
            <tbody>
            {foreach from=$aFilters name=filters item=aFilter}
                <tr{if !is_int($phpfox.iteration.filters/2)} class="tr"{/if}>
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="{phrase var='ynchat.manage'}"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{url link=$aBanFilter.url delete={$aFilter.ban_id}" onclick="return confirm('{phrase var='ynchat.are_you_sure'}');">{phrase var='ynchat.delete'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>{$aFilter.find_value|htmlspecialchars}</td>
                    {if isset($aBanFilter.replace)}
                        <td>{$aFilter.replacement|htmlspecialchars}</td>
                    {/if}
                    <td>{if empty($aFilter.user_id)}{phrase var='ynchat.n_a'}{else}{$aFilter|user}{/if}</td>
                    <td>{$aFilter.time_stamp|date}</td>
                    {*<td>{$aFilter.s_user_groups_affected}</td>*}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}
