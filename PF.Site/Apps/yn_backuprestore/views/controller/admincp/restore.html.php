<div class="error_message">
    {_p var='ynbackuprestore_restore_now_description'}
</div>
<div class="error_message">
    {_p('Your restore site should match the version with backup site in order to restore successfully.')}
</div>
<form method="post" action="" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='Restore'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{required}{_p('Restore from File')}</label>
                <div class="help-block">
                    {_p('Please choose backup file to restore')} ({_p('Max')}: {$sMaxUploadFileSize})<br>
                </div>
                <input type="file" name="backup_file" class="form-control">
            </div>
            <div class="table">
                <label>{required}{_p('Set site offline')}</label>
                <div class="help-block">
                    {$sOffline}
                </div>
                <div class="radio">
                    <label for="yes_maintenance_mode"><input type="radio" name="val[maintenance_mode]" value="yes" id="yes_maintenance_mode" checked> <b>{_p var='ynbackuprestore_turn_site_to_offline'}</b></label>
                </div>
                <div class="radio">
                    <label for="no_maintenance_mode"><input type="radio" name="val[maintenance_mode]" value="no" id="no_maintenance_mode"> <b>{_p var='ynbackuprestore_keep_site_online'}</b></label>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p('Restore')}" class="btn btn-primary" name="submit "/>
        </div>
    </div>
</form>
