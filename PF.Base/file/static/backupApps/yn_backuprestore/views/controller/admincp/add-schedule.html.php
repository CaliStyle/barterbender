<link rel="stylesheet" href="{$sAssetsDir}datetimepicker/jquery.datetimepicker.css" type="text/css">
<div class="error_message" id="error_name">
    * {_p('Schedule Name is required.')}
</div>
<div class="error_message" id="error_datetime">
    * {_p('Backup Intervals is required.')}
</div>
<div class="error_message" id="error_include">
    * {_p('Include In Backup is required.')}
</div>
<div class="error_message" id="error_plugin">
    * {_p('Select Plugins is required.')}
</div>
<div class="error_message" id="error_theme">
    * {_p('Select Themes is required.')}
</div>
<div class="error_message" id="error_upload">
    * {_p('Select Upload Folders is required.')}
</div>
<div class="error_message" id="error_database">
    * {_p('Select Database Tables is required.')}
</div>
<div class="error_message" id="error_prefix">
    * {_p('Backup Filename Prefix is required.')}
</div>
<form method="post" onsubmit="return $Core.BackupRestore.validateScheduleForm();">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('Add new schedule')}
            </div>
        </div>
        <div class="panel-body">
            <div>
                {if isset($id)}
                <input type="hidden" value="{$id}" name="schedule_id">
                {/if}
            </div>

            <div class="form-group">
                <label for="schedule_name">{required}{_p('Schedule Name')}</label>
                <div class="help-block">
                    {_p('Please specfic this Schedule Name for your reference.')}
                </div>
                <input class="form-control" type="text" name="val[schedule_name]" value="{value type='input' id='title'}" id="schedule_name">
            </div>
            <div class="form-group">
                <label for="schedule_select_interval">{required}{_p('Backup Intervals')}</label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="val[interval]" class="form-control" id="schedule_select_interval">
                            <option value="4hours" {if isset($aForms) && $aForms.interval == '4hours'}selected{/if}>{_p('Every 4 hours')}</option>
                            <option value="8hours" {if isset($aForms) && $aForms.interval == '8hours'}selected{/if}>{_p('Every 8 hours')}</option>
                            <option value="12hours" {if isset($aForms) && $aForms.interval == '12hours'}selected{/if}>{_p('Every 12 hours')}</option>
                            <option value="daily" {if isset($aForms) && $aForms.interval == 'daily'}selected{/if}>{_p('Daily')}</option>
                            <option value="weekly" {if isset($aForms) && $aForms.interval == 'weekly'}selected{/if}>{_p('Weekly')}</option>
                            <option value="fortnightly" {if isset($aForms) && $aForms.interval == 'fortnightly'}selected{/if}>{_p('Fortnightly')}</option>
                            <option value="monthly" {if isset($aForms) && $aForms.interval == 'monthly'}selected{/if}>{_p('Monthly')}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon" id="schedule_middle">{_p('start at')}</span>
                            <input class="form-control" name="val[datetime]" type="text" id="datetime" value="{value type='input' id='start_date'}" placeholder="Y-m-d H:i:s" aria-describedby="schedule_middle">
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group">
                <label for="destinations">{_p('Backup Destination')}</label>
                <div class="help-block">
                    {_p('Please select the destination in which you want to store this backup file. You can also add more destinations in Destinations section.')}
                </div>
                <select class="form-control" multiple name="val[destination_ids][]" id="destinations">
                    {foreach from=$aDestinations item=aDestination key=index}
                        {if isset($aDestinationIds)}
                            {assign var='selected' value='false'}
                            {foreach from=$aDestinationIds item=destinationId}
                                {if ($aDestination.destination_id eq $destinationId)}
                                    {assign var='selected' value='true'}
                                {/if}
                            {/foreach}
                            {if isset($selected) && $selected eq 'true'}
                                <option value="{$aDestination.destination_id}" selected>{$aDestination.title}</option>
                            {else}
                                <option value="{$aDestination.destination_id}">{$aDestination.title}</option>
                            {/if}
                        {else}
                            {if $index==0}
                            <option value="{$aDestination.destination_id}" selected>{$aDestination.title}</option>
                            {else}
                            <option value="{$aDestination.destination_id}">{$aDestination.title}</option>
                            {/if}
                        {/if}
                    {/foreach}
                </select>
            </div>
            {if $sEmailsDest}
            <div class="alert alert-warning" id="email_message">
                <b>{_p var='Note!!!'}</b> {_p('Destination type <b>Email</b> warning: Be aware that mail servers tend to have size limits, typically around 10-20 MB.
                Backups larger than any limits will likely not arrive.')}
            </div>
            {/if}
            <div class="form-group">
                <label>{required}{_p('Include In Backup')}</label>
                <div class="alert alert-info">
                    {_p('backuprestore_select_type_to_backup')}
                </div>
                <label class="checkbox-inline"><input type="checkbox" name="val[plugin]" id="backuprestore_plugin" {if isset($aForms) && $aForms.plugins_included !='[]'}checked{/if}><b>{_p('Plugins')}</b></label>
                <label class="checkbox-inline"><input type="checkbox" name="val[theme]" id="backuprestore_theme" {if isset($aForms) && $aForms.themes_included !='[]'}checked{/if}><b>{_p('Themes')}</b></label>
                <label class="checkbox-inline"><input type="checkbox" name="val[upload]" id="backuprestore_upload" {if isset($aForms) && $aForms.uploads_included !='[]'}checked{/if}><b>{_p('Uploads')}</b></label>
                <label class="checkbox-inline"><input type="checkbox" name="val[database]" id="backuprestore_database" {if isset($aForms) && $aForms.database_included !='[]'}checked{/if}><b>{_p('Database')}</b></label>
            </div>

            <!-- SELECT PLUGINS -->
            <div class="form-group clearfix" id="table_plugin">
                <label>{required}{_p('Select Plugins')}</label>
                <div class="help-block">{_p('Please select plugins that you want to backup.')}</div>
                <div class="plugin_item_option_all" id="plugin_item_option_all">
                    <input type="checkbox" name="plugin[all]" class="select_all" id="plugin_all" {if isset($aScheduleApps) && (count($aApps) + count($aModules)) == (count($aScheduleApps) + 1)} checked {/if}>
                    <label for="plugin_all">{_p('Select All')}</label>
                </div>
                <hr>
                {foreach from=$aApps item=aApp}
                <div class="plugin_item_option">
                    {if isset($aScheduleApps)}
                        <input type="checkbox" name="plugin[app_{$aApp.id}]" id="app_{$aApp.id}" {if in_array($aApp.id, $aScheduleApps)} checked{/if}>
                    {else}
                        <input type="checkbox" name="plugin[app_{$aApp.id}]" id="app_{$aApp.id}">
                    {/if}
                    <label for="app_{$aApp.id}">{$aApp.name}</label><br>
                </div>
                {/foreach}
                <div class="clearfix"></div>
                <hr>
                {foreach from=$aModules item=aModule}
                <div class="plugin_item_option">
                    {if isset($aScheduleApps)}
                        <input type="checkbox" name="plugin[module_{$aModule.id}]" id="module_{$aModule.id}" {if in_array($aModule.id, $aScheduleApps)} checked{/if}>
                    {else}
                        <input type="checkbox" name="plugin[module_{$aModule.id}]" id="module_{$aModule.id}">
                    {/if}
                    <label for="module_{$aModule.id}">{$aModule.name}</label><br>
                </div>
                {/foreach}
            </div>

            <!-- SELECT THEMES -->
            <div class="form-group" id="table_theme">
                <label>{required}{_p('Select Themes')}</label>
                <div class="help-block">{_p('Please select themes that you want to backup.')}</div>
                <div class="plugin_item_option_all" id="plugin_item_option_all">
                    <input type="checkbox" name="theme[all]" class="select_all" id="themes_all" {if isset($aScheduleThemes) && count($aThemes) == count($aScheduleThemes)} checked{/if}>
                    <label for="themes_all">{_p('Select All')}</label>
                </div>
                <hr>
                {foreach from=$aThemes item=aTheme}
                <div class="plugin_item_option">
                    {if isset($aScheduleThemes)}
                        <input type="checkbox" name="theme[{$aTheme.folder}]" id="theme_{$aTheme.folder}" {if in_array($aTheme.folder, $aScheduleThemes)} checked{/if}>
                    {else}
                        <input type="checkbox" name="theme[{$aTheme.folder}]" id="theme_{$aTheme.folder}">
                    {/if}
                    <label for="theme_{$aTheme.folder}">{$aTheme.name}</label><br>
                </div>
                {/foreach}
            </div>
            <div class="form-group backup_next_line" id="table_upload">
                <label>{required}{_p('Select Upload Folders')}</label>
                <div class="help-block">{_p('Please select upload folders that you want to backup.')}</div>
                <div class="plugin_item_option_all" id="plugin_item_option_all">
                    <input type="checkbox" name="upload[all]" class="select_all" id="upload_all" {if isset($aScheduleUploads) && count($aUploads) == count($aScheduleUploads)} checked{/if}>
                    <label for="upload_all">{_p('Select All')}</label>
                </div>
                <hr>
                {foreach from=$aUploads item=aUpload}
                <div class="plugin_item_option">
                    {if isset($aScheduleUploads)}
                        <input type="checkbox" name="upload[{$aUpload}]" id="upload_{$aUpload}" {if in_array($aUpload, $aScheduleUploads)} checked{/if}>
                    {else}
                        <input type="checkbox" name="upload[{$aUpload}]" id="upload_{$aUpload}">
                    {/if}
                    <label for="upload_{$aUpload}">{$aUpload}</label><br>
                </div>
                {/foreach}
            </div>
            <div class="form-group backup_next_line" id="table_database">
                <label>{required}{_p('Select Database Tables')}</label>
                <div class="help-block">{_p('Please select database tables that you want to backup.')}</div>
                <div class="plugin_item_option_all" id="plugin_item_option_all">
                    <input type="checkbox" name="database[all]" class="select_all" id="database_all" {if isset($aScheduleDatabase) && count($aDatabase) == count($aScheduleDatabase)} checked {/if}>
                    <label for="database_all">{_p('Select All')}</label>
                </div>
                <hr>
                {foreach from=$aDatabase item=aTable}
                <div class="db_item_option">
                    {if isset($aScheduleDatabase)}
                        <input type="checkbox" name="database[{$aTable.Name}]" id="db_{$aTable.Name}" {if in_array($aTable.Name, $aScheduleDatabase)} checked{/if}>
                    {else}
                        <input type="checkbox" name="database[{$aTable.Name}]" id="db_{$aTable.Name}">
                    {/if}
                    <label for="db_{$aTable.Name}">{$aTable.Name}</label><br>
                </div>
                {/foreach}
            </div>
            <div class="form-group backup_next_line">
                <label>{required}{_p('Backup Filename Prefix')}</label>
                <div class="help-block">
                    {_p var='ynbackuprestore_please_specify_the_prefix_for_the_name_of_this_backup_file'}
                </div>
                {if isset($aForms)}
                    <input class="form-control" type="text" name="val[prefix]" id="prefix" value="{value type='input' id='prefix'}">
                {else}
                    <input class="form-control" type="text" name="val[prefix]" id="prefix" value="backup">
                {/if}
            </div>
            <div class="form-group backup_next_line clearfix">
                <label>{required}{_p('Archive Format')}</label>
                <div class="help-block">
                    {_p('Please select an archive format for this backup.')}
                </div>
                <div class="radio">
                    <label for="zip"><input type="radio" name="val[archive_format]" value="zip" id="zip" checked> <b>Zip</b></label>
                </div>
                <div class="radio">
                    <label for="tar"><input type="radio" name="val[archive_format]" value="tar" id="tar" {if isset($aForms) && $aForms.archive_format=='tar'}checked{/if}> <b>Tar</b></label>
                </div>
                <div class="radio">
                    <label for="tar.gz"><input type="radio" name="val[archive_format]" value="tar.gz" id="tar.gz" {if isset($aForms) && $aForms.archive_format=='tar.gz'}checked{/if}> <b>Tar GZip</b></label>
                </div>
                <div class="radio">
                    <label for="tar.bz2"><input type="radio" name="val[archive_format]" value="tar.bz2" id="tar.bz2" {if isset($aForms) && $aForms.archive_format=='tar.bz2'}checked{/if}> <b>Tar BZip2</b></label>
                </div>
            </div>
            <div class="form-group backup_next_line">
                <label>{required}{_p('Set site offline')}</label>
                <div class="help-block">
                    {$sOffline}
                </div>
                <div class="radio">
                    <label for="yes"><input type="radio" name="val[maintenance_mode]" value="yes" id="yes" checked> <b>{_p('Yes, turn site to offline during backup')}.</b></label>
                </div>
                <div class="radio">
                    <label for="no"><input type="radio" name="val[maintenance_mode]" value="no" id="no"  {if isset($aForms) && $aForms.maintenance_mode!='1'}checked{/if}> <b>{_p('No, keep site online during backup')}.</b></label>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{if isset($id)}{_p('Update')}{else}{_p('Add Schedule')}{/if}" class="btn btn-primary" name="submit "/>
        </div>
    </div>
</form>
<script type="text/javascript" src="{$sAssetsDir}js/ynbackuprestore.js"></script>
{literal}
<script type="text/javascript">
    $Behavior.onLoadEvents = function() {
        if ($("#backuprestore_plugin").is(':checked')) {
            $("#table_plugin").show();
        }
        if ($("#backuprestore_theme").is(':checked')) {
            $("#table_theme").show();
        }
        if ($("#backuprestore_upload").is(':checked')) {
            $("#table_upload").show();
        }
        if ($("#backuprestore_database").is(':checked')) {
            $("#table_database").show();
        }
        $(document).off("click", ".select_all").on("click", ".select_all", function () {
            if ($(this).prop('checked')) {
                $(this).parent().parent().find('input').each(function () {
                    $(this).prop('checked', true)
                });
            }
            else {
                $(this).parent().parent().find('input').each(function () {
                    $(this).prop('checked', false);
                });
            }
        });
        $('#backuprestore_plugin').off('change').change(function(){
            $('#table_plugin').toggle(this.checked);
        }).change();
        $('#backuprestore_theme').off('change').change(function(){
            $('#table_theme').toggle(this.checked);
        }).change();
        $('#backuprestore_upload').off('change').change(function(){
            $('#table_upload').toggle(this.checked);
        }).change();
        $('#backuprestore_database').off('change').change(function(){
            $('#table_database').toggle(this.checked);
        }).change();
        $(document).off("change", "#destinations").on("change", "#destinations", function () {
            var dests = $(this).val();
            var emails = [{/literal}{$sEmailsDest}{literal}];
            var database = [{/literal}{$sDatabaseDest}{literal}];
            var hasEmailType = false;
            var isOnlyDatabase = true;
            dests.forEach(function (dest, index) {
                if ($.inArray(dest, emails) > -1) {
                    hasEmailType = true;
                }
                if (!($.inArray(dest, database) > -1)) {
                    isOnlyDatabase = false;
                }
            });
            if (hasEmailType) {
                $("#email_message").show();
            } else {
                $("#email_message").hide();
            }
            console.log(isOnlyDatabase);
            if (isOnlyDatabase) {
                $("#plugin_wrapper").hide();
                $("#upload_wrapper").hide();
                $("#theme_wrapper").hide();
            } else {
                $("#plugin_wrapper").show();
                $("#upload_wrapper").show();
                $("#theme_wrapper").show();
            }
        });

        $('#destinations').trigger('change');
    }
</script>
{/literal}
{literal}
<script type="text/javascript">
    $Behavior.initLandingSlider = function(){
        var script = document.createElement('script');
        script.src = '{/literal}{$sAssetsDir}{literal}datetimepicker/jquery.datetimepicker.js';
        script.onload = function () {
            $("#datetime").datetimepicker({
                format:'Y-m-d H:m:s',
                minDate:'0'
            });
        };
        document.getElementsByTagName("head")[0].appendChild(script);
    }
</script>
{/literal}