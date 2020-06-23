<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $aStats.type == 'full'}
    <table width="100%">
        <tr>
            <td width="40%">{phrase var='petition.victory'}:</td>
            <td>{$aStats.victories} {if $aStats.victories == 1} {phrase var='petition.petition'} {elseif $aStats.victories > 1 || $aStats.victories == 0} {phrase var='petition.petitions'} {/if} </td>
        </tr>
        <tr>
            <td>{phrase var='petition.on_going'}:</td>
            <td>{$aStats.ongoing} {if $aStats.ongoing == 1} {phrase var='petition.petition'} {elseif $aStats.ongoing > 1 || $aStats.ongoing == 0} {phrase var='petition.petitions'} {/if} </td>
        </tr>
        <tr>
            <td>{phrase var='petition.closed'}:</td>
            <td>{$aStats.closed} {if $aStats.closed == 1} {phrase var='petition.petition'} {elseif $aStats.closed > 1 || $aStats.closed == 0} {phrase var='petition.petitions'} {/if} </td>
        </tr>
    </table>
{elseif $aStats.type == 'item'}
    <div class="row1 row_first">
	<div style="float:left; width:50px;" class="t_center">
		<a href="{permalink module='petition' id=$aStats.petition_id title=$aStats.title}" title="{$aStats.title|clean}">{img server_id=$aStats.server_id path='core.url_pic' file=$aStats.image_path suffix='_50' max_width=50 max_height=50}</a>
	</div>
	<div class="item_view_content" style="margin-left:60px;">
		<a href="{permalink module='petition' id=$aStats.petition_id title=$aStats.title}" class="row_sub_link" title="{$aStats.title|clean}">{$aStats.title|clean|shorten:50:'...'|split:20}</a>		
                <div class="extra_info">                
			{$aStats.short_description|shorten:50:'...'}
		</div>
		<div class="extra_info">
			{phrase var='petition.total_sign_signatures' total_sign=$aStats.total_sign}
		</div>
		<div class="extra_info">                
			{phrase var='petition.total_like_likes' total_like=$aStats.total_like}
		</div>                
                <div class="extra_info">                
			{phrase var='petition.total_view_views' total_view=$aStats.total_view}
		</div>		
	</div>
	<div class="extra_info_link">
		{phrase var='petition.created_by'}: {$aStats|user}
	</div>
	<div class="extra_info_link">
		{phrase var='petition.target'}: {$aStats.target}
	</div>
	<div class="extra_info_link">
		{phrase var='petition.petition_goal'}: {$aStats.petition_goal}
	</div>	
    </div>
{/if}