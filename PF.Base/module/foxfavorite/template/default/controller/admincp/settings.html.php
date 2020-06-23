<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='foxfavorite.settings'}
        </div>
    </div>
    <div class="panel-body">
        <form>
            <div class="table-responsive flex-sortable">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{phrase var='foxfavorite.module'}</th>
                            <th class="w200">{phrase var='foxfavorite.active'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aSettings key=iKey name=setting item=aItem}
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td>{$aItem.title|translate:'module_id'}</td>
                            <td>
                            {if !in_array($aItem.module_id, $aFunctionedModule)}
                                <div class="js_item_is_active"{if !$aItem.is_active} style="display:none;"{/if}>
                                <a href="#?call=foxfavorite.updateModuleActivity&amp;id={$aItem.module_id}&amp;active=0" class="js_item_active_link" title="{phrase var='admincp.deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aItem.is_active} style="display:none;"{/if}>
                                    <a href="#?call=foxfavorite.updateModuleActivity&amp;id={$aItem.module_id}&amp;active=1" class="js_item_active_link" title="{phrase var='admincp.activate'}"></a>
                                </div>
                            {else}
                                <div class="js_item_is_active" title="{phrase var='foxfavorite.this_module_is_always_active'}">
                                    {phrase var='foxfavorite.this_module_is_always_active'}
                                </div>
                            {/if}
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>