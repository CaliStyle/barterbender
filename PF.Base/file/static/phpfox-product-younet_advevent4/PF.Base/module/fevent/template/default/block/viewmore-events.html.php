<div class="ynfevent-popup-viewmore">
	{if count($aEvents)}
		<ul class="ynfevent-popup-viewmore__list">
		{foreach from=$aEvents key=sEvent item=aEvent}
            {if isset($aEvent.event_id)}
                {if (int)$aEvent.isrepeat >= 0}
                <li class="ynfevent-popup-viewmore__item repeat">
                    <a href="{$aEvent.url}" class="d-block ynfevent-popup-viewmore__title-wapper">
                        <p class="mb-0 ynfevent-popup-viewmore__title">{$aEvent.title}</p>
                        <time class="d-block text-gray-dark fz-12">{$aEvent.start_time_format} ({_p var='fevent.repeat'} {$aEvent.d_repeat_time} {_p var = 'fevent.until'} {$aEvent.date_end_time})</time>
                    </a>
                </li>
                {elseif (int)$aEvent.isrepeat == -1}
                <li class="ynfevent-popup-viewmore__item one-time">
                    <a href="{$aEvent.url}" class="d-block ynfevent-popup-viewmore__title-wapper">
                        <p class="mb-0 ynfevent-popup-viewmore__title">{$aEvent.title}</p>
                        {if (int)((int)$aEvent.end_time - (int)$aEvent.start_time) > 86400}
                            <time class="d-block text-gray-dark fz-12"> {$aEvent.start_time_format} - {$aEvent.date_end_time_hour} ({$aEvent.date_end_time})</time>
                        {else}
                            <time class="d-block text-gray-dark fz-12"> {$aEvent.start_time_format}</time>
                        {/if}
                        </a>
                </li>
                {/if}
            {else}
            <li class="ynfevent-popup-viewmore__item birthday">
                <a href="{$aEvent.url}" class="d-block ynfevent-popup-viewmore__title-wapper">
                    <p class="mb-0 ynfevent-popup-viewmore__title">{$aEvent.full_name}'{_p var='fevent.is_birthday'}</p>
                    <time class="d-block text-gray-dark fz-12">{$aEvent.new_age} {_p var='fevent.years_old'}</time>
                </a>
            </li>
            {/if}
		{/foreach}
		</ul>
	{/if}
</div>