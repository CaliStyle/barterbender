<?php 
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 * 
 */
?>

<!-- Resume search form layout -->
<form method="get" action="{url link='admincp.resume.resumes'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='admin_menu_manage_resumes'}
            </div>
        </div>
        <div class="panel-body">
            <!-- Resume headline element -->
            <div class="form-group">
                <label>
                    {_p var='headline'}:
                </label>
                <input class="form-control" type="text" name="search[headline]" value="{value type='input' id='headline'}" id="headline" size="50" />
            </div>
            <!-- Resume full name element -->
            <div class="form-group">
                <label>
                    {_p var='owner'}:
                </label>
                <input class="form-control" type="text" name="search[full_name]" value="{value type='input' id='full_name'}" id="full_name" size="50" />
            </div>
            <!-- Resume status element -->
            <div class="form-group">
                <label>
                    {_p var='status'}:
                </label>
                <select name="search[status]" class="form-control">
                    <option value="all" {value type='select' id='status' default = all}>{_p var='all'}</option>
                    <option value="incomplete" {value type='select' id='status' default = incomplete}>{_p var='incomplete'}</option>
                    <option value="completed" {value type='select' id='status' default = completed}>{_p var='completed'}</option>
                    <option value="approving" {value type='select' id='status' default = approving}>{_p var='approving'}</option>
                    <option value="approved" {value type='select' id='status' default = approved}>{_p var='published'}</option>
                    <option value="denied" {value type='select' id='status' default = denied}>{_p var='denied'}</option>
                    <option value="private" {value type='select' id='status' default = private}>{_p var='private'}</option>
                </select>
            </div>
        </div>
        <!-- Submit button -->
        <div class="panel-footer">
            <input type="submit" id="filter_submit" name="search[submit]" value="{_p var='search'}" class="btn btn-primary" />
            <input type="button" id="filter_submit" name="search[reset]" value="{_p var='reset'}" class="btn btn-default" onclick="window.location.href='{url link='admincp.resume.resumes'}'" />
        </div>
    </div>
</form>
<!-- Resume Management Space -->
{if count($aResumes) > 0}
    <form action="{url link='current'}" method="post" id="resume_list" >
        <div class="panel panel-default">
            <div class="table-responsive">
            <table align='center' class="table table-admin">
                <thead>
                    <!-- Table rows header -->
                    <tr>
                        <th><input type="checkbox" onclick="checkAllResume();" id="resume_list_check_all" name="resume_list_check_all"/></th>
                        <th class=""></th>
                        <th>{_p var='headline'}</th>
                        <th>{_p var='owner'}</th>
                        <th class="">{_p var='complete'}</th>
                        <th class="">{_p var='status'}</th>
                        <th class="">{_p var='backend_favorites'}</th>
                        <th class="">{_p var='backend_views'}</th>
                        <th class="">{_p var='created'}</th>
                    </tr>
                </thead>
                <tbody>
                <!-- Resume Rows -->
                    {foreach from=$aResumes key=iKey item=aResume }
                        <tr id="resume_{$aResume.resume_id}" class="resume_row {if $iKey%2 == 0 } resume_row_even_background{else} resume_row_odd_background{/if}">
                            <!-- Check Box -->
                            <td style="width:20px">
                                <input type = "checkbox" class="resume_row_checkbox" id="resume_{$aResume.resume_id}" name="resume_row[]" value="{$aResume.resume_id}" onclick="checkDisableStatus();"/>
                            </td>
                            <!-- Options -->
                            <td class="t_center">
                                <a href="#" class="js_drop_down_link" title="Options"></a>
                                <div class="link_menu">
                                    <ul>
                                        <li><a href="{url link='resume.add' id=$aResume.resume_id}">{phrase var='admincp.edit'}</a></li>
                                        <li><a href="javascript:void(0);" onclick="return deleteResume('{$aResume.resume_id}');">{phrase var='admincp.delete'}</a></li>
                                    </ul>
                                </div>
                            </td>
                            <!-- Resume headline -->
                            <td>
                                <a href="{permalink module='resume.view' id = $aResume.resume_id title = $aResume.headline}">
                                    {$aResume.headline|shorten:35:'...'}
                                </a>
                            </td>
                            <!-- Resume owner -->
                            <td>
                                {$aResume|user}
                            </td>
                            <!-- Resume Complete -->
                            <td class="table_row_column">
                                {if $aResume.is_completed }
                                    {_p var='completed'}
                                {else}
                                    {_p var='incomplete'}
                                {/if}
                            </td>
                            <!-- Status -->
                            <td class="table_row_column">
                                {if $aResume.status == 'approving'}
                                    <div id="approve_select_resume_{$aResume.resume_id}">
                                        <a  href="javascript:void(0);" onclick="return approveResume('{$aResume.resume_id}');">{_p var='approve'}</a>
                                        |
                                        <a  href="javascript:void(0);" onclick="return denyResume('{$aResume.resume_id}');">{_p var='deny'}</a>
                                    </div>
                                    <div id ="approved_resume_{$aResume.resume_id}" style="display:none">
                                        {phrase var ="resume.published"}
                                    </div>
                                    <div id = "denied_resume_{$aResume.resume_id}" style="display:none">
                                        {phrase var ="resume.denied"}
                                    </div>
                                    <div id = "private_resume_{$aResume.resume_id}" style="display:none">
                                        {phrase var="resume.private"}
                                    </div>
                                {elseif $aResume.status == 'approved'}
                                    {if $aResume.is_published }
                                        {phrase var="resume.published"}
                                    {else}
                                        {phrase var="resume.private"}
                                    {/if}
                                {elseif $aResume.status == 'none'}
                                    --
                                {else}
                                    {phrase var="resume.".$aResume.status}
                                {/if}
                            </td>
                            <!-- Views -->
                            <td class="table_row_column">
                                {$aResume.total_favorite}
                            </td>
                            <!-- Views -->
                            <td class="table_row_column">
                                {$aResume.total_view}
                            </td>
                            <!-- Created -->
                            <td class="table_row_column">
                                <?php echo date('d F, Y',$this->_aVars["aResume"]["time_stamp"]); ?>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            </div>
            {pager}
            <!-- Delete selected button -->
            <div class="panel-footer">
                <input type="submit" name="delete_selected" id="delete_selected" disabled value="{_p var='delete_selected'}" class="sJsConfirm delete_selected btn btn-danger disabled" />
                <input type='hidden' name='task' value='do_delete_selected' />
            </div>
        </div>
    </form>
{else}
    <div class="extra_info">
        {_p var='no_resumes_found'}
    </div>
{/if}
