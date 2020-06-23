<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
<div class="alert alert-danger">
    <b>{_p var='backup_database'} </b> {_p var='before_run_the_import_from_original_event_default_event_of_phpfox_to_fevent_module'} .<br/>
    {_p var='fevent_does_not_import_activity_feeds_notifications_from_old_event'}.
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
           {_p var='import_details'}
        </div>
    </div>
    <div class="panel-body">
        <div class="extra_info">
            {_p var='following_the_instruction_below_to_import_event_from_phpfox_event_module'} :<br/>
            1. {_p var='backup_database_lower'}.<br/>
            2. {_p var='click_import_button'}.<br/>
        </div>

        <div class="form-group">
            <label for="process">
                {_p var='process'} :
            </label>
            <div id="contener_pro" style="width: 100%;border:1px solid black;height:25px;line-height:25px;text-align: center;">
                <div id="contener_percent" style="background-color: #F4645F; height: 100%; width: 0%;">
                   &nbsp;0%
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="details">
                {_p var='details'} :
            </label>
            <div id="info_process"></div>
        </div>
    </div>
	<div class="panel-footer">
		<input type="submit" value="Import" class="btn btn-primary" id="migrate" onclick="javascript:mir()"/>
	</div>
</div>
 <script type="text/javascript">
 {literal}
    function mir()
    {
        $.ajaxCall('fevent.migrateData','');
    }
 {/literal}
 </script>
    