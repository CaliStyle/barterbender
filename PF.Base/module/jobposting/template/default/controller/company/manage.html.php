<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright        [YOUNET_COPPYRIGHT]
 * @author           AnNT
 * @package          Module_jobposting
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<script src="{$core_path}module/jobposting/static/jscript/company/application_paging.js" type="text/javascript"></script>
{if $page > 1}
    <div id="page2" style="display:none;" class="table-responsive">
        <table>
        {foreach from=$aApplications name=application item=aApplication}
            <tr>
                <td>{$aApplication.name}</td>
                <td class="t_center">{$aApplication.time_stamp_text}</td>
                <td class="t_center">{phrase var=''+$aApplication.status_name}</td>
                <td>
                    {if !empty($aApplication.resume)}
                    <a class="no_ajax_link" href="{$urlModule}jobposting/static/php/download.php?id={$aApplication.application_id}">{phrase var='download'}</a>
                    |{/if} <a href="#" onclick="ynjobposting.application.view({$aApplication.application_id}, '{phrase var='view_application'}'); return false;">{phrase var='view'}</a>
                    | <a href="#" onclick="ynjobposting.application.confirm_delete({$aApplication.application_id}, '{phrase var='core.are_you_sure'}'); return false;">{phrase var='delete'}</a>
                    {if $aApplication.status_name=='pending' || $aApplication.status_name=='passed'}
                    | <a href="#" onclick="ynjobposting.application.reject({$aApplication.application_id}); return false;">{phrase var='reject'}</a>
                    {/if}
                    {if $aApplication.status_name=='pending' || $aApplication.status_name=='rejected'}
                    | <a href="#" onclick="ynjobposting.application.pass({$aApplication.application_id}); return false;">{phrase var='pass'}</a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </table>
    </div>
    <script type="text/javascript">
        appendPageApplication();
    </script>
    <div style="display:none;">
        {pager}
    </div>
{else}
    <div class="table">
        <div class="table_left">
            {phrase var='job_title'}: <a href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}">{$aJob.title}</a>
        </div>
    </div>
    <div class="table-responsive">
        <table id="tableApplication" class="table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th align="left">{phrase var='candidate'}</th>
                    <th class="t_center">{phrase var='submitted_date'}</th>
                    <th class="t_center">{phrase var='status'}</th>
                    <th align="left">{phrase var='option'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aApplications name=application item=aApplication}
                <tr{if is_int($phpfox.iteration.application/2)} class="on"{/if} id="js_ja_{$aApplication.application_id}">
                    <td>{$aApplication.name}</td>
                    <td class="t_center">{$aApplication.time_stamp_text}</td>
                    <td class="t_center">{phrase var=''+$aApplication.status_name}</td>
                    <td>
                        {if !empty($aApplication.resume)}
                        <a class="no_ajax_link" href="{$urlModule}jobposting/static/php/download.php?id={$aApplication.application_id}">{phrase var='download'}</a>
                        |{/if} <a href="#" onclick="ynjobposting.application.view({$aApplication.application_id}, '{phrase var='view_application'}'); return false;">{phrase var='view'}</a>
                        | <a href="#" onclick="ynjobposting.application.confirm_delete({$aApplication.application_id}, '{phrase var='core.are_you_sure'}'); return false;">{phrase var='delete'}</a>
                        {if $aApplication.status_name=='pending' || $aApplication.status_name=='passed'}
                        | <a href="#" onclick="ynjobposting.application.reject({$aApplication.application_id}); return false;">{phrase var='reject'}</a>
                        {/if}
                        {if $aApplication.status_name=='pending' || $aApplication.status_name=='rejected'}
                        | <a href="#" onclick="ynjobposting.application.pass({$aApplication.application_id}); return false;">{phrase var='pass'}</a>
                        {/if}
                    </td>
                </tr>
            {foreachelse}
                <tr>
                    <td colspan="6">
                        <div class="extra_info">{phrase var='no_application_found'}.</div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {pager}
    {if count($aApplications)}
        <div class="table_clear" style="margin-top: 10px;">
            <ul class="table_clear_button">
                <li><input type="button" id="download_zip" style="display:none" class="button" value="{phrase var='download_all_resumes'}" onclick="window.location.href='{$urlModule}jobposting/static/php/downloadzip.php?id={$aJob.job_id}'" /></li>
            </ul>
            <div class="clear"></div>
        </div>
    {/if}
{/if}
{literal}
<script type="text/javascript">
    $Behavior.onLoadDownloadAll = function(){
        {/literal}
            {foreach from=$aApplications name=application item=aApplication}
                {if !empty($aApplication.resume)}
                    $("#download_zip").removeAttr("style");
                {/if}
            {/foreach}
        {literal}
    }
</script>
{/literal}