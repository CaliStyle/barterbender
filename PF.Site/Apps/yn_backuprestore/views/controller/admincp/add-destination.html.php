<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($sErrors)}
<div class="error_message">
    {$sErrors}
</div>
{/if}
{if isset($amazonError)}
<div class="error_message">
    {$amazonError}
</div>
{/if}
<form method="post" action="" id="form-add-destination">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if isset($bIsEdit)}{_p('Edit Destination')}{else}{_p('Add New Destination')}{/if}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{required}{_p('Destination Type')}</label>
                <p class="help-block">{_p('Please specific the remote storage you want the backup file to be saved.')}</p>
                <select name="val[type_id]" onchange="$Core.BackupRestore.changeDestinationType()" class="form-control">
                    {foreach from=$aTypes item=aType}
                        {if ($aType.type_id != 1)}
                            <option value="{$aType.type_id}" {if $iTypeId == $aType.type_id}selected{/if}>{$aType.title}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label>{required}{_p('Destination Name')}</label>
                <span class="help-block">{_p('Please specific this Destination Name for your reference. Destination Name can be used for backing-up manually or by scheduled.')}</span>
                <input class="form-control" type="text" name="val[title]" value="{value type='input' id='title'}">
            </div>

            <!-- CASE EMAIL -->
            {if $iTypeId == 2}
            <p class="alert alert-warning">
                <b>{_p var='Note!!!'}</b> {_p('Be aware that mail servers tend to have size limits, typically around 10-20 MB. Backups larger than any limits will likely not arrive.')}
            </p>
            <div class="form-group">
                <label>{required}{_p('Email Address')}</label>
                <div class="help-block">{_p var='ynbackuprestore_email_address_description'}</div>
                {editor id='email_address'}
            </div>

            <!-- CASE FTP -->
            {elseif $iTypeId == 3}
            <div class="form-group">
                <label>{required}{_p('FTP Server')}</label>
                <input class="form-control" type="text" name="val[ftp_server]" value="{value type='input' id='ftp_server'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('FTP Login')}</label>
                <input class="form-control" type="text" name="val[ftp_login]" value="{value type='input' id='ftp_login'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('FTP Password')}</label>
                <input class="form-control" type="password" name="val[ftp_password]" value="{value type='input' id='ftp_password'}">
            </div>
            <div class="form-group">
                <label>{_p('Remote Path')}</label>
                <p class="help-block">{_p('If this directory does not already exist on the FTP server, then it will be created.')}</p>
                <input class="form-control" type="text" name="val[ftp_remote]" value="{value type='input' id='ftp_remote'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Passive Mode')}</label>
                <p class="help-block">{_p('Place FTP server in')}</p>
                {if isset($ftp_active)}
                    <input type="radio" name="val[ftp_mode]" value="0" id="passive">
                    <label for="passive">{_p('Passive Mode')}</label><br>
                    <input type="radio" name="val[ftp_mode]" value="1" id="active" checked>
                    <label for="active">{_p('Active Mode')}</label>
                {else}
                    <input type="radio" name="val[ftp_mode]" value="0" id="passive" checked>
                    <label for="passive">{_p('Passive Mode')}</label><br>
                    <input type="radio" name="val[ftp_mode]" value="1" id="active">
                    <label for="active">{_p('Active Mode')}</label>
                {/if}
            </div>

            {elseif $iTypeId == 4}
            <div class="form-group">
                <label>{required}{_p('Host')}</label>
                <input class="form-control" type="text" name="val[sftp_host]" value="{value type='input' id='sftp_host'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Port')}</label>
                <input class="form-control" type="text" name="val[sftp_port]" value="{value type='input' id='sftp_port'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Username')}</label>
                <input class="form-control" type="text" name="val[sftp_username]" value="{value type='input' id='sftp_username'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Password')}</label>
                <input class="form-control" type="password" name="val[sftp_password]" value="{value type='input' id='sftp_password'}">
            </div>
            <div class="form-group">
                <label>{_p('Directory Path')}</label>
                <input class="form-control" type="text" name="val[sftp_directory]" value="{value type='input' id='sftp_directory'}">
            </div>
            <div class="form-group">
                <label>{_p('SCP')}</label>
                <div>
                    <label class="checkbox-inline"><input type="checkbox" name="val[sftp_scp]" id="scp" {if isset($sftp_scp)}checked{/if}><b>{_p('Use SCP instead of SFTP')}</b></label>
                </div>
            </div>

            {elseif $iTypeId == 5}
            <div class="form-group">
                <label>{required}{_p('Host')}</label>
                <input class="form-control" type="text" name="val[mysql_host]" value="{value type='input' id='mysql_host'}">
            </div>
            <div class="form-group">
                <label>{_p('Port')}</label>
                <input class="form-control" type="text" name="val[mysql_port]" value="{value type='input' id='mysql_port'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Database Name')}</label>
                <div class="help-block">{_p('Please specific the name of the database. This database must exist, and it will not be created.')}</div>
                <input class="form-control" type="text" name="val[mysql_dbname]" value="{value type='input' id='mysql_dbname'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Username')}</label>
                <input class="form-control" type="text" name="val[mysql_username]" value="{value type='input' id='mysql_username'}">
            </div>
            <div class="form-group">
                <label>{_p('Password')}</label>
                <input class="form-control" type="password" name="val[mysql_password]" value="{value type='input' id='mysql_password'}">
            </div>

            {elseif $iTypeId == 6}
            <div class="form-group">
                <label>{required}{_p('S3 Access Key')}</label>
                <input class="form-control" type="text" name="val[s3_access]" value="{value type='input' id='s3_access'}" id="s3_access">
            </div>
            <div class="form-group">
                <label>{required}{_p('S3 Secret Key')}</label>
                <input class="form-control" type="text" name="val[s3_secret]" value="{value type='input' id='s3_secret'}" id="s3_secret">
            </div>
            <div class="table">
                <label>{required}{_p('S3 Bucket')}</label>
                <div class="help-block">
                    {_p('After enter access key and secret key, click <b>Get List Bucket</b> to retrieve your buckets.')}
                </div>
                <select name="val[s3_bucket]" class="form-control" id="s3_bucket">
                    <option>{if isset($s3_bucket)}{$s3_bucket}{else}...{/if}</option>
                </select>
                <br>
                <input type="button" class="btn btn-success" value="{_p('Get List Buckets')}" onclick="$Core.BackupRestore.getListBuckets()" id="s3_get_list_bucket">
                <div id="amazon_no_bucket"></div>
            </div>

            {elseif $iTypeId == 7}
            <div class="alert alert-info">
                {_p('ynbackuprestore_dropbox_instruction')}
            </div>
            <div class="form-group">
                <label>{required}{_p('App Key')}</label>
                <input class="form-control" type="text" name="val[dropbox_key]" value="{value type='input' id='dropbox_key'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('App Secret')}</label>
                <input class="form-control" type="text" name="val[dropbox_secret]" value="{value type='input' id='dropbox_secret'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Access Token')}</label>
                <input class="form-control" type="text" name="val[dropbox_token]" value="{value type='input' id='dropbox_token'}">
            </div>
            <div class="form-group">
                <label>{_p('Directory Path')}</label>
                <input class="form-control" type="text" name="val[dropbox_store]" value="{value type='input' id='dropbox_store'}">
            </div>

            {elseif $iTypeId == 8}
            <div class="alert alert-info">
                {_p('ynbackuprestore_onedrive_instruction')}<br>
                {$sOnedriveRedirectUri}
            </div>
            <div class="form-group">
                <label>{required}{_p('Application ID')}</label>
                <input class="form-control" type="text" name="val[onedrive_id]" value="{value type='input' id='onedrive_id'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Application Secret')}</label>
                <input class="form-control" type="text" name="val[onedrive_secret]" value="{value type='input' id='onedrive_secret'}">
            </div>
            <div class="form-group">
                <label>{_p('Directory Path')}</label>
                <input class="form-control" type="text" name="val[onedrive_directory]" value="{value type='input' id='onedrive_directory'}">
            </div>

            {elseif $iTypeId == 9}
            <div class="alert alert-info">
                {_p('ynbackuprestore_google_instruction')}
                {$sGoogleRedirectUri}
            </div>
            <div class="form-group">
                <label>{required}{_p('Client ID')}</label>
                <input class="form-control" type="text" name="val[google_id]" value="{value type='input' id='google_id'}">
            </div>
            <div class="form-group">
                <label>{required}{_p('Client Secret')}</label>
                <input class="form-control" type="text" name="val[google_secret]" value="{value type='input' id='google_secret'}">
            </div>
            <div class="form-group">
                <label>{_p('Directory Path')}</label>
                <input class="form-control" type="text" name="val[google_folder]" value="{value type='input' id='google_folder'}">
            </div>
            {/if}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{if isset($bIsEdit)}{_p('Update')}{else}{_p('Create')}{/if}" class="btn btn-primary" name="submit "/>
        </div>
    </div>
</form>

<script type="text/javascript" src="{$sAssetsDir}js/ynbackuprestore.js"></script>