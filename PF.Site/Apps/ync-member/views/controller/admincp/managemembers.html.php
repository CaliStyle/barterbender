<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsSearch}
    <script type="text/javascript" src="{$corePath}/assets/jscript/manage.js"></script>
{/if}
<form class="ynmember_member_search_form" method="post" onsubmit="return ynmember.getSearchData(this);">
    <div class="panel panel-default">
        <div class="panel panel-heading">
            <div class="panel-title">
                {_p('Search Filter')}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="title">{_p('Username')}:</label>
                <input class="form-control" type="text" name="search[full_name]" value="{value type='input' id='full_name'}" id="title" size="50">
            </div>
            <div class="form-group">
                <label for="email">{_p('Email')}:</label>
                <input class="form-control" type="text" name="search[email]" value="{value type='input' id='email'}" id="email" size="50">
            </div>
            <div class="form-group">
                <label for="user_group_id">{_p('User Group')}:</label>
                <select name="search[user_group_id]" id="user_group_id" class="form-control">
                    <option value="0">{_p('All')}</option>
                    {foreach from=$aUserGroups name=userGroups item=aUserGroup}
                        <option value="{$aUserGroup.user_group_id}"{value type='select' id='user_group_id' default=$aUserGroup.user_group_id}>
                            {$aUserGroup.title}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="user_group_id">{_p('Featured')}:</label>
                <select name="search[is_featured]" class="form-control" id="is_featured">
                    <option value="0">{_p('All')}</option>
                    <option value="featured" {value type='select' id='is_featured' default='featured'}>{_p('Featured')}</option>
                    <option value="not_featured"  {value type='select' id='is_featured' default='not_featured'}>{_p('Not Featured')}</option>
                </select>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="panel-footer">
            <button type="submit" id="ynmember_filter_member_submit" name="search[submit]" class="btn btn-primary">{_p('Search')}</button>
        </div>
    </div>
</form>

<span id="ynab_loading" style="display: none;">{img theme='ajax/add.gif'}</span>

{if count($aList) >0}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var="Members"}
        </div>
    </div>
    <div class="table-responsive hello-world">
        <table class="table table-bordered">
            <!-- Table rows header -->
            <thead>
                <tr>
                    <th class="t_center">{_p('ID')}</th>
                    <th class="t_center">{_p('Name')}</th>
                    <th class="t_center">{_p('Email')}</th>
                    <th class="t_center">{_p('Rating')}</th>
                    <th class="t_center">{_p('Reviews')}</th>
                    <th class="t_center">{_p('Featured')}</th>
                    <th class="t_center">{_p('Member of Day')}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aList key=iKey item=aUser}
                <tr id="ynmember_member_row_{$aUser.user_id}">
                    <td class="t_center">
                        {$aUser.user_id}
                    </td>
                    <td>
                        {$aUser|user}
                    </td>
                    <td>
                        <a href="mailto:{$aUser.email}">{$aUser.email}</a>
                    </td>

                    <td align="center">
                        {$aUser.rating|ynmember_round:2}
                    </td>
                    <td align="center">
                        {$aUser.total_review}
                    </td>
                    <!--  Feature-->
                    <td id="ynmember_member_update_featured_{$aUser.user_id}" align="center">
                        <div class="{if $aUser.is_featured}js_item_is_active{else}js_item_is_not_active{/if}">
                            <a class="js_item_active_link" href="javascript:void(0)"
                               onclick="ynmember.updateFeatured(this);" data-user_id="{$aUser.user_id}" data-is_featured="{$aUser.is_featured}">
                            </a>
                        </div>
                    </td>

                    <td id="ynmember_member_update_mod_{$aUser.user_id}" align="center">
                        <div class="{if $aUser.is_mod}js_item_is_active{else}js_item_is_not_active{/if}">
                            <a class="js_item_active_link" href="javascript:void(0)"
                               onclick="ynmember.updateMod(this);" data-user_id="{$aUser.user_id}" data-is_mod="{$aUser.is_mod}">
                            </a>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        {template file="ynmember.block.pager"}
    </div>
</div>
{else}
<div class="alert alert-info">
    {_p('No Members Found.')}
</div>
{/if}