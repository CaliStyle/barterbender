<div class="error_message" id="backup_message">
    <ul>
        <li>
            {_p('Cannot write to below directories. Please make them writable and click Restart')}
        </li>
        {foreach from=$errorFolders item=errorFolder}
        <li>
            {$errorFolder}
        </li>
        {/foreach}
    </ul>
</div>
<div class="table_clear">
    <input type="submit" class="btn btn-success" onclick="window.location.href = window.location.href;" value="Restart">
</div>