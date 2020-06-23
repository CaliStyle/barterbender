<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="GET" action="{url link="admincp.jobposting.job"}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{phrase var='job'}:</label>
                {$aFilters.search}
            </div>
            <div class="form-group">
                <label>{phrase var='company'}:</label>
                {$aFilters.searchcompany}
            </div>
            <div class="form-group">
                <label>{phrase var='industry'}:</label>
                {$aIndustryBlock}
            </div>
            <div class="form-group">
                <label>{phrase var='feature'}:</label>
                {$aFilters.feature}
            </div>
            <div class="form-group">
                <label>{phrase var='status'}:</label>
                {$aFilters.status}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='submit'}" class="btn btn-primary">
        </div>
    </div>
</form>
{if count($aJobs)}
<p class="help-block">
    {phrase var='total_upper'} {$iTotalResults} {phrase var='result_s'}
</p>
<form method="post" action="{url link='admincp.jobposting.job'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='manage_jobs'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="t_center">{phrase var='employer'}</th>
                        <th class="t_center">{phrase var='job'}</th>
                        <th class="t_center w120">{phrase var='pay_to_feature'}</th>
                        <th class="t_center w100">{phrase var='feature'}</th>
                        <th class="t_center w180">{phrase var='industry'}</th>
                        <th class="t_center w180">{phrase var='number_of_application'}</th>
                        <th class="t_center w80">{phrase var='status'}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aJobs key=iKey item=aJob}
                    <tr id="js_row{$aJob.job_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td><a href="{permalink module='jobposting.company' id=$aJob.company_id title=$aJob.name}"}>{$aJob.name}</a></td>
                        <td id="js_job_edit_title{$aJob.job_id}"><a href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}" class="quickEdit" id="js_job{$aJob.job_id}">{$aJob.title|convert|clean}</a></td>

                        <td class="t_center w120">
                               {if $aJob.is_paid==1}{phrase var='paid'}{else}{phrase var='n_a'}{/if}
                        </td>

                        <td class="t_center">
                            {if $aJob.post_status==1}
                                <div class="js_item_is_active" style="{if !$aJob.is_featured}display:none;{/if}">
                                    <a href="#?call=jobposting.updateFeatured&amp;job_id={$aJob.job_id}&amp;active=0" class="js_item_active_link" title="{phrase var='feature'}"></a>
                                </div>
                                <div class="js_item_is_not_active" style="{if $aJob.is_featured}display:none;{/if}">
                                    <a href="#?call=jobposting.updateFeatured&amp;job_id={$aJob.job_id}&amp;active=1" class="js_item_active_link" title="{phrase var='unfeature'}"></a>
                                </div>
                            {else}
                                {phrase var='n_a'}
                            {/if}
                        </td>


                        <td class="t_center w180">{$aJob.industrial_phrase}</td>
                        <td class="t_center w180">
                            {$aJob.total_application}
                        </td>
                        <td class="t_center w80">
                            {$aJob.status_jobs}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</form>
{pager}

{else}
<div class="alert alert-info">
    {phrase var='no_jobs_found'}
</div>
{/if}
