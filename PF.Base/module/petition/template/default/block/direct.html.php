<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{if $aDirect.petition_status != 2}
<div class="petition_image">
    {if $aDirect.petition_status == 3}
        <div class="petition_victory"></div>
    {else if $aDirect.petition_status == 1}
        <div class="petition_closed"></div>
    {/if}
{else}
<div class="petition_image direct_image">
{/if}
    <a href="{permalink module='petition' id=$aDirect.petition_id title=$aDirect.title}" 
	title="{$aDirect.title|clean}">
	{if (!empty($aDirect.image_path))}
		{img server_id=$aDirect.server_id path='core.url_pic' 
		file=$aDirect.image_path 
		suffix='_300' max_width=235 max_height=300 class='photo_holder'}
	{else}	
		<img 
			src="{$corepath}module/petition/static/image/no_photo.png" />	
	{/if}	
	
	</a>
</div>
<div class="petition_detail">
    <a class="link"  href="{permalink module='petition' id=$aDirect.petition_id title=$aDirect.title}" class="row_sub_link" title="{$aDirect.title|clean}">{$aDirect.title|clean|shorten:50:'...'|split:20}</a>
    <div class="extra_info">{phrase var='petition.created_by'} {$aDirect|user} {if isset($aDirect.category)}{phrase var='petition.in'} <a href="{$aDirect.category.link}">{$aDirect.category.name}</a>{/if}

        <br/>{phrase var='petition.target'}: {$aDirect.target|shorten:75:'...'}
        <br/>{phrase var='petition.petition_goal'}: {$aDirect.petition_goal|shorten:75:'...'}

        <br/><span class="total_sign">{$aDirect.total_sign}</span>
        <i class="fa fa-pencil"></i>
        &nbsp; <i class="fa fa-thumbs-up"></i> {$aDirect.total_like} 
        &nbsp; <i class="fa fa-eye"></i> {$aDirect.total_view} 
    </div>
    <div class="item_content item_view_content">
        {$aDirect.short_description|shorten:200:'...'}
    </div>
</div>
{if $aDirect.petition_status == 2}
{if $aDirect.can_sign == 1}
<div id="sign_now_{$aDirect.petition_id}">
   <div class="sign_now">
      <div class="sign_now_r">
          <a href="#" class="btn btn-success btn-sm" onclick="$Core.box('petition.sign',400,'&id={$aDirect.petition_id}'); return false;">{phrase var='petition.sign_now'}</a>
      </div>
  </div>
</div>
{/if}
<div id="signed_{$aDirect.petition_id}" {if $aDirect.can_sign != 2} style="display: none" {/if}>
    <div class="signed">

               {phrase var='petition.signed'}

    </div>
</div>
{/if}