<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style type="text/css">
	input[name='quick_edit_input']{
		width: 90%;
		margin-bottom: 2px;
	}
</style>
{/literal}
<form method="get" action="{url link="admincp.contest"}">
    <div class="panel panel-default">
        <div class="panel-heading">
            {phrase var='contest.search_filter'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='contest.search_for_text'}:</label>
                {$aFilters.search}
            </div>
            <div class="form-group">
                <label for="">{phrase var='contest.search_for_user'}:</label>
                {$aFilters.user}
            </div>
            <div class="table form-group">
                <label for="">{phrase var='contest.status'}:</label>
                {$aFilters.status}
            </div>

            <div class="table form-group">
                <label for="">{phrase var='contest.featured'}:</label>
                {$aFilters.featured}
            </div>

            <div class="table form-group">
                <label for=""> {phrase var='contest.premium'}:</label>
                {$aFilters.premium}
            </div>

            <div class="table form-group">
                <label for=""> {phrase var='contest.ending_soon'}:</label>
                {$aFilters.ending_soon}
            </div>

            <div class="form-group">
                <label for=""> {phrase var='contest.active'}:</label>
                {$aFilters.active}
            </div>

            <div class="form-group">
                <label for=""> {phrase var='contest.display'}:</label>
                {$aFilters.display}
            </div>
            <div class="table form-group">
                <label for=""> {phrase var='contest.sort'}:</label>
                {$aFilters.sort}
                <br/>
                {$aFilters.sort_by}

            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='contest.submit'}" class="btn btn-primary" />
            <input type="submit" name="search[reset]" value="{phrase var='contest.reset'}" class="btn btn-default" />
        </div>
    </div>
</form>
{pager}

