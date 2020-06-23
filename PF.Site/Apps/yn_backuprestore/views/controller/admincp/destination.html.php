<form method="get" action="">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('Find Destination')}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p('Destination Name')}</label>
                <input class="form-control" type="text" name="val[title]" value="{value type='input' id='title'}">
            </div>
            <div class="form-group">
                <label>{_p('Destination')}</label>
                <select name="val[type_id]" class="form-control">
                    <option value="">{_p('All')}</option>
                    {foreach from=$aTypes item=aType}
                        {if $aType.type_id != 1}
                        <option value="{$aType.type_id}" {if isset($iTypeId) && $iTypeId==$aType.type_id}selected{/if}>{$aType.title}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{_p('Search')}">
        </div>
    </div>
</form>
{if count($aDestinations)}
<form method="post" id="" action="" onsubmit="return $Core.BackupRestore.deleteSelected(this);">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='Manage Destinations'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="t_center w20"><input type="checkbox" onclick="$Core.BackupRestore.checkAllDestination();" id="destinations_check_all" name="destinations_check_all"/></th>
                    <th class="w20"></th>
                    <th>{_p('Destination Name')}</th>
                    <th>{_p('Destination Type')}</th>
                    <th>{_p('Location')}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aDestinations item=aDestination}
                <tr>
                    <td class="t_center w20">
                        <input type="checkbox" class="destination_row_checkbox" id="" name="destination_row[]" value="{$aDestination.destination_id}" onclick="$Core.BackupRestore.checkEnabled();"/>
                    </td>
                    <td class="t_center w20">
                        <a href="#" class="js_drop_down_link" title="Options"></a>
                        <div class="link_menu">
                            <ul>
                                <li>
                                    <a href="{permalink module='admincp.ynbackuprestore.add-destination' id=$aDestination.destination_id}">
                                        {_p('Edit')}
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="return $Core.BackupRestore.deleteDestination({$aDestination.destination_id})">
                                        {_p('Delete')}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>{$aDestination.title|shorten:50:'...'}</td>
                    <td>{$aDestination.destination_type}</td>
                    <td>{$aDestination.destination_location}</td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer t_right">
            <input type="submit" name="val[delete_selected]" id="delete_selected" disabled value="{_p('Delete Selected')}" class="sJsConfirm delete_selected btn btn-danger disabled"/>
        </div>
    </div>
    {pager}
</form>
<script type="text/javascript" src="{$sAssetsDir}js/ynbackuprestore.js"></script>
{else}
<div class="alert alert-info">
    {_p('No Destination Found.')}
</div>
{/if}