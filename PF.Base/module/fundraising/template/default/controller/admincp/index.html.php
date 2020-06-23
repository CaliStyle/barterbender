<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<style>
    ul.pagination li a.active {
        color: #FFFFFF;
        background-color: #1ab394;
    }
</style>
{/literal}
<form method="get" action="{url link='admincp.fundraising'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='search_filter'}
            </div>
        </div>
        <div class="panel-body row">
            <div class="form-group col-md-6">
                <label>{_p var='search_for_text'}:</label>
                {$aFilters.search}
            </div>
            <div class="form-group col-md-6">
                <label>{_p var='search_for_user'}:</label>
                {$aFilters.user}
            </div>
            <div class="form-group col-md-6">
                <label>{_p var='status'}:</label>
                {$aFilters.status}
            </div>
            <div class="form-group col-md-6">
                <label>{_p var='featured'}:</label>
                {$aFilters.featured}
            </div>
            <div class="form-group col-md-6">
                <label>{_p var='approved'}:</label>
                {$aFilters.approved}
            </div>
            <div class="form-group col-md-6">
                <label>{_p var='s_f_u_active'}:</label>
                {$aFilters.active}
            </div>
            <div class="form-group col-md-6">
                <label>{_p var='display'}:</label>
                {$aFilters.display}
            </div>
            <div class="form-group col-md-6">
                <label>{_p var='sort'}:</label>
                <div class="row">
                    <div class="col-md-6">{$aFilters.sort}</div>
                    <div class="col-md-6">{$aFilters.sort_by}</div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" name="search[submit]" class="btn btn-primary">{_p var='submit'}</button>
            <a href="{url link='admincp.fundraising'}" class="btn btn-danger">{_p var='reset'}</a>
        </div>
    </div>
</form>

<div class="help-block">
    <b>{_p var='total_upper'} {$iTotalResults} {_p var='result_s'}</b>
</div>

<!-- CAMPAIGNS LISTING -->
{if count($aCampaigns)}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='manage_fundraisings'}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="w40 t_center"></th>
                    <th class="t_center">{_p var='campaign_name'}</th>
                    <th class="w100 t_center">{_p var='status'}</th>
                    <th class="t_center">{_p var='featured'}</th>
                    <th class="t_center">{_p var='highlight'}</th>
                    <th class="t_center">{_p var='s_u_active'}</th>
                    <th class="w100 t_center">{_p var='created_user'}</th>
                    <th class="w100 t_center">{_p var='expired_date'}</th>
                    <th class="w140 t_center">{_p var='fundraising_goal'}</th>
                    <th class="t_center">{_p var='raised_upper'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aCampaigns key=iKey item=aCampaign}
            <tr>
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                    <div class="link_menu">
                        <ul>
                            {if $aCampaign.can_edit_campaign}
                            <li>
                                <a href="{url link='fundraising.add' id=$aCampaign.campaign_id}">{_p var='edit'}</a>
                            </li>
                            {/if}
                            {if $aCampaign.is_approved == '0'}
                            <li>
                                <a class="sJsConfirm" href="{url link='admincp.fundraising' approve=$aCampaign.campaign_id}">{_p var='approve'}</a></li>
                            {/if}
                            <li>
                                <a href="{permalink module='fundraising.list' id=$aCampaign.campaign_id }">{_p var='view_statistics'}</a>
                            </li>
                            {if $aCampaign.can_delete_campaign}
                            <li>
                                <a class="sJsConfirm" href="{url link='admincp.fundraising' delete=$aCampaign.campaign_id}">{_p var='delete'}</a>
                            </li>
                            {/if}
                        </ul>
                    </div>
                </td>
                <td>
                    <a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}">
                        {$aCampaign.title|convert|clean}
                    </a>
                </td>
                <td>{$aCampaign.campaign_status_text}</td>
                {if $aCampaign.is_approved == 1 && $aCampaign.status == $aCampaignStatus.ongoing}
                <td>
                    <div class="js_item_is_active" style="{if !$aCampaign.is_featured}display:none;{/if}">
                        <a href="#?call=fundraising.feature&amp;campaign_id={$aCampaign.campaign_id}&amp;active=0&amp;admin=true"
                           class="js_item_active_link" title="{_p var='un_feature'}"></a>
                    </div>
                    <div class="js_item_is_not_active" style="{if $aCampaign.is_featured}display:none;{/if}">
                        <a href="#?call=fundraising.feature&amp;campaign_id={$aCampaign.campaign_id}&amp;active=1&amp;admin=true"
                           class="js_item_active_link" title="{_p var='feature'}"></a>
                    </div>
                </td>
                <td>
                    {if $aCampaign.status == $aCampaignStatus.ongoing && $aCampaign.module_id == 'fundraising'}
                    <div class="js_item_is_active js_item_directsign_active"
                         style="{if !$aCampaign.is_highlighted}display:none;{/if}">
                        <a href="#?call=fundraising.highlight&amp;campaign_id={$aCampaign.campaign_id}&amp;active=0&amp;admin=true"
                           class="js_item_active_link js_item_directsign_link js_remove_default"
                           title="{_p var='un_highlight_this_campaign'}"></a>
                    </div>
                    <div class="js_item_is_not_active js_item_directsign_not_active"
                         style="{if $aCampaign.is_highlighted}display:none;{/if}">
                        <a href="#?call=fundraising.highlight&amp;campaign_id={$aCampaign.campaign_id}&amp;active=1&amp;admin=true"
                           class="js_item_active_link js_item_directsign_link js_remove_default"
                           title="{_p var='highlight_this_campaign'}"></a>
                    </div>
                    {else}
                        N/A
                    {/if}
                </td>
                {else}
                <td>
                    N/A
                </td>
                <td>
                    N/A
                </td>
                {/if}
                <!-- Active status -->
                <td>
                    {if Phpfox::getUserParam('fundraising.can_active_campaign')}
                    <div class="js_item_is_active" style="{if !$aCampaign.is_active}display:none;{/if}">
                        <a href="#?call=fundraising.setActive&amp;campaign_id={$aCampaign.campaign_id}&amp;active=0&amp;admin=true"
                           class="js_item_active_link" title="{_p var='s_inactive'}"></a>
                    </div>
                    <div class="js_item_is_not_active" style="{if $aCampaign.is_active}display:none;{/if}">
                        <a href="#?call=fundraising.setActive&amp;campaign_id={$aCampaign.campaign_id}&amp;active=1&amp;admin=true"
                           class="js_item_active_link" title="{_p var='s_active'}"></a>
                    </div>
                    {else}
                    <div class="js_item_is_active" style="{if !$aCampaign.is_active}display:none;{/if}">
                        <a href="" onclick="return false;" class="js_item_active_link"
                           title="{_p var='s_inactive'}"></a>
                    </div>
                    <div class="js_item_is_not_active" style="{if $aCampaign.is_active}display:none;{/if}">
                        <a href="" onclick="return false;" class="js_item_active_link" title="{_p var='s_active'}"></a>
                    </div>
                    {/if}
                </td>
                <td>{$aCampaign|user}</td>
                <td>{if $aCampaign.end_time }
                        {$aCampaign.end_time|date:'core.global_update_time'}
                    {else}
                        {_p var='unlimited_upper'}
                    {/if}
                </td>
                <td class="t_right">{$aCampaign.financial_goal_text}</td>
                <td class="t_right">{$aCampaign.total_amount_text}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{pager}
{else}
<div class="alert alert-info">
    {_p var='no_campaigns_found'}
</div>
{/if}