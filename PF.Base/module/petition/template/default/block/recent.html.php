<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="viewed_listing">
{foreach from=$aRecent key=iKey item=aPetition name=Recent}
<div class="view_content_listing{if $iKey == count($aRecent)-1} last {/if}">
        <div class="row_listing_image">
            <a href="{permalink module='petition' id=$aPetition.petition_id title=$aPetition.title}" title="{$aPetition.title|clean}">
			
			{if !empty($aPetition.image_path)}
			{img server_id=$aPetition.server_id path='core.url_pic' file=$aPetition.image_path suffix='_120' max_width=90 max_height=90}
				{else}
				<img
					width="120"	
					src="{$corepath}module/petition/static/image/no_photo_small.png" />	
				{/if}
			</a>
        </div>
        <div class="row_title_info">
            <a class="row_sub_link"  href="{permalink module='petition' id=$aPetition.petition_id title=$aPetition.title}" class="row_sub_link" title="{$aPetition.title|clean}">{$aPetition.title|clean|shorten:20:'...'|split:20}</a>
           <div class="item_view_content">
                {$aPetition.short_description|clean|shorten:45:'...'}
                <br/>
                <div class="extra_info stats">
                    {if $aPetition.is_directsign == 1}
                    <span class="total_sign"><i class="fa fa-pencil"></i> {$aPetition.total_sign}</span>
                    {else}
                    <i class="fa fa-pencil"></i> {$aPetition.total_sign}
                    {/if}
                    &nbsp; 
                    <i class="fa fa-thumbs-up"></i> {$aPetition.total_like}
                    &nbsp;
                    <i class="fa fa-eye"></i> {$aPetition.total_view}
                </div>
            </div>
        </div>
</div>
<div class="clear"></div>
{/foreach}
<div class="clear"></div>
{if $iTotal > 4}
<div class="block_viewmore text-center"><a class="btn btn-success btn-sm" href="{url link='petition' status='0' view='listing' sort='latest'}">{phrase var='petition.view_all'}</a></div>
{/if}
</div>
