<div class="error_message" id="backup_message">
    {_p var='ynbackuprestore_process_backup_description_replacement'}
</div>

<div class="panel panel-default" id="js_admincp_process_backup">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Backup Process')}
        </div>
    </div>
    <div class="panel-body">
        <b>{_p('Progress')}</b>
        <div id="myProgress">
            <div id="myBar">
                <div id="label">1%</div>
            </div>
        </div>
        <div id="process_status">{_p('Initializing backup process...')}</div>
        <hr>
        <div><b>{_p('Time Taken')}:</b> <span id="time_taken">0</span> {_p('sec')}</div>
        <div class="alert alert-info" id="backup_info">
            <p><b>{_p('Backup File')}:</b> <a href="{url link='admincp.ynbackuprestore.download' id=$id}"><span id="backup_file"></span></a></p>
            <p><b>{_p('File Size')}:</b> <span id="file_size"></span> MB</p>
            <p><b>{_p('Log File')}:</b> <a href="{url link='admincp.ynbackuprestore.download-log' id=$id}">logfile.txt</a></p>
        </div>
    </div>
</div>
<script type="text/javascript" src="{$sAssetsDir}js/ynbackuprestore.js"></script>
{literal}
<script type="text/javascript">
    var timer = setInterval(function () {
        var time_taken = $('#time_taken');
        var second = parseInt(time_taken.text());
        time_taken.text(second + 1);
    }, 1000);
    $Behavior.onLoadEvents = function () {
        if($('#js_admincp_process_backup:not(.executed)').length)
        {
            $.ajaxCall('ynbackuprestore.initializeProcess', 'id={/literal}{$id}{literal}');
            $('#js_admincp_process_backup').addClass('executed');
        }

    }
</script>
{/literal}