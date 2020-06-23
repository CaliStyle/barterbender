<link rel="stylesheet" href="{$sAssetsDir}datetimepicker/jquery.datetimepicker.css" type="text/css">


<form action="">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('Find Schedule')}
            </div>
        </div>
        <div class="panel-body row dont-unbind-children">
            <div class="form-group col-md-6">
                <label>{_p('Name')}</label>
                <input class="form-control" type="text" name="val[title]" value="{value type='input' id='title'}">
            </div>
            <div class="form-group col-md-6">
                <label>{_p('From')}</label>
                <input class="form-control" type="text" name="val[from_date]" id="from_date" autocomplete="off" placeholder="yyyy-mm-dd hh:ii:ss" value="{value type='input' id='from_date'}">
            </div>
            <div class="form-group col-md-6">
                <label>{_p('To')}</label>
                <input class="form-control" type="text" name="val[to_date]" id="to_date" autocomplete="off" placeholder="yyyy-mm-dd hh:ii:ss" value="{value type='input' id='to_date'}">
            </div>
            <div class="form-group col-md-6">
                <label>{_p('Included')}</label>
                <div>
                    <div class="checkbox-inline">
                        <label for="included_plugins"><input type="checkbox" name="val[plugins]" id="included_plugins" {if isset($plugins)}checked{/if}> <b>{_p('Plugins')}</b></label>
                    </div>
                    <div class="checkbox-inline">
                        <label for="included_themes"><input type="checkbox" name="val[themes]" id="included_themes" {if isset($themes)}checked{/if}> <b>{_p('Themes')}</b></label>
                    </div>
                    <div class="checkbox-inline">
                        <label for="included_uploads"><input type="checkbox" name="val[uploads]" id="included_uploads" {if isset($uploads)}checked{/if}> <b>{_p('Uploads')}</b></label>
                    </div>
                    <div class="checkbox-inline">
                        <label for="included_database"><input type="checkbox" name="val[database]" id="included_database" {if isset($database)}checked{/if}> <b>{_p('Database')}</b></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{_p('Search')}">
        </div>
    </div>
</form>
{if count($aSchedules)}
<form method="post" id="" action="" onsubmit="return $Core.BackupRestore.deleteSelectedSchedules(this);">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='Manage Schedules'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th><input type="checkbox" onclick="$Core.BackupRestore.checkAllSchedule();" id="schedules_check_all" name="schedules_check_all"/></th>
                    <th></th>
                    <th>{_p('Next Time')}</th>
                    <th>{_p('Schedule Name')}</th>
                    <th>{_p('Included')}</th>
                    <th>{_p('Destinations')}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aSchedules item=aSchedule}
                <tr>
                    <td style="width:20px">
                        <input type = "checkbox" class="schedule_row_checkbox" id="" name="schedule_row[]" value="{$aSchedule.schedule_id}" onclick="$Core.BackupRestore.scheduleCheckEnabled();"/>
                    </td>
                    <td class="t_center" style="width:20px">
                        <a href="#" class="js_drop_down_link" title="Options"></a>
                        <div class="link_menu">
                            <ul>
                                <li>
                                    <a href="{url link='admincp.ynbackuprestore.add-schedule' schedule_id=$aSchedule.schedule_id}">
                                        {_p('Edit')}
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="return $Core.BackupRestore.deleteSchedule({$aSchedule.schedule_id})">
                                        {_p('Delete')}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>{$aSchedule.start_date}</td>
                    <td>{$aSchedule.title}</td>
                    <td>{$aSchedule.sIncluded}</td>
                    <td>{$aSchedule.destinations}</td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer t_right">
            <input type="submit" name="val[delete_selected]" id="delete_selected" disabled value="{_p('Delete Selected')}" class="sJsConfirm delete_selected btn btn-danger disabled"/>
        </div>
    </div>
</form>
{pager}
{else}
    <div class="alert alert-info">{_p('No schedules found.')}</div>
{/if}
<script type="text/javascript" src="{$sAssetsDir}js/ynbackuprestore.js"></script>
{literal}
<script type="text/javascript">
    $Behavior.initLandingSlider = function(){
        $("#from_date").datetimepicker({
            format:'Y-m-d H:00:00'
        });
        $("#to_date").datetimepicker({
            format:'Y-m-d H:00:00'
        });
    }
</script>
{/literal}