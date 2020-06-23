<?php 
    defined('PHPFOX') or exit('NO DICE!');    
	$this->_aVars["click_url"] = Phpfox::getLib('url')->makeUrl('admincp.document.backupdb');
?>
<div class="error_message">
    <b>{phrase var='backup_database'} </b> {phrase var='before_run_the_import_from_older_version_of_younet_document' click_url=$click_url} .<br/>
    {phrase var='document_does_not_import_activity_feeds_notifications'}.
</div>
<div class="tip">
    {phrase var='following_the_instruction_below_to_import'} :<br/>
    1. {phrase var='backup_database_lower' click_url=$click_url}.<br/>
    2. {phrase var='click_import_button'}.<br/>
    
</div>

<div class="table_header">
       {phrase var='import_details'}
</div>
    <div class="table">
        <div class="table_left">
            {phrase var='process'} :
        </div>
        <div class="table_right">
            <div id="contener_pro" style="width: 100%;border:1px solid black;height:20px;text-align: center;">
                <div id="contener_percent" style="background-color: fuchsia; height: 16px; width: 0%; padding-top: 4px;">
                   &nbsp;0%
                </div>
              
            </div>
        </div>
        <div class="clear"></div>
    </div>
	<br clear="all" />
	<div class="table_clear">
		<input type="submit" value="Import" class="button" id="migrate" onclick="javascript:mir()"/>
	</div>
 <script type="text/javascript">
 {literal}
    function mir()
    {
        $.ajaxCall('document.migrate','');     
    }
 {/literal}
 </script>
    