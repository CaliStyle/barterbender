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
    ul.packages li {
        list-style: disc inside none;
    }
    div#breadcrumb_content_holder
	{
		min-height: 400px;
	}	
</style>
{/literal}
<form method="GET" action="{url link='admincp.jobposting'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{phrase var='employer'}:</label>
                {$aFilters.search}
            </div>
            <div class="form-group">
                <label>{phrase var='representative'}:</label>
                {$aFilters.user}
            </div>
            <div class="form-group">
                <label>{phrase var='sponsor'}:</label>
                {$aFilters.status}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='submit'}" class="btn btn-primary">
        </div>
    </div>
</form>

{if count($aCompanies)}
<p class="help-block">
    {phrase var='total_upper'} {$iTotalResults} {phrase var='result_s'}
</p class="help-block">
<form method="post" action="{url link='admincp.jobposting'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='manage_companies'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="t_center">{phrase var='company_name'}</th>
                    <th class="t_center w200">{phrase var='representative'}</th>
                    <th class="w120">{phrase var='pay_to_sponsor'}</th>
                    <th class="t_center w100">{phrase var='sponsor'}</th>
                    <th class="t_center w100">{phrase var='activate'}</th>
                    <th class="t_center w200">{phrase var='valid_packages'}</th>
                    <th class="t_center w100">{phrase var='action'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aCompanies key=iKey item=aCompany}
                    <tr id="js_row{$aCompany.company_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td id="js_job_edit_title{$aCompany.company_id}">
                            <a href="{permalink module='jobposting.company' id=$aCompany.company_id title=$aCompany.name}" class="quickEdit" id="js_job{$aCompany.company_id}">
                                {$aCompany.name|convert|clean}
                            </a>
                        </td>
                        <td>{$aCompany|user}</td>
                        <td class="t_center">
                            {if $aCompany.paid_to_sponsor}{phrase var='paid'}{/if}
                        </td>
                        <td class="t_center" id="item_update_sponsor_{$aCompany.company_id}">
                            {if !empty($aCompany.is_approved)}
                                <div class="js_item_is_active" style="{if !$aCompany.is_sponsor}display:none;{/if}">
                                    <a href="#?call=jobposting.updateSponsor&amp;company_id={$aCompany.company_id}&amp;active=0" class="js_item_active_link" title="{phrase var='sponsor'}"></a>
                                </div>
                                <div class="js_item_is_not_active" style="{if $aCompany.is_sponsor}display:none;{/if}">
                                    <a href="#?call=jobposting.updateSponsor&amp;company_id={$aCompany.company_id}&amp;active=1" class="js_item_active_link" title="{phrase var='unsponsor'}"></a>
                                </div>
                            {else}
                                {phrase var='n_a'}
                            {/if}
                        </td>
                        <td class="t_center" id="item_update_sponsor_{$aCompany.company_id}">
                            {if !empty($aCompany.is_approved)}
                                <div class="js_item_is_not_active" style="{if $aCompany.is_activated}display:none;{/if}">
                                    <a href="#?call=jobposting.activatedCompany&amp;id={$aCompany.company_id}&amp;active=0" class="js_item_active_link" title="{phrase var='sponsor'}"></a>
                                </div>
                                <div class="js_item_is_active" style="{if !$aCompany.is_activated}display:none;{/if}">
                                    <a href="#?call=jobposting.deactivatedCompany&amp;id={$aCompany.company_id}&amp;active=1" class="js_item_active_link" title="{phrase var='unsponsor'}"></a>
                                </div>
                            {else}
                                {phrase var='n_a'}
                            {/if}
                        </td>

                        <td>
                            {if count($aCompany.packages)}
                            <ul class="packages">
                                {foreach from=$aCompany.packages item=aPackage}
                                <li>{$aPackage.name}</li>
                                {/foreach}
                            </ul>
                            {/if}
                        </td>
                        <td class="t_center">
                            <a href="{url link='jobposting.company.add.jobs' id=$aCompany.company_id}" class="quickEdit" id="js_job{$aCompany.company_id}">{phrase var='view_jobs'}</a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</form>
{else}
<div class="alert alert-info">
    {phrase var='no_companies_found'}
</div>
{/if}
{pager}