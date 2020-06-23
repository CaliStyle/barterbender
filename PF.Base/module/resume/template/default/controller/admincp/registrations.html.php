

{*if $iWhoViewedMMeGroupId && $iViewAllResumeGroupId*}

{literal}
<script type="text/javascript">
	function deleteAccount(account_id)
	{
		$.ajaxCall('resume.deleteAccount','account_id='+account_id);
	}
	function setApproveView(id, status) {
		$.ajaxCall('resume.setApproveView', 'id=' + id + '&status=' + status);
	}
	function setApproveWhoView(id, status) {
		$.ajaxCall('resume.setApproveWhoView', 'id=' + id + '&status=' + status);
	}
	function checkAllResume()
	{
		var checked = document.getElementById('resume_list_check_all').checked;
		$('.resume_view_checkbox').each(function(index,element){
			element.checked=checked;
			var sIdName = '#resume_view_' + element.value;
			
			if (element.checked == true) {
				$(sIdName).css({
					'backgroundColor' : '#FFFF88'
				});
			}
			else {
				if(element.value % 2 == 0){
					$(sIdName).css({
						'backgroundColor' : '#F0f0f0'
					});
				}
				else{
					$(sIdName).css({
						'backgroundColor' : '#F9F9F9'
					});
				}
			}
		});
		//console.log(checked);
		//checked = true;

		setDeleteSelectedButtonStatus(checked);

		return checked;
	}
	
	function setDeleteSelectedButtonStatus(status) {
	if (status) {
		$('.delete_selected').removeClass('disabled');
		$('.delete_selected').attr('disabled', false);
	}
	else {
		$('.delete_selected').addClass('disabled');
		$('.delete_selected').attr('disabled', true);
	}
}

	function checkDisableStatus_reg()
	{
		console.log("checkDisableStatus in registration ");
		var status = false;
		$('.resume_view_checkbox').each(function(index,element){
		var sIdName = '#resume_view_' + element.value;

		if (element.checked == true) {
			status = true;
			$(sIdName).css({
				'backgroundColor' : '#FFFF88'
			});
		}
		else {
			if(element.value % 2 == 0){
				$(sIdName).css({
					'backgroundColor' : '#F0f0f0'
				});
			}
			else{
				$(sIdName).css({
					'backgroundColor' : '#F9F9F9'
				});
			}
		}

	});


		setDeleteSelectedButtonStatus(status);
		return status;
	}
</script>
<style type="text/css">
	th{
		text-align: center !important;
	}
</style>
{/literal}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='admin_menu_manage_view_service_registration'}
        </div>
    </div>
    <!-- Search -->
    <form method="post" action="{url link='admincp.resume.registrations'}">
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {_p var='search_from'}:
                </label>
                <div class="form-inline dont-unbind-children">
                    <input class="form-control" name="search[fromdate]"
                           id="js_from_date_filter"
                           type="text"
                           value="{if isset($sFromDate) && $sFromDate}{$sFromDate}{/if}" />
                    <a href="#" id="js_from_date_filter_anchor">
                        <img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
                    </a>
                </div>
            </div>
            <div class="form-group">
                <label>
                    {_p var='to'}
                </label>
                <div class="form-inline dont-unbind-children">
                    <input name="search[todate]" id="js_to_date_filter" class="form-control"
                           type="text"
                           value="{if isset($sToDate) && $sToDate}{$sToDate}{/if}" />
                    <a href="#" id="js_to_date_filter_anchor">
                        <img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
                    </a>
                </div>
            </div>
            <div class="form-group">
                <label>
                    {_p var='type'}
                </label>
                <select name="search[type]" class="form-control">
                    <option value="3" selected>{_p var='all'}</option>
                    <option value="4" {if isset($sType) && $sType=="4"}selected{/if}>{_p var='who_viewed_me'}</option>
                    <option value="1" {if isset($sType) && $sType=="1"}selected{/if}>{_p var='view_resume'}</option>
                </select>
            </div>
        </div>
        <!-- Submit button -->
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{_p var='search'}"></span>
        </div>
    </form>
