<div class="yndirectory-event-list">
	{if count($aEvents) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}

	<div class="yndirectory-content-column3">
		{foreach from=$aEvents key=keyevent item=aItem}
			<div class="yndirectory-event-item">
				<div class="yndirectory-event-item-border">
					<div class="large_item_image ele_relative" {if !empty($aItem.url_photo)}style="background-image:url({$aItem.url_photo})"{/if}>
						<!-- normal/repeat event -->
						{if (int)$aItem.isrepeat >= 0}
							<span class="entype"></span> 
						{/if}	

						<ul class="list_itype">
							{if $aItem.is_featured}
								<li class="itype featured">{phrase var='featured'}</li>
							{/if}
							{if $aItem.is_sponsor}
								<li class="itype sponsored">{phrase var='sponsored'}</li>
							{/if}
							{if $aItem.d_type == 'upcoming'}
								<li class="itype upcoming">{phrase var='type_upcoming'}</li>
							{/if}
							{if $aItem.d_type == 'ongoing'}
								<li class="itype ongoing">{phrase var='type_ongoing'}</li>
							{/if}
							{if $aItem.d_type == 'past'}
								<li class="itype past">{phrase var='type_past'}</li>
							{/if}
						</ul>

						<div class="large_item_info large_hover" onclick="window.location.href='{if Phpfox::getService('directory.helper')->isAdvEvent()}{permalink module='fevent' id=$aItem.event_id title=$aItem.title}{else}{permalink module='event' id=$aItem.event_id title=$aItem.title}{/if}'">
							<div class="extra_info_table">
								<div class="extra_info">
									{if $aItem.d_type == 'upcoming'}
										{if (int)$aItem.isrepeat >= 0}
											<p><strong>{phrase var='hover_repeat'}:</strong> {$aItem.d_repeat_time}</p>
											<p><strong>{phrase var='hover_nst'}:</strong> {$aItem.d_next_start_time}</p>
										{else}	
											<p><strong>{phrase var='hover_starttime'}:</strong> {$aItem.d_start_time}</p>
										{/if}					
									{/if}
									{if $aItem.d_type == 'ongoing'}
										{if (int)$aItem.isrepeat >= 0}
											<p><strong>{phrase var='hover_repeat'}:</strong> {$aItem.d_repeat_time}</p>
											<p><strong>{phrase var='hover_endtime'}:</strong> {$aItem.d_end_time}</p>
										{else}	
											<p><strong>{phrase var='hover_endtime'}:</strong> {$aItem.d_end_time}</p>
										{/if}					
									{/if}
									{if $aItem.d_type == 'past'}
										<p><strong>{phrase var='hover_starttime'}:</strong> {$aItem.d_start_time}</p>
										<p><strong>{phrase var='hover_endtime'}:</strong> {$aItem.d_end_time}</p>
									{/if}
									<p><strong>{phrase var='hover_owner'}:</strong> {$aItem|user}</p>
									{if isset($aItem.page_id) && empty($aItem.page_id) == false}
										<p><strong>{phrase var='hover_frompage'}:</strong> {$aItem.page_title}</p>
									{/if}
								</div>
							</div>
						</div>

						{if $aItem.d_type == 'upcoming' || $aItem.d_type == 'ongoing'}
					        <div class="large_item_info">
								<a class="small_title" href="{if Phpfox::getService('directory.helper')->isAdvEvent()}{permalink module='fevent' id=$aItem.event_id title=$aItem.title}{else}{permalink module='event' id=$aItem.event_id title=$aItem.title}{/if}" title="{$aItem.title|clean}">	
									<!-- start in/left -->
									{if $aItem.d_type == 'upcoming'}
										<span class="ynfe_clock">{$aItem.d_start_in|clean|shorten:17:'...'}</span> 
									{/if}
									{if $aItem.d_type == 'ongoing'}
										<span class="ynfe_clock">{$aItem.d_left|clean|shorten:17:'...'}</span> 
									{/if}
								</a>
								<div class="extra_info">
								</div>
							</div>
						{/if}					
					</div>

					<div class="large_item_action">
						<!-- event name -->
						<a class="small_title" href="{if Phpfox::getService('directory.helper')->isAdvEvent()}{permalink module='fevent' id=$aItem.event_id title=$aItem.title}{else}{permalink module='event' id=$aItem.event_id title=$aItem.title}{/if}" title="{$aItem.title|clean}">	
							{$aItem.title|clean|shorten:17:'...'}
						</a>
					</div>
				</div>
			</div>
		{/foreach}
	</div>

	<div class="clear"></div>
	{module name='directory.paging'}
	
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}