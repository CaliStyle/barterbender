<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<td><a href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}">{$aJob.title}</a></td>
<td class="t_center">{$aJob.posted_text}</td>
<td class="t_center">{$aJob.expire_text}</td>
<td class="t_center"><a href="#" onclick="$.ajaxCall('jobposting.changeJobHide', 'id={$aJob.job_id}'); return false;">{if $aJob.is_hide==1}{phrase var='hide'}{else}{phrase var='show'}{/if}</a></td>
    <td class="t_center">{$aJob.status_jobs}</td>
<td class="t_center">
    {if !isset($aJob.is_expired) || $aJob.is_expired == 0}
        {if ($aCompany.user_id == Phpfox::getUserId() || $aJob.canEdit) }
        <a href="{permalink module='jobposting.add' id=$aJob.job_id}">{phrase var='edit'}</a> |
        {/if}
    {/if}

    {if ($aCompany.user_id == Phpfox::getUserId() || $aJob.canDelete) }
    <a href="#" onclick="if(confirm('{phrase var='core.are_you_sure'}')) $.ajaxCall('jobposting.deleteJob', 'id={$aJob.job_id}'); return false;">{phrase var='delete'}</a> |
    {/if}

    {if $aJob.post_status!=1}
    <a href="javascript:void(0);" onclick="$Core.box('jobposting.popupPublishJob', '500', 'id={$aJob.job_id}&company_id={$aJob.company_id}'); return false;">{phrase var='publish'}</a>
    {else}
        {if $aJob.total_application > 0}
            {if ($aCompany.user_id == Phpfox::getUserId() || $aJob.canViewApplication) }
            <a href="{url link='jobposting.company.manage' job=$aJob.job_id}">{phrase var='view_applications'} ({$aJob.total_application})</a> |
            {/if}
            {if ($aCompany.user_id == Phpfox::getUserId() || $aJob.canDownloadResumes) }
            <a class="no_ajax_link" href="{$urlModule}jobposting/static/php/downloadzip.php?id={$aJob.job_id}">{phrase var='download_all_resumes'}</a>
            {/if}
        {else}
            {phrase var='view_applications'} (0) | {phrase var='download_all_resumes'}
        {/if}
    {/if}
</td>
