<div class="fevent-detail-statistics">
	<div class="fevent-detail-statistics{if isset($aEvent.d_left_past)}__past-event{/if}{if isset($aEvent.d_left)}__ongoing-event{/if}{if isset($aEvent.d_start_in)}__upcoming-event{/if} px-2 py-2">
		<span class="fevent-detail-statistics__icon"><i class="ico{if isset($aEvent.d_left_past)} ico-sandclock-end-o{/if}{if isset($aEvent.d_left)} ico-refresh-o{/if}{if isset($aEvent.d_start_in)} ico-sandclock-end-o{/if}"></i></span>
		<div class="fevent-detail-statistics__body ml-2">
			<p class="mb-0 d-block fevent-detail-statistics__title">{if isset($aEvent.d_start_in)}{$aEvent.d_start_in}{/if}{if isset($aEvent.d_left)}{$aEvent.d_left}{/if}{if isset($aEvent.d_left_past)}{$aEvent.d_left_past}{/if}</p>
			<p class="fevent-detail-statistics__label text-gray-dark d-block mb-0">{if isset($aEvent.d_start_in)}{_p var='fevent.start_in_the_event'}{/if}{if isset($aEvent.d_left)}{_p var='fevent.left_the_event'}{/if}{if isset($aEvent.d_left_past)}{_p var='fevent.ago'}{/if}</p>
		</div>
	</div>
</div>