<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
{foreach from=$aMostLiked item=aMLEvent name=Mostliked}
	<div class="ynfevent-block-item">
		<div class="ynfevent-block-item-top">
			<a href="{permalink module='fevent' id=$aMLEvent.event_id title=$aMLEvent.title}" title="{$aMLEvent.title|clean}" class="ynfevent-block-item-photo" style="background-image: url('{$aMLEvent.image_path}');"></a>
		</div>
		<div class="ynfevent-block-item-top-info">
			<a href="{permalink module='fevent' id=$aMLEvent.event_id title=$aMLEvent.title}" class="ynfevent-block-item-title" title="{$aMLEvent.title|clean}">{$aMLEvent.title|clean|shorten:50:'...'|split:20}</a>
			<div class="space-left text-gray-dark ynfevent-block-item-owner mt-1 fz-12"><i class="ico ico-user1-o"></i>{_p var='fevent.by'} {$aMLEvent|user}</div>
			<time class="mt-h1 d-block space-left">
				<i class="ico ico-calendar-star-o text-gray-dark"></i><span class="fw-bold fz-12">{$aMLEvent.d_start_time}&nbsp;-&nbsp;{$aMLEvent.short_start_time}</span>
			</time>
		</div>
	</div>
{/foreach}


