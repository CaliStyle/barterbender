<?php 
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
?>

<!-- Level Management Space -->
{if count($aLevelList) > 0 }
<form action="{url link='admincp.resume.levels'}" method="post" id="admincp_resume_level_list" >
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='levels'}
            </div>
        </div>
        <div class="table-responsive">
            <table id="js_drag_drop" align='center' class="table table-bordered">
                <thead>
                    <tr>
                        <th class="w40">{phrase var='contact.order'}</th>
                        <th><input type="checkbox" onclick="checkAllLevel();" id="resume_level_list_check_all" name="resume_level_list_check_all"/></th>
                        <th>{_p var='level_title'}</th>
                        <th class="t_center w120">{_p var='used'}</th>
                        <th class="t_center w80">{_p var='options'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$aLevelList key=iKey item=oLevel}
                        <tr id="resume_level_{$oLevel.level_id}" class="checkRow resume_level_row {if $iKey%2 == 0 } resume_row_even_background {else} resume_row_odd_background {/if}">
                            <td class="drag_handle"><input type="hidden" name="val[ordering][{$oLevel.level_id}]" value="{$oLevel.ordering}" /></td>

                            <td style="width:20px">
                                <input type = "checkbox" class="resume_level_row_checkbox" id="level_{$oLevel.level_id}" name="level_row[]" value="{$oLevel.level_id}" onclick="checkDisableStatus();"/>
                            </td>
                            <td id ='js_resume_level_edit_title{$oLevel.level_id}'>
                                <a href="#?type=input&amp;id=js_resume_level_edit_title{$oLevel.level_id}&amp;content=js_resume_level{$oLevel.level_id}&amp;call=resume.updateLevelTitle&amp;level_id={$oLevel.level_id}" class="quickEdit" id="js_resume_level{$oLevel.level_id}">
                                    {$oLevel.name|convert|clean}</a>
                            </td>
                            <td class="t_center w120">
                                {$oLevel.used}
                            </td>
                            <td class="t_center w120">
                                 {if $oLevel.used == 0}
                                    <a  href="javascript:void(0);" onclick="return deleteResumeLevel('{$oLevel.level_id}');">{_p var='delete'}</a>
                                 {else}
                                    <span style="color:gray">{_p var='delete'} </span>
                                 {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <input type="submit" name="delete_selected" id="delete_selected" disabled value="{_p var='delete_selected'}" class="sJsConfirm delete_selected btn btn-danger disabled" />
            <input type='hidden' name='task' value='do_delete_selected' />
        </div>
    </div>
</form>
{pager}
{else}
	<div class="extra_info">{_p var='no_levels_had_been_added'}</div>
{/if}