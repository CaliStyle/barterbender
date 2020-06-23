<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright        [YOUNET_COPPYRIGHT]
 * @author           AnNT
 * @package          Module_jobposting
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<form id="js_jp_promote_job_form">
	<div class="ynjp_promote_code" style="width: 320px; float: left;">
        <div class="table_left">{phrase var='embed_code'}:</div>
        <div class="table_right">
    		<textarea id="js_jp_promote_code_textarea" readonly="readonly" style="width:300px; height:150px;">{$sPromoteCode}</textarea>
    		<div class="clear"></div>
    		<label><input type="checkbox" checked ="true" name="val[en_photo]" onclick="$('#js_jp_promote_job_form').ajaxCall('jobposting.changePromoteCode', 'id={$iId}');" /> {phrase var='enable_company_photo'}</label><br />
    		<label><input type="checkbox" checked ="true" name="val[en_description]" onclick="$('#js_jp_promote_job_form').ajaxCall('jobposting.changePromoteCode', 'id={$iId}');" /> {phrase var='enable_description'}</label><br />
        </div>
	</div>

	<div class="ynjp_promote_review" style="width: 180px; float: left; margin-left: 10px;">
		<div class="table_left">{phrase var='review'}:</div>
		<div id="js_jp_promote_iframe">{$sPromoteCode}</div>
	</div>
	
	<div class="clear"></div>
</form>

