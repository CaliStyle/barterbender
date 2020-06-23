<!-- Filter Search Form Layout -->
<div class="panel panel-default">
    <form class="ynfr" method="get" action="{url link='admincp.fevent.manageevents'}">
        <!-- Form Header -->
        <div class="panel-heading">
            <div class="panel-title">
                {_p var="search_filter"}
            </div>
        </div>
        <div class="panel-body">
            <!-- Event Name-->
            <div class="form-group">
                <label for="event">
                       {_p var="event"}:
                </label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50" />
            </div>
            <div class="form-group">
                <label for="event_owner">
                   {_p var='event_owner'}:
                </label>
                <input class="form-control" type="text" name="search[owner]" value="{value type='input' id='owner'}" id="owner" size="50" />
            </div>

            <div class="form-group">
                <label for="category">
                   {_p var='category'}:
                </label>
                <select name="search[category_id]" class="form-control">
                    <option value="0">{_p var='any'}</option>
                    {foreach from = $aCategories item = aCategoriesItem}
                        <option value="{$aCategoriesItem.category_id}"
                            {if isset($aForms) && $aForms.category_id == $aCategoriesItem.category_id}
                                selected="selected"
                            {/if}
                        >
                            {$aCategoriesItem.name}
                        </option>
                        {*
                        {foreach from = $aCategoriesItem.sub item = subItem}
                            <option value="{$subItem.category_id}"  >&nbsp;&nbsp;&nbsp;{$subItem.name}</option>
                        {/foreach}
                        *}
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="status">
                   {_p var='status'}:
                </label>
                <select name="search[status]" class="form-control">
                    <option value="0">{_p var='any'}</option>
                    <option value="approved"  {value type='select' id='status' default = 'approved'}>{_p var='approved'}</option>
                    <option value="pending"  {value type='select' id='status' default = 'pending'}>{_p var='pending'}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="featured">
                    {_p var="featured"}:
                </label>
                <select name="search[feature]" class="form-control">
                    <option value="0">{_p var='any'}</option>
                    <option value="featured"  {value type='select' id='feature' default = 'featured'}>{_p var='featured'}</option>
                    <option value="not_featured"  {value type='select' id='feature' default = 'not_featured'}>{_p var='not_featured'}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sponsor">
                    {_p var="sponsor"}:
                </label>
                <select name="search[sponsor]" class="form-control">
                    <option value="0">{_p var='any'}</option>
                    <option value="sponsor"  {value type='select' id='sponsor' default = 'sponsor'}>{_p var='sponsor'}</option>
                    <option value="not_sponsor"  {value type='select' id='sponsor' default = 'not_sponsor'}>{_p var='not_sponsor'}</option>
                </select>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="panel-footer">
            <input type="submit" id="filter_submit" name="search[submit]" value="{_p var='search'}" class="btn btn-primary" />
        </div>
    </form>
