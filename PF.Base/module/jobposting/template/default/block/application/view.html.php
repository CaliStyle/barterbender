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

<div class="ynjp_apply_header_holder">
	<div class="ynjp_applyformTitle">{$aItem.form_title}</div>
	<p class="ynjp_applyformDesc">{$aItem.form_description}</p>
</div>
<div class="clear"> </div>
<div class="ynjp_apply_Jobheader_holder">
	<a title="Postcards" href="" class="ynjp_applyform_infoThumb">
        {if !empty($aItem.logo_path)}
		{img server_id=$aItem.logo_server_id path='core.url_pic' file="jobposting/".$aItem.logo_path suffix='_50' max_width='50' max_height='50'}
        {else}
        <img src="{$sDefaultImage}" alt="" height="50" width="50">
        {/if}
	</a>
	<div class="ynjp_applyJobform_Info">
		<div class="ynjp_applyJobTitle">{$aItem.job_title}</div>
		<p class="ynjp_applyformInfo"><strong>{$aItem.company_name}</strong> - {$aItem.map_location}</p>
	</div>
</div>
<div class="clear"> </div>

<div class="ynjp_application ynjp_apply_Job_form">
	<div class="ynjp_application_employee ynjp_row_employee_loop">
		<div class="ynjp_employee_image">
			<a href="#">{img server_id=$aItem.server_id path='core.url_pic' file='jobposting/'.$aItem.photo_path suffix='_120' max_width='80' max_height='80'}</a>
		</div>
		<div class="ynjp_row_employee_name">
			<span>{$aItem.name}</span>
		</div>
	</div>
	<div class="clear"> </div>

    {if !empty($aItem.email)}
    <div class="table form-group">
    	<div class="table_left view_application_title">
    		{phrase var='email'}
    	</div>
    	<div class="table_right">
    		{$aItem.email}
    	</div>
    </div>
    {/if}

    {if !empty($aItem.telephone)}
    <div class="table form-group">
    	<div class="table_left view_application_title">
    		{phrase var='telephone'}
    	</div>
    	<div class="table_right">
    		{$aItem.telephone}
    	</div>
    </div>
    {/if}

    {if !empty($aItem.custom_field)}
    {foreach from=$aItem.custom_field item=aField}
        {template file='jobposting.block.custom.view'}
    {/foreach}
    {/if}

    {if !empty($aItem.resume)}
    <div class="table form-group">
    	<div class="table_left view_application_title">
    		{phrase var='resume'}
    	</div>
    	<div class="table_right">
            {if $aItem.resume_type==1}
    		<a href="{permalink module='resume.view' id=$aItem.resume}">{phrase var='view'}</a> |
            {/if}
            <a class="no_ajax_link" href="{$urlFolder}module/jobposting/static/php/download.php?id={$aItem.application_id}">{phrase var='download'}</a>
    	</div>
    </div>
    {/if}
</div>
<script>
	$Core.loadInit();
</script>