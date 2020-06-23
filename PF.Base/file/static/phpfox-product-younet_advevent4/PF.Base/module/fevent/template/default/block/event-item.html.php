<div class="ynfevent-content-item item-outer">
	<div class="ynfevent-content-item__photo">
		<a href="{permalink module='fevent' id=$aItem.event_id title=$aItem.title}" class="ynfevent-content-item__thumb" style="background-image:url('{$aItem.image_path}')"></a>
		<div class="ynfevent-content-item__label-status">
		    <div class="sticky-label-icon sticky-featured-icon" {if !$aItem.is_featured}style="display: none"{/if}>
		        <span class="flag-style-arrow"></span>
		        <i class="ico ico-diamond"></i>
		    </div>
		    <div class="sticky-label-icon sticky-sponsored-icon" {if !$aItem.is_sponsor}style="display: none"{/if}>
			    <span class="flag-style-arrow"></span>
			    <i class="ico ico-sponsor"></i>
		    </div>
		</div>
	</div>
	<div class="ynfevent-content-item__body">
		<a class="ynfevent-content-item__title" href="{permalink module='fevent' id=$aItem.event_id title=$aItem.title}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
		<div class="ynfevent-content-item__owner text-gray fz-12 mt-h1">
			{_p var='fevent.by'} {$aItem|user}
		</div>
		<time class="ynfevent-content-item__time">
			<p class="ynfevent-content-item__time__start mb-0 text-primary fz-12"><span class="fw-bold">{$aItem.date_start_time} - {$aItem.short_start_time}</span>{if (int)$aItem.isrepeat >= 0} <span class="text-gray-dark fz-12">(Repeated)</span>{/if}</p>
            <p class="ynfevent-content-item__time__end fz-12 mb-0 fw-bold">
                {if (int)((int)$aItem.end_time - (int)$aItem.start_time) > 86400}
                    {if ($aItem.check) > 0}
                		{_p var='fevent.end'}: {$aItem.date_end_time} - {$aItem.date_end_time_hour}
                    {else}
                        {_p var='fevent.end'}: {$aItem.date_end_time1} - {$aItem.date_end_time_hour}
                    {/if}
                {else}
                    {_p var='fevent.end'}: {$aItem.date_end_time_hour}
                {/if}
			</p>
		</time>
		<div class="ynfevent-content-item__info">
			<p class="ynfevent-content-item__location mb-0">{$aItem.location|clean|shorten:50:'...'}</p>
			<div class="hidden ynfevent-content-item__show-invite">
				<p class="ynfevent-content-item__description mb-0 text-gray-dark item_view_content">{$aItem.description_parsed|striptag|clean|shorten:100:'...'}</p>
			</div>
		</div>
	</div>
</div>