</div>
<!-- Transaction Listing Space -->
<span id="ynfevent_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
{if count($aList) >0}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var="event_s"}
        </div>
    </div>
    <form action="{url link='current'}" method="post" id="event_list">
        <div class="table-responsive ">
            <table class='table table-bordered' style="margin-bottom: 0px;">
            <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="w20"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                        <th class="w20"></th>
                        <th class="w180">{_p var='event'}</th>
                        <th class="" align="center">{_p var='category'}</th>
                        <th class="" align="center">{_p var='owner'}</th>
                        <th class="" align="center">{_p var='status'}</th>
                        {if Phpfox::getUserParam('fevent.can_feature_events')}
                        <th class="" align="center">{_p var='featured'}</th>
                        {/if}
                        {if Phpfox::getUserParam('fevent.can_sponsor_fevent')}
                        <th class="" align="center">{_p var='sponsor'}</th>
                        {/if}
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aList key=iKey item=aItem}
                        <tr id="coupon_{$aItem.event_id}" class="coupon_row {if $iKey%2 == 0 } coupon_row_even_background{else} coupon_row_odd_background{/if}">
                            <!-- Options -->
                            <td><input type="checkbox" name="id[]" class="checkbox" value="{$aItem.event_id}" id="js_id_row{$aItem.event_id}" /></td>
                            <td class="t_center">
                                <a href="#" class="js_drop_down_link" title="Options"></a>
                                <div class="link_menu">
                                    <ul>
                                        {if $aItem.can_approve_event == true && $aItem.view_id == 1}
                                            <li><a href="javascript:void(0)" onclick="manageevent.approveEvent({$aItem.event_id}); return false;">{_p var='approve'}</a></li>
                                        {/if}

                                        {if $aItem.can_delete_event == true}
                                            <li><a href="javascript:void(0)" onclick="manageevent.confirmdeleteEvent({$aItem.event_id}); return false;">{_p var='delete'}</a></li>
                                        {/if}
                                    </ul>
                                </div>
                            </td>
                            <td class="w180">
                                <a href="{permalink module='fevent' id=$aItem.event_id title=$aItem.title}">
                                    {$aItem.title|shorten:50:'...'}
                                </a>
                            </td>
                            <td>
                                {if $aItem.category_title}
                                    {$aItem.category_title|convert|clean|shorten:25:'...'}
                                {/if}
                            </td>
                            <td>
                                {$aItem|user}
                            </td>
                            <td>
                                {if $aItem.view_id == 0}
                                    {_p var='approved'}
                                {else}
                                    {_p var='pending'}
                                {/if}
                            </td>
                            {if $aItem.can_feature_event}
                            <td id ="item_update_featured_{$aItem.event_id}" class="w80 on_off">
                                <div class="js_item_is_active"{if !$aItem.is_featured} style="display:none;"{/if}>
                                    <a href="#?call=fevent.updateFeaturedBackEnd&amp;event_id={$aItem.event_id}&amp;iIsFeatured=0&amp;active=0" class="js_item_active_link" title="{_p var='unfeature'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aItem.is_featured} style="display:none;"{/if}>
                                    <a href="#?call=fevent.updateFeaturedBackEnd&amp;event_id={$aItem.event_id}&amp;iIsFeatured=1&amp;active=1" class="js_item_active_link" title="{_p var='feature'}"></a>
                                </div>
                            </td>
                            {/if}
                            {if $aItem.can_sponsor_event}
                            <td id ="item_update_sponsor_{$aItem.event_id}" class="w80 on_off">
                                <div class="js_item_is_active"{if !$aItem.is_sponsor} style="display:none;"{/if}>
                                    <a href="#?call=fevent.updateSponsorBackEnd&amp;event_id={$aItem.event_id}&amp;iSponsor=0&amp;active=0" class="js_item_active_link" title="{_p var='unsponsor'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aItem.is_sponsor} style="display:none;"{/if}>
                                    <a href="#?call=fevent.updateSponsorBackEnd&amp;event_id={$aItem.event_id}&amp;iSponsor=1&amp;active=1" class="js_item_active_link" title="{_p var='sponsor'}"></a>
                                </div>
                            </td>
                            {/if}
                        </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        {pager}
        <div class="panel-footer">
            <div class="table_bottom">
                {if Phpfox::getUserParam('fevent.can_feature_events')}
                <input type="submit" id="feature_selected" name="val[feature]" value="{_p('Feature Selected')}" class="sJsConfirm feature btn btn-success sJsCheckBoxButton disabled" disabled onclick=""/>
                <input type="submit" id="unfeature_selected" name="val[un_feature]" value="{_p('Un-Feature Selected')}" class="sJsConfirm un-feature btn btn-success sJsCheckBoxButton disabled" disabled onclick=""/>
                {/if}
                {if Phpfox::getUserParam('fevent.can_sponsor_fevent')}
                <input type="submit" id="sponsor_selected" name="val[sponsor]" value="{_p('Sponsor Selected')}" class="sJsConfirm sponsor btn btn-success sJsCheckBoxButton disabled" disabled onclick=""/>
                <input type="submit" id="unsponsor_selected" name="val[un_sponsor]" value="{_p('Un-Sponsor Selected')}" class="sJsConfirm un-sponsor btn btn-success sJsCheckBoxButton disabled" disabled onclick=""/>
                {/if}
                <input type="submit" id="delete_selected" name="val[delete]" value="{_p('Delete Selected')}" class="sJsConfirm delete btn btn-danger sJsCheckBoxButton disabled" disabled onclick=""/>
            </div>
        </div>
    </form>
</div>
{else}
    <div class="alert alert-danger">
        {_p var='no_events_found'}
    </div>
{/if}

