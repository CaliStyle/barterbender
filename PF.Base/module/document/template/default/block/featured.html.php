<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

{foreach from=$aDocuments_featured item=aDocument}
<div class="topview_block clearfix">
	<div class="table_left_topview">
		  <div id="document_image_{$aDocument.document_id}">
				<div class="js_mp_fix_holder image_hover_holder">                         
					<a href="{$aDocument.link}">
                        {if !empty($aDocument.image_url) && strpos($aDocument.image_url,'http') !== false}
                            <img id="js_img_block_{$aDocument.document_id}" class="document_normal" onload title="{$aDocument.title}" src="{$aDocument.image_url}" onerror="this.src='{$no_image_url}';" style="height:70px; width:50px;"/>
                        {else}
                            {img server_id=$aDocument.image_server_id path='core.url_pic' file='document/'.$aDocument.image_url suffix='_400' max_height=70 max_width =50}
                        {/if}
                    </a>
				</div>
		</div>
	</div>
	<div class="table_right_topview">
		<div class="topview_div_title"><a class="topview_title" href="{$aDocument.link}" title="{$aDocument.title}">{$aDocument.title|shorten:20:'...'}</a></div>
		<div class="extra_info">
		   <div class="topview_extra_info_div">{phrase var='by'} <a href="{$aDocument.full_name_link}" {if $aDocument.is_long_name}title="{$aDocument.full_name}"{/if}>{$aDocument.full_name|shorten:20:'...'}</a></div>
			<div> {if $aDocument.total_view == 1}{$aDocument.total_view} {phrase var='view'}{else} {$aDocument.total_view} {phrase var='views'}{/if} | {if $aDocument.total_like == 1}{$aDocument.total_like} {phrase var='like'}{else}{$aDocument.total_like} {phrase var='likes'} {/if}</div>
		</div>    
		
	</div>
</div>
{/foreach}