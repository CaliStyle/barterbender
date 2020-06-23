<div class="js_find_event_block">
    <form id="js_find_event_block_form" action="{url link='fevent'}" method="GET">
        <input type="hidden" value="" class="js_find_event_start_time" name="search[stime]">
        <input type="hidden" value="" class="js_find_event_end_time" name="search[etime]">
        <input type="hidden" class="js_find_event_time_type" value="" name="search[time_type]">
        <input type="hidden" name="search[advsearch]" value="1"/>
    </form>
    <div class="p-fevent-find-event-container">
        <div class="item-find-event">
            <div class="item-outer js_find_event_select" data-type="today" data-text="{_p var='today'}" onclick="P_AdvEvent.findEventClick(this);">
                <div class="item-icon">
                    <i class="ico ico-calendar-o"></i>
                </div>
                <div class="item-title">
                    {_p var='today'}
                </div>
            </div>
        </div>

        <div class="item-find-event">
            <div class="item-outer js_find_event_select" data-type="tomorrow" data-text="{_p var='tomorrow'}" onclick="P_AdvEvent.findEventClick(this);">
                <div class="item-icon">
                    <i class="ico ico-calendar-o"></i>
                </div>
                <div class="item-title">
                    {_p var='tomorrow'}
                </div>
            </div>
        </div>

        <div class="item-find-event">
            <div class="item-outer js_find_event_select" data-type="this-week" data-text="{_p var='this_week'}" onclick="P_AdvEvent.findEventClick(this);">
                <div class="item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
				<g>
                    <path d="M19,3h-1v2h1c1.1,0,2,0.9,2,2v2H3V7c0-1.1,0.9-2,2-2h1V3H5C2.8,3,1,4.8,1,7v12c0,2.2,1.8,4,4,4h14c2.2,0,4-1.8,4-4V7   C23,4.8,21.2,3,19,3z M16,11v4h-3v-4H16z M11,11v4H8v-4H11z M3,11h3v4H3V11z M5,21c-1.1,0-2-0.9-2-2v-2h3v4H5z M8,21v-4h3v4H8z    M13,21v-4h3v4H13z M21,19c0,1.1-0.9,2-2,2h-1v-4h3V19z M21,15h-3v-4h3V15z"/>
                    <path d="M8,7c0.6,0,1-0.4,1-1V2c0-0.6-0.4-1-1-1S7,1.4,7,2v4C7,6.6,7.4,7,8,7z"/>
                    <path d="M16,7c0.6,0,1-0.4,1-1V2c0-0.6-0.4-1-1-1s-1,0.4-1,1v4C15,6.6,15.4,7,16,7z"/>
                    <rect x="10" y="3" width="4" height="2"/>
                </g>
				</svg>
                </div>
                <div class="item-title">
                    {_p var='this_week'}
                </div>
            </div>
        </div>

        <div class="item-find-event">
            <div class="item-outer js_find_event_select" data-type="next-week" data-text="{_p var='advevent_next_week'}" onclick="P_AdvEvent.findEventClick(this);">
                <div class="item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
				<g>
                    <path d="M19,3h-1v2h1c1.1,0,2,0.9,2,2v2H3V7c0-1.1,0.9-2,2-2h1V3H5C2.8,3,1,4.8,1,7v12c0,2.2,1.8,4,4,4h14c2.2,0,4-1.8,4-4V7   C23,4.8,21.2,3,19,3z M16,11v4h-3v-4H16z M11,11v4H8v-4H11z M3,11h3v4H3V11z M5,21c-1.1,0-2-0.9-2-2v-2h3v4H5z M8,21v-4h3v4H8z    M13,21v-4h3v4H13z M21,19c0,1.1-0.9,2-2,2h-1v-4h3V19z M21,15h-3v-4h3V15z"/>
                    <path d="M8,7c0.6,0,1-0.4,1-1V2c0-0.6-0.4-1-1-1S7,1.4,7,2v4C7,6.6,7.4,7,8,7z"/>
                    <path d="M16,7c0.6,0,1-0.4,1-1V2c0-0.6-0.4-1-1-1s-1,0.4-1,1v4C15,6.6,15.4,7,16,7z"/>
                    <rect x="10" y="3" width="4" height="2"/>
                </g>
				</svg>
                </div>
                <div class="item-title">
                    {_p var='advevent_next_week'}
                </div>
            </div>
        </div>

        <div class="item-find-event">
            <div class="item-outer js_find_event_{$currentTime}">
                <div class="item-icon">
                    <i class="ico ico-calendar-o"></i>
                </div>
                <div class="item-title">
                    {_p var='advevent_choose_date'}
                </div>
            </div>
        </div>
    </div>
</div>

{literal}
<script type="text/javascript">
    $Behavior.find_events_block_{/literal}{$currentTime}{literal} = function() {
        let ele = '.js_find_event_{/literal}{$currentTime}{literal}';
        let startInit = moment().startOf('isoWeek').format('MM/DD/YYYY');
        let endInit = moment().endOf('isoWeek').format('MM/DD/YYYY');
        let formInit = $(ele).closest('.js_find_event_block').find('form:first');
        formInit.find('.js_find_event_start_time').val(startInit);
        formInit.find('.js_find_event_end_time').val(endInit);
        formInit.find('.js_find_event_time_type').val('Custom Range');
        P_AdvEvent.initSimpleDateRangePickerWithOnlyCustomRange(ele, function(start, end) {
            let startTime = start.format('MM/DD/YYYY');
            let endTime = end.format('MM/DD/YYYY');
            let startDateArray = trim(startTime).split('/');
            let endDateArray = trim(endTime).split('/');
            if(startDateArray.length == 3 && endDateArray.length == 3) {
                let form = $(ele).closest('.js_find_event_block').find('form:first');
                form.find('.js_find_event_start_time').val(startTime);
                form.find('.js_find_event_end_time').val(endTime);
                form.find('.js_find_event_time_type').val('Custom Range');
            }
        });
    }
</script>
{/literal}

