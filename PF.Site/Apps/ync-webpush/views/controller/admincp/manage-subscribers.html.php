<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="get" class="form-search" action="{url link='admincp.yncwebpush.manage-subscribers'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="clearfix row">
                <div class="form-group col-sm-3">
                    <label >{_p var='search'}</label>
                    {filter key='keyword' placeholder='search'}
                </div>
                <div class="form-group col-sm-3">
                    <label>{_p var='within'}</label>
                    {filter key='type'}
                </div>
                <div class="form-group col-sm-3">
                    <label >{_p var='group'}</label>
                    {filter key='group'}
                </div>
                <div class="form-group col-sm-3">
                    <label >{_p var='gender'}</label>
                    {filter key='gender'}
                </div>
                <div id="js_admincp_search_options" class="hide">
                    <div class="form-group col-sm-3">
                        <label >{_p var='location'}</label>
                        {filter key='country'}
                        {module name='core.country-child' admin_search=1 country_child_filter=true country_child_type='browse'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='city'}</label>
                        {filter key='city'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='zip_postal_code'}</label>
                        {filter key='zip'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='ip_address'}</label>
                        {filter key='ip'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='age_group'}</label>
                        {filter key='from'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label>&nbsp;</label>
                        {filter key='to'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='show_members'}</label>
                        {filter key='status'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='sort_results_by'}</label>
                        {filter key='sort'}
                    </div>
                    {assign var='sFormGroupClass' value='col-sm-3'}
                    {foreach from=$aCustomFields item=aCustomField}
                    {template file='custom.block.foreachcustom'}
                    {/foreach}
                </div>
            </div>
            <div class="form-btn-group">
                <button type="submit" class="btn btn-primary" name="search[submit]">{_p var='search'}</button>
                <a class="btn btn-info" href="{url link='admincp.yncwebpush.manage-subscribers'}">{_p var='reset'}</a>
                <button type="button" class="btn btn-link" rel="{_p var='view_less_search_options'}" onclick="$('#js_admincp_search_options').toggleClass('hide'); var text = $(this).text(); $(this).text($(this).attr('rel')); $(this).attr('rel', text)">
                    {_p var='view_more_search_options'}
                </button>
            </div>
        </div>
    </div>
</form>

<form method="post" action="{url link='admincp.yncwebpush.manage-subscribers'}" class="ajax_post" data-include-button="true" data-callback-start="process_admincp_browse">
    <div class="panel panel-default ync-manage-subscribers-holder">
        {if $aUsers}
        <div class="table-responsive">
            <table class="table table-admin" {if isset($bShowFeatured) && $bShowFeatured == 1} id="js_drag_drop"{/if}>
                <thead>
                    <tr>
                        <th class="w20 js_checkbox">
                            <input type="checkbox" name="val[ids]" value="" id="js_check_box_all" class="main_checkbox js_ync_checkbox_all_subscribers" />
                        </th>
                        <th {table_sort class="w60 centered" asc="u.user_id asc" desc="u.user_id desc" query="search[sort]"}>
                            {_p var='id'}
                        </th>
                        <th class="w80">{_p var='photo'}</th>
                        <th {table_sort class="centered" asc="u.full_name asc" desc="u.full_name desc" query="search[sort]"}>
                            {_p var='display_name'}
                        </th>
                        <th>
                            {_p var='group'}
                        </th>
                        <th style="width: 240px">
                            {_p var='browsers_u'}
                        </th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aUsers name=users key=iKey item=aUser}
                    <tr>
                        <td class="t_center js_checkbox">
                            {if count($aUser.browsers)}
                            <input type="checkbox" name="ids[]" class="checkbox js_ync_checkbox_subscribers" value="{$aUser.user_id}" id="js_id_row{$aUser.user_id}" />
                            {/if}
                        </td>
                        <td>#{$aUser.user_id}</td>
                        <td>{img user=$aUser suffix='_50_square' max_width=50 max_height=50}</td>
                        <td><a href="{$aUser.profile_url}" target="_blank">{$aUser.full_name}</a></td>
                        <td>
                            {if ($aUser.status_id == 1)}
                                <div class="js_verify_email_{$aUser.user_id}">{_p var='pending_email_verification'}</div>
                            {/if}
                            {if Phpfox::getParam('user.approve_users') && $aUser.view_id == '1'}
                                <span id="js_user_pending_group_{$aUser.user_id}">{_p var='pending_approval'}</span>
                            {elseif $aUser.view_id == '2'}
                                {_p var='not_approved'}
                            {else}
                                {$aUser.user_group_title|convert}
                            {/if}
                        </td>
                        <td>
                            {if count($aUser.browsers)}
                                {foreach from=$aUser.browsers item=aBrowser}
                                    <span class="ync-browser-icon">
                                        <img src="{param var='core.path_actual'}PF.Site/Apps/ync-webpush/assets/images/{$aBrowser.browser}.png" alt="" width="50px">
                                        <span class="ync-total-token">{$aBrowser.total_count}</span>
                                    </span>
                                {/foreach}
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <a class="btn btn-primary disabled sJsCheckBoxButton popup" id="js_send_selected_link" data-href="{url link='admincp.yncwebpush.send-push-notification'}" data-ids="" data-template="{if !empty($iTemplateId)}{$iTemplateId}{else}0{/if}">{_p var='send_selected_u'}</a>
            <a class="btn btn-primary popup" href="{url link='admincp.yncwebpush.send-push-notification' send_to='all'}{if !empty($iTemplateId)}&template={$iTemplateId}{/if}">{_p var='send_all_u'}</a>
        </div>
        {else}
            <div class="alert alert-empty">
                {_p var='no_subscribers_found'}
            </div>
        {/if}
    </div>
    {if $aUsers}
        {pager}
    {/if}
</form>
{literal}
<script type="text/javascript">
    $Behavior.onLoadManageSubscribers = function(){
        if (!$('input[name="ids[]"]').length) {
            $('#js_check_box_all').remove();
            $('.js_checkbox').remove();
        }
    }
</script>
{/literal}