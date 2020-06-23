{if count($aCurrentEvents)}
	{foreach from=$aCurrentEvents item=aEvent}
	<div class="ynfevent-detail-recurring-item">
		<div class="ynfevent-detail-recurring-time">
			{$aEvent.M_start_time|shorten:3} {$aEvent.d_Y_start_time} <span>{$aEvent.d_start_time_hour}</span>
		</div>

		<div class="ynfevent-detail-recurring-main">
			<div class="ynfevent-detail-recurring-title">
				<a href="{$aEvent.url}">{$aEvent.title}</a>
			</div>

			<div class="ynfevent-detail-recurring-attend">
				{$aEvent.number_attending} {_p var='fevent.attending'}
			</div>
		</div>
	</div>
	{/foreach}
{/if}