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

<div class="p-detail-container p-detail">
    {if (int)$aItem.view_id == 1}
    {template file='core.block.pending-item-action'}
    {/if}
    <div class="p-detail-top-content">
        <div class="p-fevent-detail-silder-container">
            <ul id="p-fevent-detail-slider" class="p-fevent-detail-slider owl-carousel dont-unbind-children">
                {if count($aImages) > 0}
					{foreach from=$aImages name=images key=iKey item=aImage}
						<li class="item">
							<div class="item-media">
								<span style="background-image: url('{img return_url=true server_id=$aImage.server_id title=$aEvent.title path='event.url_image' file=$aImage.image_path}')"></span>
							</div>
						</li>
					{/foreach}
			    {else}
                    <li class="item">
                        <div class="item-media">
                            <span style="background-image: url('{$defaultImage}')"></span>
                        </div>
                    </li>
			    {/if}
            </ul>
			<div class="p-fevent-detail-label-status">
				{if $aItem.d_type == 'past'}
				<span class="p-label-status solid danger">{_p var='end'}</span>
				{elseif $aItem.d_type == 'ongoing'}
				<span class="p-label-status solid success">{_p var='ongoing'}</span>
				{elseif $aItem.d_type == 'upcoming'}
				<span class="p-label-status solid warning ">{_p var='upcoming'}</span>
				{/if}
			</div>
		</div>
	</div>
	<div class="p-detail-main-content">
		<div class="p-fevent-main-content-bg">
			<h1 class="p-detail-header-page-title header-page-title item-title {if isset($aTitleLabel.total_label) && $aTitleLabel.total_label > 0}header-has-label-{$aTitleLabel.total_label}{/if}">
                <a href="{permalink module='fevent' id=$aItem.event_id title=$aItem.title}" class="ajax_link">
                    <span>{$aItem.title|clean}</span>
                </a>
	            <div class="p-type-id-icon">
                    {if $aItem.view_id == 1}
	                <div class="sticky-label-icon sticky-pending-icon">
	                    <span class="flag-style-arrow"></span>
	                    <i class="ico ico-clock-o"></i>
	                </div>
                    {/if}
                    {if $aItem.is_sponsor == 1}
	                <div class="sticky-label-icon sticky-sponsored-icon">
	                    <span class="flag-style-arrow"></span>
	                    <i class="ico ico-sponsor"></i>
	                </div>
                    {/if}
                    {if $aItem.is_featured == 1}
	                <div class="sticky-label-icon sticky-featured-icon">
	                    <span class="flag-style-arrow"></span>
	                    <i class="ico ico-diamond"></i>
	                </div>
                    {/if}
	            </div>
	        </h1>
	        <div class="p-fevent-detail-info-wrapper">
	        	<div class="p-fevent-timer-component">
	                <span class="item-month">{$aItem.start_time_short_month}</span>
	                <span class="item-date">{$aItem.start_time_short_day}</span>
	                <span class="item-time">{$aItem.start_time_phrase_stamp}</span>
	            </div>
	            <div class="p-fevent-detail-info-inner">
	            	<div class="p-detail-author-wrapper">
			            <div class="p-detail-author-image">{img user=$aItem suffix='_50_square'}</div>
			            <div class="p-detail-author-info">
			                <span class="item-author"><span class="item-text-label">{_p var='by'}</span> {$aItem|user:'':'':50:'':'author'}</span>
                            {if $repeat}
			                    <span class="item-time">{_p var='repeat'}: {$repeat}</span>
                            {/if}
                            {if $aItem.has_ticket}
			                    <span class="item-info">
									{_p var='ticket'}: <span class="item-price">
									{if $aItem.ticket_type == 'free'}{_p var='free'}{else}{$aItem.ticket_price}{/if}
								</span>{if $aItem.ticket_url} <a href="{$aItem.ticket_url}" target="_blank" class="no_ajax_link">
										({_p var='get_ticket'})
									</a>{/if}
								</span>
                            {/if}
			            </div>
			            <div class="p-fevent-detail-option-manage-wrapper">
				            <div class="p-detail-option-manage">
								{if Phpfox::isModule('share')}
				            	<div class="mr-1">
				            		<a href="javascript:void(0);" class="p-option-button"
									   onclick="tb_show('{_p var='share' phpfox_squote=true}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=feed&amp;url={$shareButton.feed_link}&amp;title={$shareButton.feed_title}&amp;feed_id={$aItem.event_id}&amp;sharemodule=fevent')); return false;">
										<i class="ico ico-share-o"></i>
									</a>
				            	</div>
								{/if}
								{if $aEvent.can_edit_event
								|| ($aEvent.view_id == 0 && $aEvent.can_edit_event)
								|| $aEvent.can_delete_event
								}
				                <div class="dropdown">
				                    <a data-toggle="dropdown" class="p-option-button"><i class="ico ico-gear-o"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        {template file='fevent.block.action-link'}
                                    </ul>
				                </div>
								{/if}
				            </div>
							{if $bIsGapi}
			                <div class="item-action-calendar">
			                	<a href="javascript:void(0);" onclick="show_glogin();">
									<i class="ico ico-plus"></i>
									{_p var='add_to_google_calendar'}
								</a>
			                </div>
							{/if}
			            </div>
			        </div>
			        <div class="p-fevent-action-statistic-wrapper">
			        	<div class="p-detail-action-list js_event_rsvp">
                            {module name='fevent.rsvp'}
			            </div>
			            <div class="p-detail-statistic-list">
							{if $aItem.total_guest}
			                	<span class="item-statistic">
									{if $aItem.total_guest == 1}
										{$aItem.total_guest} {_p var='fevent.person'}
									{else}
										{$aItem.total_guest} {_p var='fevent.people'}
									{/if}
								</span>
							{/if}
							{if $aItem.total_view}
								<span class="item-statistic">
									{if $aItem.total_view == 1}
										{_p var='one_view'}
									{else}
										{_p var='number_views' number=$aItem.total_view}
									{/if}
								</span>
							{/if}
			            </div>
			        </div>
	            </div>
	        </div>
	    </div>
		{if $aItem.d_type == 'upcoming' && $isTimeToShowCountDown}
	    <div class="p-fevent-detail-label-count-container">
	    	<div class="item-outer">
	    		<div class="item-icon">
	    			<svg xmlns:x="http://ns.adobe.com/Extensibility/1.0/" xmlns:i="http://ns.adobe.com/AdobeIllustrator/10.0/" xmlns:graph="http://ns.adobe.com/Graphs/1.0/" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
						<switch>
							<foreignObject requiredExtensions="http://ns.adobe.com/AdobeIllustrator/10.0/" x="0" y="0" width="1" height="1">
								<i:pgfRef xlink:href="#adobe_illustrator_pgf">
								</i:pgfRef>
							</foreignObject>
							<g i:extraneous="self">
								<g>
									<path d="M15.4,12.6c0.3-0.1,1-0.3,1.3-1.2l0.5-1.5l0,0l0.1,0h0c0.2,0,0.3-0.1,0.4-0.3l0.3-0.9c0-0.1,0-0.2,0-0.3     c0-0.1-0.1-0.2-0.2-0.2l-7.6-2.6l-0.1,0h0c-0.2,0-0.3,0.1-0.4,0.3L9.4,6.8c0,0.1,0,0.2,0,0.3c0,0.1,0.1,0.2,0.2,0.2l0,0L9.2,8.9     c-0.3,0.9-0.1,1.2,0.3,1.7l1.4,1.6l-0.1,0.3c-0.6,0.1-1.9,0.6-2.1,0.6c-0.3,0.1-1,0.3-1.3,1.2l-0.4,1.3l0,0l-0.1,0h0     c-0.2,0-0.3,0.1-0.4,0.3L6,16.8c0,0.1,0,0.2,0,0.3c0,0.1,0.1,0.2,0.2,0.2l7.6,2.6l0.1,0h0c0.2,0,0.3-0.1,0.4-0.3l0.3-0.9     c0.1-0.2,0-0.4-0.3-0.5l0,0l0.5-1.3c0.3-0.9,0.1-1.2-0.3-1.7l-1.3-1.7l0.1-0.3C13.9,13,15.2,12.7,15.4,12.6z M8,16l0.4-1.3     c0.1-0.4,0.3-0.4,0.5-0.5c0.1,0,1.3-0.5,2.4-0.7c0.2,0,0.4-0.2,0.4-0.4l0.3-0.9c0.1-0.2,0-0.4-0.1-0.5l-1.6-1.8     c-0.1-0.1-0.2-0.2-0.2-0.2c0,0,0-0.1,0.1-0.4l0.5-1.6l5.3,1.8L15.6,11c-0.1,0.4-0.3,0.4-0.5,0.5c-0.1,0-1.2,0.3-2.4,0.6     c-0.2,0-0.4,0.2-0.4,0.4L12,13.4c-0.1,0.2,0,0.4,0.1,0.5l1.5,2c0.1,0.1,0.2,0.2,0.2,0.2c0,0,0,0.1-0.1,0.4l-0.5,1.3L8,16z"/>
									<path d="M17.1,20.5c-0.2,0-0.4,0-0.5,0.1c-0.2,0.1-0.3,0.2-0.5,0.3c0,0-0.1,0-0.1,0l-0.1,0c-0.7,0.3-1.5,0.6-2.3,0.7     c-0.2,0-0.4,0.1-0.6,0.1c-0.3,0-0.7,0.1-1,0.1c-3.9,0-7.4-2.5-8.8-6.2l0-0.1c-0.3-0.9-0.5-1.9-0.5-2.9c0-1,0.2-2,0.5-3l0.1-0.2     c1.1-2.9,3.5-5,6.5-5.7l0.1,0c0.6-0.1,1.2-0.2,1.8-0.2l0,0.8c0,0.3,0.2,0.5,0.4,0.5c0.1,0,0.2-0.1,0.4-0.2l2.1-1.7     c0.2-0.1,0.2-0.3,0.2-0.5c0-0.2-0.1-0.4-0.2-0.5l-2.1-1.7C12.4,0,12.2,0,12.1,0c-0.1,0-0.2,0-0.3,0.1c-0.1,0.1-0.1,0.2-0.1,0.4     l0,0.8C8.8,1.3,6,2.5,3.9,4.6C3.7,4.8,3.5,5,3.2,5.3c-1.7,2-2.7,4.4-2.7,7.1l0,0.3c0,0.1,0,0.3,0,0.5c0.1,2.4,0.9,4.6,2.4,6.5     c0.1,0.2,0.3,0.4,0.5,0.6c0.2,0.2,0.3,0.3,0.5,0.5c1.7,1.7,3.8,2.8,6.2,3.2c0.6,0.1,1.3,0.2,1.9,0.2c1.7,0,3.3-0.4,4.9-1.1     c0,0,0.1,0,0.1,0c0,0,0.1,0,0.1,0c0.2-0.1,0.4-0.2,0.6-0.3c0.3-0.1,0.4-0.4,0.5-0.7c0.1-0.3,0-0.6-0.1-0.8     C17.9,20.7,17.5,20.5,17.1,20.5z"/>
									<path d="M19.4,7.1c0.1,0.1,0.2,0.3,0.3,0.5C19.9,7.8,20.3,8,20.6,8c0.2,0,0.4-0.1,0.6-0.2c0.5-0.3,0.6-1,0.3-1.5     c-0.1-0.2-0.3-0.4-0.4-0.6c-0.2-0.3-0.5-0.4-0.9-0.4c-0.2,0-0.5,0.1-0.7,0.2c-0.2,0.2-0.4,0.4-0.4,0.7     C19.1,6.5,19.2,6.8,19.4,7.1z"/>
									<path d="M20.9,9c-0.1,0.3-0.2,0.6-0.1,0.8c0.1,0.2,0.1,0.4,0.2,0.5c0.1,0.5,0.6,0.8,1.1,0.8c0.1,0,0.2,0,0.3,0     c0.3-0.1,0.5-0.3,0.7-0.5c0.2-0.3,0.2-0.5,0.1-0.8C23.1,9.6,23,9.4,23,9.2c-0.1-0.5-0.6-0.8-1.1-0.8c-0.1,0-0.2,0-0.3,0.1     C21.3,8.5,21,8.7,20.9,9z"/>
									<path d="M19.9,18.3c-0.3,0-0.6,0.1-0.8,0.4c-0.1,0.1-0.3,0.3-0.4,0.4c-0.2,0.2-0.3,0.5-0.3,0.8c0,0.3,0.1,0.6,0.3,0.8     c0.2,0.2,0.5,0.3,0.8,0.3c0.3,0,0.6-0.1,0.8-0.3c0.2-0.2,0.3-0.3,0.5-0.5c0.2-0.2,0.3-0.5,0.3-0.8c0-0.3-0.2-0.6-0.4-0.7     C20.4,18.4,20.1,18.3,19.9,18.3z"/>
									<path d="M22.4,11.8c-0.6,0-1.1,0.5-1.1,1.1c0,0.2,0,0.4,0,0.6c0,0.3,0.1,0.6,0.3,0.8c0.2,0.2,0.5,0.4,0.7,0.4l0.1,0     c0.6,0,1-0.4,1.1-1c0-0.2,0-0.5,0-0.7C23.5,12.4,23,11.9,22.4,11.8z"/>
									<path d="M22.1,15.3c-0.1,0-0.3-0.1-0.4-0.1c-0.5,0-0.9,0.3-1,0.7c-0.1,0.2-0.1,0.3-0.2,0.5c-0.3,0.5,0,1.2,0.5,1.5     c0.1,0.1,0.3,0.1,0.5,0.1c0.4,0,0.8-0.2,1-0.6c0.1-0.2,0.2-0.4,0.3-0.6c0.1-0.3,0.1-0.6,0-0.8C22.6,15.7,22.4,15.5,22.1,15.3z"/>
									<path d="M17.1,4.9c0.2,0.1,0.3,0.2,0.5,0.3c0.2,0.1,0.4,0.2,0.7,0.2c0.3,0,0.7-0.2,0.9-0.4c0.2-0.2,0.2-0.5,0.2-0.8     c0-0.3-0.2-0.5-0.4-0.7c-0.2-0.1-0.4-0.3-0.6-0.4c-0.2-0.1-0.4-0.2-0.6-0.2c-0.4,0-0.7,0.2-0.9,0.5c-0.2,0.2-0.2,0.5-0.2,0.8     C16.6,4.5,16.8,4.8,17.1,4.9z"/>
								</g>
							</g>
						</switch>
					</svg>
	    		</div>

	    		<div class="item-count">
	    			{_p var='start_in_days_hours' days=$days_diff hours=$hours_diff}
	    		</div>
	    	</div>
	    </div>
		{/if}
	    <div class="p-detail-content-wrapper p-fevent-detail-content-info">
	    	<div class="item-map-info">
				<div class="item-time">
					<span class="ico ico-calendar-o"></span>
					<div class="item-info">
						{$aEvent.detail_start_time} - {$aEvent.detail_end_time}
					</div>
				</div>
				<div class="item-location">
					<span class="ico ico-checkin-o"></span>
		            <div class="item-info">
		            	{$locationText}
		            </div>
				</div>
				{if $aEvent.lat && $aEvent.lng && $aEvent.lat > 0 && $aEvent.lng > 0}
				<div class="item-map-container">
					<div class="item-map-action">
						<div class="item-map-collapse">
							<a href="javascript:void(0);" onclick="$('#js_event_map_container').slideToggle();" class="fevent-show-map">{_p var='show_map'}</a>
						</div>
						<div class="item-map-viewmore">
							<a href="//maps.google.com/?q={$aEvent.gmap.latitude},{$aEvent.gmap.longitude}" target="_blank">
								{_p var='view_on_google_map'}
							</a>
						</div>
					</div>
					<div class="item-location-map mt-2" id="js_event_map_container" style="width: 100%; height: 400px; display: none;">
						<div id="fevent_detail_map" class="item-map-img fevent-map" style="height: 100%;"></div>
					</div>
				</div>
				{/if}
			</div>
			<div class="p-fevent-detail-member-container">
		    	<div class="item-wrapper-outer">
					{if $aEvent.view_id == 0}
			    	<div class="p-fevent-detail-member" data-event-id="{$aEvent.event_id}">
						<div class="item-tab-member">
							<a href="javascript:void(0);" data-tab="attending" class="fevent_detail_guest_list">
								<span class="item-number">{$iAttendingCnt}</span>
								<span class="item-text">{_p var='attending'}</span>
							</a>
						</div>
						<div class="item-tab-member">
							<a href="javascript:void(0);" data-tab="maybe" class="fevent_detail_guest_list">
								<span class="item-number">{$iMaybeCnt}</span>
								<span class="item-text">{_p var='maybe_attending'}</span>
							</a>
						</div>
						<div class="item-tab-member">
							<a href="javascript:void(0);" data-tab="awaiting" class="fevent_detail_guest_list">
								<span class="item-number">{$iAwaitingCnt}</span>
								<span class="item-text">{_p var='awaiting_reply'}</span>
							</a>
						</div>
					</div>
					{/if}
					<div class="p-fevent-member-list-component">
						{if !empty($aEvent.attending_statistic.people)}
							{foreach from=$aEvent.attending_statistic.people item=attending_person}
								<div class="item-member">
									{img user=$attending_person suffix='_200_square'}
								</div>
							{/foreach}
						{/if}
						{if !empty($aEvent.attending_statistic.other_people)}
							<div class="item-more">
								<span>+{$aEvent.attending_statistic.other_people}</span>
							</div>
						{/if}
		            </div>
		        </div>
		    </div>
			{if ($aEvent.description|parse) || (isset($aEvent.categories) && null != $aEvent.categories && count($aEvent.categories) > 0)}
			<div class="p-collapse-content js_p_collapse_content">
				{$aEvent.description|parse}
				{if isset($aEvent.categories) && null != $aEvent.categories && count($aEvent.categories) > 0}
				<div class="p-detail-type-info">
                    <div class="p-type-info-item">
                        <div class="p-category">
                            <span class="p-item-label">{_p var='fevent.category'}:</span>
                            <div class="p-item-content">{$aEvent.categories|category_display}</div>
                        </div>
                    </div>
                </div>
				{/if}
			</div>
			{/if}
			{if count($aEvent.custom) }
				<div class="ynfevent-detail-custom_fields ">
					<h4 class="ynfevent-detail-title"><span>{_p var='fevent.custom_fields'}<span></h4>

					<div class="ynfevent-detail-description ync-detail-custom-fields-container">
						{foreach from=$aEvent.custom item=aCustom}
							<div class="ynfevent-detail-description-item ync-detail-custom-fields-item">
								<div class="ync-detail-customfield-title">{_p var=$aCustom.phrase_var_name}</div>
								<div class="ync-detail-customfield-info">{$aCustom.value}</div>
							</div>
						{/foreach}
					</div>
				</div>
			{/if}
	    </div>

	    <div class="p-detail-bottom-content">
	    	<div class="p-detail-addthis-wrapper">
	    		<div class="p-detail-addthis">
	    			{addthis url=$aEvent.bookmark title=$aEvent.title description=$sShareDescription}
	    		</div>
	    	</div>
	    	<div class="item-detail-feedcomment p-detail-feedcomment">
				{module name='feed.comment'}
			</div>
			{unset var=$sFeedType}
	    </div>
	</div>
</div>

<div class="marvic_separator clearfix"></div>

<script src="//maps.googleapis.com/maps/api/js?v=3.exp&key={$apiKey}&sensor=false&language=en&libraries=places"></script>

{plugin call='fevent.template_default_controller_view_extra_info'}

<script type="text/javascript">
	function show_glogin() {l}
		tb_remove();
		tb_show("{_p var='fevent.google_calendar'}", $.ajaxBox("fevent.glogin", "height=300;width=350&id=" + {$aEvent.event_id}));
	{r};

	$Behavior.initFEventDetail = function() {l}
		fevent.showDetailMapView({$aEvent.event_id});
		fevent.bindGuestListButtons();
	{r};

{if $gcalendar == 1}
	{literal}
		var flag = false;
		$Behavior.feLoadDetailEvent = function(){
			if(flag === false)
			{
				$.ajaxCall('fevent.gnotif','type=success');
				flag = true;
			}
		};
	{/literal}
{/if}
</script>
