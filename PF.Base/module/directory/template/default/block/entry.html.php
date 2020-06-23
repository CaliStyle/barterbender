<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_directory_entry{$aBusiness.business_id}">
	<div class="yndirectory-image-directory">
		<a class="yndirectory_directory_img" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" title="{$aBusiness.name|clean}">
			<span style="background:url({$sNoimageUrl}) no-repeat center center;display:block;text-indent:-99999px;height:110px;width:165px;">
			</span>
        </a>        
	</div>
    <a href="#" class="image_hover_menu_link">{phrase var='link'}</a>
    <div class="image_hover_menu">
        <ul>
            {template file='directory.block.link'}
        </ul>
    </div>
    <div class="yndirectory_title_info">
        <p id="js_directory_edit_title{$aBusiness.business_id}" class="yndirectory-title">
            <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" id="js_directory_edit_inner_title{$aBusiness.business_id}" class="link ajax_link">{$aBusiness.name|clean|shorten:25:'...'|split:10} </a>
        </p>
        <div class="extra_info">
            <p><i class="fa fa-phone"></i> {phrase var='call_us'}</p>
            <p><i class="fa fa-globe"></i> {phrase var='website'}</p>
            <p><i class="fa fa-envelope"></i> {phrase var='email'}</p>
            <p><i class="fa fa-location-arrow"></i> {phrase var='direction'}</p>
        </div>  
    </div>
</div>