</div>
{if count($aResumes) > 0}
    <form action="{url link='current'}" method="post" id="karaoke_recording_list" >
    <div class="panel panel-default">
        <div class="table-responsive">
            <table align='center' class="table table-bordered">
                <thead>
                    <!-- Table rows header -->
                    <tr>
                        <th class="w20"><input type="checkbox" onclick="checkAllResume();" id="resume_list_check_all" name="resume_list_check_all"/></th>
                        <th>{_p var='id'}</th>
                        <th></th>
                        <th class="w100">{_p var='owner'}</th>


                        <th>{_p var='approve_view'}</th>
                        <th>{_p var='registered_date'}</th>


                        <th>{_p var='approve_who_view'}</th>
                        <th>{_p var='registered_date'}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Request rows -->
                    {foreach from=$aResumes key=iKey item=aResume}
                    <tr id="resume_view_{$aResume.account_id}"
                        class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <!-- Check Box -->
                        <td class="w20">
                            <input type = "checkbox" class="resume_view_checkbox"
                                   id="resume_{$aResume.account_id}" name="resume_row[]"
                                   value="{$aResume.account_id}" onclick="checkDisableStatus_reg();"/>
                        </td>

                        <td style="text-align: center">{$aResume.account_id}</td>
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="Options"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="javascript:void(0);" onclick="if(confirm( '{_p var='are_you_sure'}')) return deleteAccount('{$aResume.account_id}');">{phrase var='admincp.delete'}</a></li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <a href="{url link=''}{$aResume.user_name}/">{$aResume.full_name}</a>
                        </td>



                        <!-- Feature -->
                        <td style="text-align: center" class="type_1">

                            {if $aResume.view_resume == 1 || $aResume.view_resume == 2}
                                {if $aResume.user_group_id!=1}
                                    <div class="js_item_is_active yes_button"{if !$aResume.is_employer} style="display:none;"{/if}>
                                        <a  href="#?call=resume.setApproveView&amp;id={$aResume.account_id}&amp;status=no&amp;active=0" class="js_item_active_link" title="{_p var='resume_deactivate'}"></a>
                                    </div>
                                    <div class="js_item_is_not_active no_button"{if $aResume.is_employer} style="display:none;"{/if}>
                                        <a href="#?call=resume.setApproveView&amp;id={$aResume.account_id}&amp;status=yes&amp;active=1" class="js_item_active_link" title="{_p var='resume_activate'}"></a>
                                    </div>
                                {else}
                                    {_p var='enable'}
                                {/if}
                            {else}
                                {_p var='none'}
                            {/if}

                        </td>
                        <td style="text-align: center">

                            <?php if($this->_aVars['aResume']['start_employer_time']==0 || $this->_aVars['aResume']['start_employer_time']==null) echo _p('resume.n_a'); else echo Phpfox::getTime("m/d/Y", $this->_aVars['aResume']['start_employer_time']); ?></td>


                        <!-- Statistic -->
                            <td style="text-align: center" class="type_2">

                            {if $aResume.view_resume == 0 || $aResume.view_resume == 2}
                            {if $aResume.user_group_id!=1}
                                <div class="js_item_is_active yes_button"{if !$aResume.is_employee} style="display:none;"{/if}>
                                <a  href="#?call=resume.setApproveWhoView&amp;id={$aResume.account_id}&amp;status=no&amp;active=0" class="js_item_active_link" title="{phrase var='rss.deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active no_button"{if $aResume.is_employee} style="display:none;"{/if}>
                                <a href="#?call=resume.setApproveWhoView&amp;id={$aResume.account_id}&amp;status=yes&amp;active=1" class="js_item_active_link" title="{phrase var='rss.activate'}"></a>
                                </div>
                            {else}
                                {_p var='enable'}
                            {/if}
                            {else}
                                {_p var='none'}
                            {/if}

                        </td>
                        <td style="text-align: center"><?php if($this->_aVars['aResume']['start_time']==0 || $this->_aVars['aResume']['start_time']==null) echo _p('resume.n_a'); else echo Phpfox::getTime("m/d/Y", $this->_aVars['aResume']['start_time']); ?></td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            <div class="panel-footer">
                <input type="submit" name="delete_selected" id="delete_selected"  value="{_p var='delete'}" class="sJsConfirm delete_selected btn btn-danger disabled" disabled="disabled"/>
                <input type='hidden' name='task' value='do_delete_selected' />
            </div>
        </div>
        <!-- Delete selected button -->
    </div>
    </form>
{pager}
{else}
<div class="extra_info">{_p var='no_account_is_found'}</div>
{/if}

{*else*}
	{*phrase var='resume.please_chooose_default_group_for_who_s_view_me_service_and_view_all_resume_service_before_upgrading'*}
{*/if*}