<form method="post" action="{url link='admincp.contest'}">
    <div class="panel panel-default">
        {if count($aContests)}
            <div class="table-responsive flex-sortable">
                <table colspan='1' class="table table-bordered">
                <tr>
                    <th class="w60"></th>
                    <th class="w120">{phrase var='contest.contest_name'}</th>
                    <th class="w80">{phrase var='contest.status'}</th>
                    <th class="w80">{phrase var='contest.featured'}</th>
                    <th class="w80">{phrase var='contest.premium'}</th>
                    <th class="w80">{phrase var='contest.ending_soon'}</th>
                    <th class="w80">{phrase var='contest.created_user'}</th>
                    <th class="w80">{phrase var='contest.end_date'}</th>
                    <th class="w80 t_center">{phrase var='contest.active'}</th>
                </tr>
                {foreach from=$aContests key=iKey item=aContest}
                    <tr id="js_row{$aContest.contest_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="{phrase var='contest.manage'}"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}">{phrase var='contest.view'}</a></li>

                                    {if $aContest.can_edit_contest}
                                        <li><a href="{url link="contest.add" id=$aContest.contest_id}">{phrase var='contest.edit'}</a></li>
                                    {/if}

                                    {if $aContest.can_approve_deny_contest}
                                        <li id="js_contest_approve__{$aContest.contest_id}">
                                            <a href="#" title="{phrase var='contest.approve_this_contest'}" onclick="$Core.jsConfirm({l}{r}, function(){l}$('#js_contest_approve__{$aContest.contest_id} a').attr('onclick', 'return false');$.ajaxCall('contest.approveContest', '&contest_id={$aContest.contest_id}', 'GET'){r}, function(){l}{r}); return false;">{phrase var='contest.approve'}</a>
                                        </li>

                                        <li id="js_contest_deny_{$aContest.contest_id}">
                                            <a href="#" title="{phrase var='contest.deny_this_contest'}" onclick="$Core.jsConfirm({l}{r}, function(){l}$('#js_contest_deny_{$aContest.contest_id} a').attr('onclick', 'return false');$.ajaxCall('contest.denyContest', '&contest_id={$aContest.contest_id}', 'GET'){r}, function(){l}{r}); return false;">{phrase var='contest.deny'}</a>
                                        </li>
                                    {/if}

                                    {if $aContest.can_close_contest}
                                            <li id="js_contest_close_{$aContest.contest_id}">
                                                <a href="#" title="{phrase var='contest.close_this_contest'}" onclick="$Core.jsConfirm({l}{r}, function(){l}$('#js_contest_close_{$aContest.contest_id} a').attr('onclick', 'return false');$.ajaxCall('contest.closeContest', '&contest_id={$aContest.contest_id}&amp;is_owner=1', 'GET'){r}, function(){l}{r}); return false;">{phrase var='contest.close'}</a>

                                            </li>
                                    {/if}

                                    {if $aContest.can_delete_contest}
                                            <li id="js_contest_close_{$aContest.contest_id}">
                                                <a href="#" title="{phrase var='contest.delete_this_contest'}" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('contest.deleteContest', '&contest_id={$aContest.contest_id}&amp;is_admincp=1', 'GET'){r}, function(){l}{r}); return false;">{phrase var='contest.delete'}</a>

                                            </li>
                                    {/if}

                                </ul>
                            </div>
                        </td>
                        <td id="js_contest_edit_title{$aContest.contest_id}"><a href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}" class="quickEdit" id="js_contest{$aContest.contest_id}">{$aContest.contest_name|convert|clean}</a></td>
                        <td>{$aContest.contest_status_text}</td>

                        {if $aContest.can_feature_contest}
                        <!-- Fetured-->
                            <td>
                                <div class="js_item_is_active"{if !$aContest.is_feature} style="display:none;"{/if}>
                                    <a href="#?call=contest.feature&amp;contest_id={$aContest.contest_id}&amp;type=0&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.un_feature'}"></a>
                                </div>

                                 <div class="js_item_is_not_active"{if $aContest.is_feature} style="display:none;"{/if}>
                                    <a href="#?call=contest.feature&amp;contest_id={$aContest.contest_id}&amp;type=1&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.feature'}"></a>
                                 </div>
                            </td>
                        {else}
                            <td>
                                   {if $aContest.is_feature}
                                        {img theme='misc/bullet_green.png' alt=''}
                                    {else}
                                       {img theme='misc/bullet_red.png' alt=''}
                                    {/if}
                            </td>
                        {/if}
                        <!-- Premium-->

                        {if $aContest.can_premium_contest}
                            <td>
                                <div class="js_item_is_active"{if !$aContest.is_premium} style="display:none;"{/if}>
                                    <a href="#?call=contest.premium&amp;contest_id={$aContest.contest_id}&amp;type=0&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.un_premium'}"></a>
                                </div>

                                 <div class="js_item_is_not_active"{if $aContest.is_premium} style="display:none;"{/if}>
                                    <a href="#?call=contest.premium&amp;contest_id={$aContest.contest_id}&amp;type=1&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.premium'}"></a>
                                 </div>
                            </td>
                        {else}
                            <td>	{if $aContest.is_premium}
                                        {img theme='misc/bullet_green.png' alt=''}
                                    {else}
                                       {img theme='misc/bullet_red.png' alt=''}
                                    {/if}
                            </td>
                        {/if}

                        <!-- Ending Soon-->
                        {if $aContest.can_ending_soon_contest}
                            <td>
                                <div class="js_item_is_active"{if !$aContest.is_ending_soon} style="display:none;"{/if}>
                                    <a href="#?call=contest.endingSoon&amp;contest_id={$aContest.contest_id}&amp;type=0&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.un_ending_soon'}"></a>
                                </div>

                                 <div class="js_item_is_not_active"{if $aContest.is_ending_soon} style="display:none;"{/if}>
                                    <a href="#?call=contest.endingSoon&amp;contest_id={$aContest.contest_id}&amp;type=1&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.ending_soon'}"></a>
                                 </div>
                            </td>
                        {else}
                            <td>
                                   {if $aContest.is_ending_soon}
                                        {img theme='misc/bullet_green.png' alt=''}
                                    {else}
                                       {img theme='misc/bullet_red.png' alt=''}
                                    {/if}
                            </td>
                        {/if}

                        <td>{$aContest|user}</td>
                        <td>{if $aContest.end_time } {$aContest.end_time|date:'contest.contest_short_date_time_format'}{/if}</td>

                        {if Phpfox::isAdmin()}
                        <td>
                            <div class="js_item_is_active"{if !$aContest.is_active} style="display:none;"{/if}>
                            <a href="#?call=contest.active&amp;contest_id={$aContest.contest_id}&amp;type=0&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.inactive'}"></a>
                            </div>

                            <div class="js_item_is_not_active"{if $aContest.is_active} style="display:none;"{/if}>
                            <a href="#?call=contest.active&amp;contest_id={$aContest.contest_id}&amp;type=1&amp;admin=true" class="js_item_active_link" title="{phrase var='contest.active'}"></a>
                            </div>
                        </td>
                        {else}
                        <td>
                            {if $aContest.is_active}
                            {img theme='misc/bullet_green.png' alt=''}
                            {else}
                            {img theme='misc/bullet_red.png' alt=''}
                            {/if}
                        </td>
                        {/if}

                    </tr>
                {/foreach}
                </table>
            </div>
        {else}
            <div class="p_4">
                {phrase var='contest.no_contests_found'}
            </div>
        {/if}
    </div>
</form>

<div class="extra_info" style="margin-right: 700px; width: 100px; font-weight:bold; position: absolute">
    {phrase var='contest.total'} {$iTotalResults} {phrase var='contest.results'}
</div>

{pager}