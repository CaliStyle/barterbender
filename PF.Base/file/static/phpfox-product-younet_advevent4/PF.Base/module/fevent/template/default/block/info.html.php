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

<div class="fevent-detail-page ync-detail">
    {if $aEvent.view_id == 1}
        {template file='core.block.pending-item-action'}
    {/if}
	<div class="ync-detail-info">
	    <div class="item-info-image">{img user=$aEvent suffix='_50_square'}</div>
	    <div class="item-info-main">
	        <span class="item-author">{_p var='by_user' full_name=$aEvent|user:'':'':50:'':'author'}</span>
		    <span class="item-time">{_p var='on'} {$aEvent.time_stamp|convert_time:'core.global_update_time'}</span>
	    </div>
	    {if $aEvent.can_edit_event
			|| ($aEvent.view_id == 0 && $aEvent.can_edit_event)
			|| $aEvent.can_delete_event
		}
	        <div class="ync-detail-bar">
	            <div class="item_bar_action_holder">
	                <span data-toggle="dropdown" class="ync-option-button item_bar_action dropdown-toggle" aria-haspopup="true" aria-expanded="true"><i class="ico ico-gear-o"></i></span>
	                <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aItem.event_id}">
	                    {template file='fevent.block.action-link'}
	                </ul>
	            </div>
	        </div>
	    {/if}
	</div>
	<div class="js_event_rsvp item-choice mt-2 pb-1">{module name='fevent.rsvp'}</div>
	<div class="ync-detail-content{if (int)$aEvent.isrepeat >= 0 && (int)$aEvent.after_number_event > 0} fevent-repeat{/if}">
		{if count($aImages) > 0}
			<ul id="fevent-detail-slider" class="fevent-detail-slider owl-carousel dont-unbind-children mt-2">
				{foreach from=$aImages name=images key=iKey item=aImage}
					<li class="item">
						<div class="fevent-detail-slider__inner">
							{img server_id=$aImage.server_id title=$aEvent.title path='event.url_image' file=$aImage.image_path}
						</div>
					</li>
				{/foreach}
			</ul>
		{/if}
		<time class="fevent-detail-page__time d-block mt-2">
			<p class="mb-0 fz-12 text-gray-dark"><i class="ico ico-clock-o mr-1"></i>{_p var='fevent.s_st'}: {$aEvent.detail_start_time}</p>
			<p class="mb-0 fz-12 text-gray-dark mt-1"><i class="ico ico-sandclock-end-o mr-1"></i>{_p var='fevent.s_et'}: {$aEvent.detail_end_time}</p>
		</time>

		{if $aEvent.lat !=0 && $aEvent.lng !=0}
			<div class="fevent-detail-page__location mt-1 mb-2 fz-12 text-gray-dark">
				<input type="hidden" id="eventGlat" value="{$aEvent.lat}" />
				<input type="hidden" id="eventGlong" value="{$aEvent.lng}" />
				<i class="ico ico-checkin-o"></i>
	            {$$aEventAddress}
	            {if !empty($aEvent.event_country_iso)}
	                {if !empty($aEvent.country_child_id)}
	                    {$aEvent.country_child_id|location_child},&nbsp;
	                {/if}
	                ,&nbsp;{$aEvent.event_country_iso|location}
	                <input type="hidden" id="eventCountry" value="{$aEvent.country_iso|location}" />
	            {/if}
	        </div>
	        <div id="fevent_detail_map" class="fevent-map mt-2 mb-2" style="width: 100%; height: 420px;" ></div>
		{/if}

		{if $aEvent.description|parse != ''}
			<div class="ynfevent-detail-description">
				{if $iLengthDescription > 1073}
					<div id="ynfeDescription">
						<span class="js_view_more_parent">
							<span id="ynfeDescriptionLess" class="js_view_more_part item_view_content">
								{$aEvent.description|parse|shorten:'350':'...'|split:350}
								<div class="item_view_more text-center">
									<a onclick="$('#ynfeDescriptionLess').hide(); $('#ynfeDescriptionMore').show(); return false;" href="#">{_p var='fevent.view_more'}</a>
								</div>
							</span>
						</span>
					</div>		
				{else}
					<div id="ynfeDescription">
						<span class="js_view_more_parent">
							<span id="ynfeDescriptionLess" class="js_view_more_part item_view_content">
								{$aEvent.description|parse}
							</span>
						</span>
					</div>	
				{/if}

				<div id="ynfeDescriptionMore" style="display:none;" class="js_view_more_full item_view_content">
					{$aEvent.description|parse}
					<div class="item_view_more text-center">
						<a onclick="$('#ynfeDescriptionMore').hide(); $('#ynfeDescriptionLess').show(); return false;" href="#">{_p var='fevent.view_less'}</a>
					</div>
				</div>

				<div>
					{if $aEvent.total_attachment}
						{module name='attachment.list' sType=fevent iItemId=$aEvent.event_id}
					{/if}
				</div>		
			</div>		
		{/if}

		{if (count ($aEvent.custom)) }
		    <div class="ynfevent-detail-custom_fields ">
		    	<h3 class="ynfevent-detail-title"><span>{_p var='fevent.custom_fields'}<span></h3>

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
		{if (int)$aEvent.isrepeat >= 0 && (int)$aEvent.after_number_event > 0}
			<div class="fevent-detail-recurring_events pt-3">
				<h3 class="fevent-detail-title text-gray-dark">
					{_p var='fevent.recurring_events'}
					<p class="fz-14 mb-0 mt-1">{$sPhraseRecurrent}</p>
				</h3>
				
				<div id='ynfevent_recurrent_event' class="ynfevent_recurrent_event">
                    {if $iCount > 3}
                        <span id="ynfeRecurringLess" class="js_view_more_part">
                            {foreach from=$aItems item=aItem}
                                <div class="ynfevent-detail-recurring-item">
                                    <div class="ynfevent-detail-recurring-title">
                                        <a href="{$aItem.url}">{$aItem.title}</a>
                                    </div>
                                    <div class="ynfevent-detail-recurring-time">
                                        {$aItem.recuring_time}
                                    </div>
                                    <div class="ynfevent-detail-recurring-attend">
                                        {$aItem.number_attending} {_p var='fevent.attending'}
                                    </div>
                                    <div class="ynfevent-detail-recurring-setting">{if $aItem.can_edit_event|| ($aItem.view_id == 0 && $aItem.can_edit_event)|| $aItem.can_delete_event
                                        }<div class="item_bar"><div class="item_bar_action_holder"><a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='fevent.actions'}</span><i class="ico ico-gear-o"></i></a><ul class="dropdown-menu dropdown-menu-right">{template file='fevent.block.action-link'}</ul></div>
                                        </div>{/if}</div>
                                </div>
                            {/foreach}
                        </span>
                        <div class="ynfevent-detail-recurring-more text-center">
                            <a onclick="$('#ynfeRecurringLess').hide(); $('#ynfeRecurringMore').show(); $('.ynfevent-detail-recurring-more').hide(); return false;" href="#">{_p var='fevent.view_more'}</a>
                        </div>
                    {else}
                        {foreach from=$aItems item=aItem}
                            <div class="ynfevent-detail-recurring-item">
                                <div class="ynfevent-detail-recurring-title">
                                    <a href="{$aItem.url}">{$aItem.title}</a>
                                </div>
                                <div class="ynfevent-detail-recurring-time">
                                    {$aItem.recuring_time}
                                </div>
                                <div class="ynfevent-detail-recurring-attend">
                                    {$aItem.number_attending} {_p var='fevent.attending'}
                                </div>
                                <div class="ynfevent-detail-recurring-setting">{if $aItem.can_edit_event|| ($aItem.view_id == 0 && $aItem.can_edit_event)|| $aItem.can_delete_event
                                    }<div class="item_bar"><div class="item_bar_action_holder"><a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='fevent.actions'}</span><i class="ico ico-gear-o"></i></a><ul class="dropdown-menu dropdown-menu-right">{template file='fevent.block.action-link'}</ul></div>
                                    </div>{/if}</div>
                            </div>
                        {/foreach}
                    {/if}
                    <div id="ynfeRecurringMore" style="display:none;" class="js_view_more_full">
                        {foreach from=$aAllItems item=aItem}
                            <div class="ynfevent-detail-recurring-item">
                                <div class="ynfevent-detail-recurring-title">
                                    <a href="{$aItem.url}">{$aItem.title}</a>
                                </div>
                                <div class="ynfevent-detail-recurring-time">
                                    {$aItem.recuring_time}
                                </div>
                                <div class="ynfevent-detail-recurring-attend">
                                    {$aItem.number_attending} {_p var='fevent.attending'}
                                </div>
                                <div class="ynfevent-detail-recurring-setting">{if $aItem.can_edit_event|| ($aItem.view_id == 0 && $aItem.can_edit_event)|| $aItem.can_delete_event
                                    }<div class="item_bar"><div class="item_bar_action_holder"><a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='fevent.actions'}</span><i class="ico ico-gear-o"></i></a><ul class="dropdown-menu dropdown-menu-right">{template file='fevent.block.action-link'}</ul></div>
                                    </div>{/if}</div>
                            </div>
                        {/foreach}
                    </div>
				</div>
			</div>
		{/if}
	</div>

	{if isset($aEvent.categories) && null != $aEvent.categories && count($aEvent.categories) > 0}
		<div class="ync-item-info-group">
			<div class="ync-item-info">
				<span class="ync-item-label">{_p var='fevent.category'}:</span>
				<div class="ync-item-content">{$aEvent.categories|category_display}</div>
			</div>
		</div>
	{/if}
	<div class="ync-addthis">{addthis url=$aEvent.bookmark title=$aEvent.title description=$sShareDescription}</div>

	<div class="ynfevent-detail-feedcomment ync-detail-comment">
		{module name='feed.comment'}
	</div>

    {unset var=$sFeedType}
</div>

<div class="marvic_separator clearfix"></div>

{literal}
	<script type="text/javascript">
		 $Behavior.ynFeventLoadMapDetail = function()
	     {
	     	fevent.showDetailMapView({/literal}{$aEvent.event_id}{literal});
	     };
	</script>
{/literal}