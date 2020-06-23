<div class="error_message" id="backup_message">
    {_p var='ynbackuprestore_process_backup_description'}
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Restore Process')}
        </div>
    </div>
    <div class="panel-body">
        <b>{_p('Progress')}</b>
        <div id="myProgress">
            <div id="myBar">
                <div id="label">1%</div>
            </div>
        </div>
        <div id="process_status">{_p('Initializing restore process...')}</div>
        <hr>
        <div class="alert alert-info">
            <div><b>{_p('Time Taken')}:</b> <span id="time_taken">0</span> {_p('sec')}</div>
            <div><b>{_p('Restore File')}</b>: {$file}</div>
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
        $.ajaxCall('ynbackuprestore.restoreInitializeProcess', 'file={/literal}{$file}{literal}');
    }
</script>
{/literal}