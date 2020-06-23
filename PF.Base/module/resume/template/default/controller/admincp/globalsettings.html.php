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
 
 <form method="post" action="{url link='admincp.resume.globalsettings'}" id="js_form">
     <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var="resume.admin_menu_global_settings"}
            </div>
        </div>
        <!-- Group Setup for Who 's Viewed Me Service -->
        <div class="panel-body">
            <div class="from-group">
                <label>{_p var='Note'}:</label>
                {foreach from=$aCustomGroups item=aGroupParent}
                    {if $aGroupParent.view_all_resume}
                        <p> {_p var='was_set_permission_to_view_resume' group=$aGroupParent.title|convert}</p>
                    {/if}
                {/foreach}
            </div>
            <div class="from-group">
                <label>{_p var="resume.configure_the_group_for_using_who_s_viewed_me_service"}</label>
            </div>
            <table class="table table-admin">
            {foreach from=$aCustomGroups item=aGroupParent}
                <tr>
                    <td>
                        {_p var='group_is_transferred' title=$aGroupParent.title|convert}
                    </td>
                    <td>
                        <select class="form-control" name="val[whoview][{$aGroupParent.user_group_id}]">
                            <option value="">{_p var='select'}</option>
                            {foreach from=$aCustomGroups item=aGroup}
                                {assign var="check" value="1"}
                                {foreach from=$aWhoViewedMeGroup item=WhoViewedMeGroup}
                                    {if $WhoViewedMeGroup.begin_group == $aGroupParent.user_group_id && $WhoViewedMeGroup.end_group == $aGroup.user_group_id}
                                        {assign var="check" value="2"}
                                    {/if}
                                {/foreach}
                                <option value="{$aGroup.user_group_id}" {if $check == 2}selected{/if}>{_p var=$aGroup.title}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            {/foreach}
            </table>
            <!-- Group Setup for View Resume Service -->
            <div class="form-group">
                <label>{_p var="resume.configure_the_group_for_using_view_all_resume_service"}</label>
            </div>

            <table class="table table-admin" >
            {foreach from=$aCustomGroups item=aGroupParent}
                <tr>
                    <td>
                        {_p var='group_is_transferred' title = $aGroupParent.title|convert}
                    </td>
                    <td>
                        <select class="form-control" name="val[viewme][{$aGroupParent.user_group_id}]">
                            <option value="">{_p var='select'}</option>
                            {foreach from=$aCustomGroups item=aGroup}
                                {assign var="check" value="1"}
                                {foreach from=$aViewAllResumeGroup item=ViewAllResumeGroup}
                                    {if $ViewAllResumeGroup.begin_group == $aGroupParent.user_group_id && $ViewAllResumeGroup.end_group == $aGroup.user_group_id}
                                        {assign var="check" value="2"}
                                    {/if}
                                {/foreach}
                                <option value="{$aGroup.user_group_id}" {if $check == 2}selected{/if}>{_p var=$aGroup.title}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            {/foreach}
            </table>

            <table class="table" >
                <tr>
                    <td style="padding-left: 0;">
                        <label for="">{_p var='public_all_resumes_for_all_members_of_this_site'}</label>
                    </td>
                    <td style="padding-left: 0;">
                        <select class="form-control" name="val[public_resume]">
                            <option {if $aPublic==1}selected{/if} value="1">{_p var='everyone'}</option>
                            <option {if $aPublic==2}selected{/if} value="2">{_p var='registered_members'}</option>
                            <option {if $aPublic==3}selected{/if} value="3">{_p var='specific_user_groups'}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 0; ">
                        <label for="">{_p var='configure_to_get_basic_information_from_profile'}</label>
                    </td>
                    <td style="padding-left: 0;">
                        <select class="form-control" name="val[get_basic_information]">
                            <option value="1" {if $aPers.get_basic_information}selected="selected"{/if}>{_p var='true'}</option>
                            <option value="0" {if !$aPers.get_basic_information}selected="selected"{/if}>{_p var='false'}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 0;">
                        <label for="">{_p var='configure_position_to_put_resume_in_basic_info_block_of_profile_page'}</label>
                    </td>
                    <td style="padding-left: 0;">
                        <select class="form-control" name="val[display_resume_in_profile_info]">
                            <option value="1" {if $aPers.display_resume_in_profile_info}selected="selected"{/if}>{_p var='true'}</option>
                            <option value="0" {if !$aPers.display_resume_in_profile_info}selected="selected"{/if}>{_p var='false'}</option>
                        </select>
                    </td>
                </tr>
            </table>
            <div class="form-group">
                <label><input type="radio" {if $aPers.position == 1} checked="checked" {/if} value="1" name="val[position]">&nbsp;{_p var='the_beginning_of_basic_information_block'}</label>
            </div>
            <div class="form-group">
                <label><input type="radio" {if $aPers.position == 2} checked="checked" {/if} value="2" name="val[position]">&nbsp;{_p var='the_end_of_basic_information_block'}</label>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="panel-footer">
            <input type="submit" value="{phrase var='admincp.update'}" class="btn btn-primary" />
        </div>
     </div>
 </form>
