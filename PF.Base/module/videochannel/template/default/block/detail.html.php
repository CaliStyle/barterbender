<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="video_info_box">
	<div class="video_info_box_content">
		<ul class="video_info_box_list">
			<li class="full_name first">{$aVideo|user}</li>
			{foreach from=$aVideoDetails key=sKey item=sValue}
			<li>{$sValue} ({$sKey})</li>
			{/foreach}
			<li class="video_info_view">{if $aVideo.total_view == 0}1{else}{$aVideo.total_view|number_format}{/if}</li>
		</ul>

		<div class="video_info_box_text">
			{$aVideo.text|parse}
		</div>

		<div class="video_info_box_extra" style="display: none">
			{if count($aVideo.breadcrumb)}
			<div class="table form-group">
				<div class="table_left">
					{phrase var='videochannel.category'}:
				</div>
				<div class="table_right js_allow_video_click">
				{foreach from=$aVideo.breadcrumb name=breadcrumbs item=aBredcrumb}
                    {if $phpfox.iteration.breadcrumbs != 1}<div class="p_2">&raquo; {/if}
                        <a href="{$aBredcrumb.1}">{$aBredcrumb.0}</a>
					{if $phpfox.iteration.breadcrumbs != 1}</div>{/if}
				{/foreach}
				</div>
			</div>
			{/if}

			{if !empty($aVideo.tag_list)}
			<div class="table form-group">
				<div class="table_left">
					{phrase var='tag.topics'}:
				</div>
				<div class="table_right js_allow_video_click">
				{foreach from=$aVideo.tag_list name=tags item=aTag}
					{if $phpfox.iteration.tags != 1}, {/if}<a href="{if isset($sGroup) && $sGroup !=''}{url link='group.'$sGroup'.videochannel.tag.'$aTag.tag_url''}{else}{url link='videochannel.tag.'$aTag.tag_url''}{/if}">{$aTag.tag_text}</a>
				{/foreach}
				</div>
			</div>
			{/if}
		</div>
	</div>
	<a href="#" class="video_info_toggle">
		<span class="js_info_toggle_show_more">{phrase var='videochannel.show_more'} <i class="fa fa-fw">&#xf103</i></span>
		<span class="js_info_toggle_show_less">{phrase var='videochannel.show_less'} <i class="fa fa-fw">&#xf102</i></span>
	</a>
</div>