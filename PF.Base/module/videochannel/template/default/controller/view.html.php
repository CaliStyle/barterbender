<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="item_view">
    <div class="video-info">
        <div class="video-info-image">{img user=$aVideo suffix='_50_square'}</div>
        <div class="video-info-main">
            <span class="video-author">{_p var='by_user' full_name=$aVideo|user:'':'':50:'':'author'}</span>
            <span class="video-time">{_p var='on'} {$aVideo.time_stamp|convert_time:'core.global_update_time'}</span>
        </div>
    </div>
	<div id="js_video_edit_form_outer" style="display:none;">
		<form method="post" action="#" onsubmit="$(this).ajaxCall('videochannel.viewUpdate'); return false;">
			<div><input type="hidden" name="val[is_inline]" value="true" /></div>
			<div id="js_video_edit_form"></div>
			<div class="table_clear">
				<ul class="table_clear_button">
					<li><input type="submit" value="{phrase var='videochannel.update'}" class="button btn-primary" /></li>
					<li><a href="#" id="js_video_go_advanced" class="button button_off">{phrase var='videochannel.go_advanced_uppercase'}</a></li>
					<li><a href="#" onclick="$('#js_video_edit_form_outer').hide(); $('#js_video_outer_body').show(); return false;" class="button button_off">{phrase var='videochannel.cancel_uppercase'}</a></li>
				</ul>
				<div class="clear"></div>
			</div>
		</form>
	</div>

	<div id="js_video_outer_body">
		{if $aVideo.in_process > 0}
            <div class="message">
                {phrase var='videochannel.video_is_being_processed'}
            </div>
		{else}
            {if $aVideo.view_id == 2}
                {template file='core.block.pending-item-action'}
            {/if}

		{/if}

		{if (($aVideo.user_id == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_edit_own_video')) || Phpfox::getUserParam('videochannel.can_edit_other_video'))
			|| (($aVideo.user_id == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_delete_own_video')) || Phpfox::getUserParam('videochannel.can_delete_other_video'))
		}
            <div class="item_bar">
                <div class="item_bar_action_holder h-5">
                    <a role="button" data-toggle="dropdown" class="item_bar_action">
                        <i id="icon_edit" class="fa fa-cog fa-lg" style="margin:12px; color:#626262; position: absolute;top: 0"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        {template file='videochannel.block.menu'}
                    </ul>
                </div>
            </div>
		{/if}

		<div class="t_center video_container">
            {if $aVideo.is_stream}
                {$aVideo.embed_code}
            {else}
                <div id="js_video_player" style="width:100%; height:auto; margin:auto;{if $aVideo.in_process > 0} display:none;{/if}"></div>
            {/if}
		</div>

		{module name='videochannel.detail'}
        {if $bShowAddThisSection}
            <div class="addthis_share pf_video_addthis">
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={$sAddThisPubId}" data-title="{$aVideo.title|clean}"></script>
                {addthis url=$aVideo.bookmark title=$aVideo.title description=$aVideo.clean_description}
            </div>
        {/if}

        <div id='videochannel_addthis'>

        {if Phpfox::isUser()}
            <div class ="video_favourite_body">
                <div class="video_favourite_display">
                    <ul>
                        <li id='yn_videochannel_favourite'>
                            <a href="#" class="btn btn-sm {if (!$bIsFavourite)}btn-primary{else} btn-danger{/if}" onclick="addToFavourite({$aVideo.video_id}, {if (!$bIsFavourite)} 'favourite' {else} 'unfavourite' {/if});">
                                {if (!$bIsFavourite)}
                                <i class="fa fa-fw">&#xf006</i>
                                {else}
                                <i class="fa fa-fw">&#xf005</i>
                                {/if}
                               <span id="yn_favourite_text">{if (!$bIsFavourite)} {phrase var='videochannel.favourite'} {else} {phrase var='videochannel.unfavourite'} {/if}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        {/if}
         <a href="#" class="btn btn-sm btn-success video_view_embed">{phrase var='videochannel.embed'}</a>
        </div>
		<div {if $aVideo.view_id}style="display:none;" class="js_moderation_on"{/if}>

		<div class="video_rate_body">
			<div class="video_rate_display">
				{module name='rate.display'}
			</div>

			<div class="video_view_embed_holder">
				<input name="#" value="{$aVideo.embed}" type="text" class="form-control" onfocus="this.select();" />
			</div>
		</div>
		{plugin call='videochannel.template_default_controller_view_extra_info'}
		{module name='feed.comment'}
		</div>
	</div>
</div>