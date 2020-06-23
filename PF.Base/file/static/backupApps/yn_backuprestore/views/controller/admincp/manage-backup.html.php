<link rel="stylesheet" href="{$sAssetsDir}datetimepicker/jquery.datetimepicker.css" type="text/css">
<div class="row">
    <div class="col-md-6">
        <div class="alert alert-info">
            <p>
                <b>{_p('Last backup')}</b>:
                {if isset($aLastBackup)}
                    {$aLastBackup.creation_timestamp|convert_time:'feed.feed_display_time_stamp'}
                {else}
                    {_p('N/A')}
                {/if}
            </p>
            <p>
                <b>{_p('Next scheduled backup')}</b>:
                {if isset($aNextSchedule)}
                    {$aNextSchedule}
                {else}
                    {_p('N/A')}
                {/if}
            </p>
            <p>
                <b>{_p('Last log file')}</b>:
                {if isset($aLastBackup)}
                    <a href="{url link='admincp.ynbackuprestore.download-log' id=$aLastBackup.backup_id}">logfile.txt</a>
                {else}
                    {_p('N/A')}
                {/if}
            </p>
            <p>
                <a href="{url link='admincp.ynbackuprestore.backup'}" class="btn btn-danger">{_p('Backup Now')}</a>
            </p>
        </div>
    </div>
</div>

<form action="">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('Find backup')}
            </div>
        </div>
        <div class="panel-body row dont-unbind-children">
            <div class="form-group col-md-6">
                <label for="">{_p('From')}</label>
                <input class="form-control" type="text" name="val[from_date]" id="from_date" placeholder="yyyy-mm-dd" autocomplete="off" value="{value type='input' id='from_date'}">
            </div>
            <div class="form-group col-md-6">
                <label for="">{_p('To')}</label>
                <input class="form-control" type="text" name="val[to_date]" id="to_date" placeholder="yyyy-mm-dd" autocomplete="off" value="{value type='input' id='to_date'}">
            </div>
            <div class="form-group col-md-6">
                <label for="">{_p('Type')}</label>
                <select name="val[backup_type]" class="form-control">
                    <option value="all" {if isset($backup_type) && ($backup_type == 'all')}selected{/if}>{_p var='all}}</option>
                    <option value="manual" {if isset($backup_type) && ($backup_type == 'manual')}selected{/if}>{_p('Manual')}</option>
                    <option value="automatic" {if isset($backup_type) && ($backup_type == 'automatic')}selected{/if}>{_p('Automatic')}</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="">{_p('Included')}</label>
                <div class="">
                    <label class="checkbox-inline"><input type="checkbox" name="val[plugins]" id="included_plugins" {if isset($plugins)}checked{/if}><b>{_p('Plugins')}</b></label>
                    <label class="checkbox-inline"><input type="checkbox" name="val[themes]" id="included_themes" {if isset($themes)}checked{/if}><b>{_p('Themes')}</b></label>
                    <label class="checkbox-inline"><input type="checkbox" name="val[uploads]" id="included_uploads" {if isset($uploads)}checked{/if}><b>{_p('Uploads')}</b></label>
                    <label class="checkbox-inline"><input type="checkbox" name="val[database]" id="included_database" {if isset($database)}checked{/if}><b>{_p('Database')}</b></label>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{_p('Search')}</button>
        </div>
    </div>
</form>

{if count($aBackups)}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='Manage Backups'}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>{_p('Date')}</th>
                <th>{_p('Type')}</th>
                <th>{_p('Included')}</th>
                <th>{_p('Destinations')}</th>
                <th>{_p('File Name')}</th>
                <th>{_p('File Size (MB)')}</th>
                <th>{_p('Log File')}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aBackups item=aBackup}
            <tr>
                <td>{$aBackup.creation_timestamp|convert_time:'feed.feed_display_time_stamp'}</td>
                <td>{_p var=$aBackup.type}</td>
                <td>{$aBackup.sIncluded}</td>
                <td>{$aBackup.destinations}</td>
                <td><a href="{url link='admincp.ynbackuprestore.download' id=$aBackup.backup_id}">{$aBackup.title}.{$aBackup.archive_format}</a></td>
                <td>{$aBackup.size}</td>
                <td><a href="{url link='admincp.ynbackuprestore.download-log' id=$aBackup.backup_id}">logfile.txt</a></td>
                <td><a href="{url link='admincp.ynbackuprestore.re-backup' backup_id=$aBackup.backup_id}">Re-Backup</a></td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{pager}
{else}
<div class="alert alert-info">
    {_p('No backups found.')}
</div>
{/if}

{literal}
<script type="text/javascript">
    $Behavior.initLandingSlider = function(){
        $("#to_date").datetimepicker({
            format:'Y-m-d',
            timepicker: false
        });
        $("#from_date").datetimepicker({
            format:'Y-m-d',
            timepicker: false
        });
    }
</script>
{/literal}
