jdPicker = (function ($) {

    function jdPicker(el, opts) {
        if (typeof (opts) != "object") opts = {};
        $.extend(this, jdPicker.DEFAULT_OPTS, opts);

        this.input = $(el);
        this.bindMethodsToObj("show", "hide", "hideIfClickOutside", "keydownHandler", "selectDate");

        this.build();
        this.selectDate();

        this.hide();
    };
    jdPicker.DEFAULT_OPTS = {
        month_names: [oTranslations["fevent.january"], oTranslations["fevent.february"], oTranslations["fevent.march"], oTranslations["fevent.april"], oTranslations["fevent.may"], oTranslations["fevent.june"], oTranslations["fevent.july"], oTranslations["fevent.august"], oTranslations["fevent.september"], oTranslations["fevent.october"], oTranslations["fevent.november"], oTranslations["fevent.december"]],
        short_month_names: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        short_day_names: [oTranslations["fevent.weekday_sunday"], oTranslations["fevent.weekday_monday"], oTranslations["fevent.weekday_tuesday"], oTranslations["fevent.weekday_wednesday"], oTranslations["fevent.weekday_thursday"], oTranslations["fevent.weekday_friday"], oTranslations["fevent.weekday_saturday"]],
        short_day_names_two_characters: [oTranslations["fevent.weekday_sunday_two_characters"], oTranslations["fevent.weekday_monday_two_characters"], oTranslations["fevent.weekday_tuesday_two_characters"], oTranslations["fevent.weekday_wednesday_two_characters"], oTranslations["fevent.weekday_thursday_two_characters"], oTranslations["fevent.weekday_friday_two_characters"], oTranslations["fevent.weekday_saturday_two_characters"]],
        error_out_of_range: "Selected date is out of range",
        selectable_days: [0, 1, 2, 3, 4, 5, 6],
        non_selectable: [],
        rec_non_selectable: [],
        start_of_week: 0,
        show_week: 0,
        select_week: 0,
        week_label: "",
        date_min: "",
        date_max: "",
        date_format: "YYYY/mm/dd"
    };
    jdPicker.prototype = {
        build: function () {

            this.wrapp = this.input.wrap('<div class="jdpicker_w">');

            if (this.input.context.type != "hidden") {
                var clearer = $('<span class="date_clearer">&times;</span>');
                clearer.click(this.bindToObj(function () {
                    this.input.val("");
                    this.selectDate();
                }));
                this.input.after(clearer);
            }

            switch (this.date_format) {
                case "dd/mm/YYYY":
                    this.reg = new RegExp(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
                    this.date_decode = "new Date(matches[3], parseInt(matches[2]-1), matches[1]);";
                    this.date_encode = 'this.strpad(date.getDate()) + "/" + this.strpad(date.getMonth()+1) + "/" + date.getFullYear();';
                    this.date_encode_s = 'this.strpad(date.getDate()) + "/" + this.strpad(date.getMonth()+1)';
                    break;
                case "FF dd YYYY":
                    this.reg = new RegExp(/^([a-zA-Z]+) (\d{1,2}) (\d{4})$/);
                    this.date_decode = "new Date(matches[3], this.indexFor(this.month_names, matches[1]), matches[2]);";
                    this.date_encode = 'this.month_names[date.getMonth()] + " " + this.strpad(date.getDate()) + " " + date.getFullYear();';
                    this.date_encode_s = 'this.month_names[date.getMonth()] + " " + this.strpad(date.getDate());';
                    break;
                case "dd MM YYYY":
                    this.reg = new RegExp(/^(\d{1,2}) ([a-zA-Z]{3}) (\d{4})$/);
                    this.date_decode = "new Date(matches[3], this.indexFor(this.short_month_names, matches[2]), matches[1]);";
                    this.date_encode = 'this.strpad(date.getDate()) + " " + this.short_month_names[date.getMonth()] + " " + date.getFullYear();';
                    this.date_encode_s = 'this.strpad(date.getDate()) + " " + this.short_month_names[date.getMonth()];';
                    break;
                case "MM dd YYYY":
                    this.reg = new RegExp(/^([a-zA-Z]{3}) (\d{1,2}) (\d{4})$/);
                    this.date_decode = "new Date(matches[3], this.indexFor(this.short_month_names, matches[1]), matches[2]);";
                    this.date_encode = 'this.short_month_names[date.getMonth()] + " " + this.strpad(date.getDate()) + " " + date.getFullYear();';
                    this.date_encode_s = 'this.short_month_names[date.getMonth()] + " " + this.strpad(date.getDate());';
                    break;
                case "dd FF YYYY":
                    this.reg = new RegExp(/^(\d{1,2}) ([a-zA-Z]+) (\d{4})$/);
                    this.date_decode = "new Date(matches[3], this.indexFor(this.month_names, matches[2]), matches[1]);";
                    this.date_encode = 'this.strpad(date.getDate()) + " " + this.month_names[date.getMonth()] + " " + date.getFullYear();';
                    this.date_encode_s = 'this.strpad(date.getDate()) + " " + this.month_names[date.getMonth()];';
                    break;
                case "YYYY/mm/dd":
                default:
                    this.reg = new RegExp(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/);
                    this.date_decode = "new Date(matches[1], parseInt(matches[2]-1), matches[3]);";
                    this.date_encode = 'date.getFullYear() + "/" + this.strpad(date.getMonth()+1) + "/" + this.strpad(date.getDate());';
                    this.date_encode_s = 'this.strpad(date.getMonth()+1) + "/" + this.strpad(date.getDate());';
                    break;
            }

            if (this.date_max != "" && this.date_max.match(this.reg)) {
                var matches = this.date_max.match(this.reg);
                this.date_max = eval(this.date_decode);
            } else
                this.date_max = "";

            if (this.date_min != "" && this.date_min.match(this.reg)) {
                var matches = this.date_min.match(this.reg);
                this.date_min = eval(this.date_decode);
            } else
                this.date_min = "";

            var monthNav = $('<p class="month_nav">' +
                '<span class="button prev"><i class="ico ico-angle-left"></i></span>' +
                ' <span class="month_name"></span> ' +
                '<span class="button next"><i class="ico ico-angle-right"></i></span>' +
                '</p>');

            this.monthNameSpan = $(".month_name", monthNav);
            $(".prev", monthNav).click(this.bindToObj(function () {
                this.moveMonthBy(-1);
            }));
            $(".next", monthNav).click(this.bindToObj(function () {
                this.moveMonthBy(1);
            }));

            this.monthNameSpan.dblclick(this.bindToObj(function () {
                this.monthNameSpan.empty().append(this.getMonthSelect());
                $('select', this.monthNameSpan).change(this.bindToObj(function () {
                    this.moveMonthBy(parseInt($('select :selected', this.monthNameSpan).val()) - this.currentMonth.getMonth());
                }));
            }));

            var yearNav = $('<p class="year_nav">' +
                '<span class="button prev"><i class="ico ico-angle-left"></i></span>' +
                ' <span class="year_name" id="year_name"></span> ' +
                '<span class="button next"><i class="ico ico-angle-right"></i></span>' +
                '</p>');

            this.yearNameSpan = $(".year_name", yearNav);
            $(".prev", yearNav).click(this.bindToObj(function () {
                this.moveMonthBy(-12);
            }));
            $(".next", yearNav).click(this.bindToObj(function () {
                this.moveMonthBy(12);
            }));

            this.yearNameSpan.dblclick(this.bindToObj(function () {

                if ($('.year_name input', this.rootLayers).length == 0) {
                    var initialDate = this.yearNameSpan.html();

                    var yearNameInput = $('<input type="text" class="text year_input" value="' + initialDate + '" />');
                    this.yearNameSpan.empty().append(yearNameInput);

                    $(".year_input", yearNav).keyup(this.bindToObj(function () {
                        if ($('input', this.yearNameSpan).val().length == 4 && $('input', this.yearNameSpan).val() != initialDate && parseInt($('input', this.yearNameSpan).val()) == $('input', this.yearNameSpan).val()) {
                            this.moveMonthBy(parseInt(parseInt(parseInt($('input', this.yearNameSpan).val()) - initialDate) * 12));
                        } else if ($('input', this.yearNameSpan).val().length > 4)
                            $('input', this.yearNameSpan).val($('input', this.yearNameSpan).val().substr(0, 4));
                    }));

                    $('input', this.yearNameSpan).focus();
                    $('input', this.yearNameSpan).select();
                }

            }));

            var error_msg = $('<div class="error_msg"></div>');

            var nav = $('<div class="nav"></div>').append(error_msg, monthNav, yearNav);

            var tableShell = "<table><thead><tr>";

            if (this.show_week == 1) tableShell += '<th class="week_label">' + (this.week_label) + '</th>';

            let dayNames = this.input.data('daynamewithtwocharacters') ? this.short_day_names_two_characters : this.short_day_names;
            $(this.adjustDays(dayNames)).each(function () {
                tableShell += "<th>" + this + "</th>";
            });
            tableShell += "</tr></thead><tbody></tbody></table>";

            var style = (this.input.context.type == "hidden") ? ' style="display:block; position:static; margin:0 auto"' : '';

            this.dateSelector = this.rootLayers = $('<div class="date_selector" ' + style + '></div>').append(nav, tableShell).insertAfter(this.input);

            if ($.browser.msie && $.browser.version < 7) {

                this.ieframe = $('<iframe class="date_selector_ieframe" frameborder="0" src="#"></iframe>').insertBefore(this.dateSelector);
                this.rootLayers = this.rootLayers.add(this.ieframe);

                $(".button", nav).mouseover(function () {
                    $(this).addClass("hover");
                });
                $(".button", nav).mouseout(function () {
                    $(this).removeClass("hover");
                });
            }
            ;

            this.tbody = $("tbody", this.dateSelector);

            this.input.change(this.bindToObj(function () {
                this.selectDate();
            }));
            this.selectDate();
        },
        buildDayEventList: function(events, day_url) {
            var limit = 2,
                remain_events = 0,
                total_event = events.length,
                tooltip = '<div class="p-fevent-popup-calendar-info"><div class="item-popup-outer">',
                d_class = 'bg-primary';
            if (total_event > limit) {
                remain_events = total_event - limit;
            } else {
                limit = total_event;
            }
            for (var i = 0; i < limit; i++) {
                if (events[i]['event_d_type'] == 'ongoing') {
                    d_class = 'bg-success';
                } else if (events[i]['event_d_type'] == 'past') {
                    d_class = 'bg-gray';
                }
                tooltip += '<div class="item-event">' + (events[i]['is_birthday'] ? '<span class="ico ico-gift p-fevent-birthday '+ d_class + '" style="margin-right: 6px;"></span>' : '<span class="item-status-dot p-fevent-dot-status ' + d_class + '"></span>')
                    + '<a href="' + events[i]['event_url'] + '">' + (events[i]['is_birthday'] ? events[i]['display_name'] : events[i]['event_name']) + '</a>'
                    + '</div>';
            }
            var onclick = '$(this).closest(".selectable_day").trigger("click")';
            if (remain_events) {
                if (remain_events > 1) {
                    tooltip += '<div class="item-event" onclick="' + onclick + '"><a href="javascript:void(0);">' + oTranslations['fevent_and'] + ' ' + remain_events + ' ' + oTranslations['fevent_others'] + '</a></div>';
                } else {
                    tooltip += '<div class="item-event" onclick="' + onclick + '"><a href="javascript:void(0);">' + oTranslations['fevent_and'] + ' ' + remain_events + ' ' + oTranslations['fevent_other'] + '</a></div>';
                }
            }
            tooltip += '</div></div>';

            return tooltip;
        },
        selectMonth: function (date) {
            var newMonth = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            if (this.isNewDateAllowed(newMonth)) {
                if (!this.currentMonth || !(this.currentMonth.getFullYear() == newMonth.getFullYear() &&
                    this.currentMonth.getMonth() == newMonth.getMonth())) {

                    this.currentMonth = newMonth;

                    var rangeStart = this.rangeStart(date), rangeEnd = this.rangeEnd(date);
                    var numDays = this.daysBetween(rangeStart, rangeEnd);
                    var dayCells = "";

                    for (var i = 0; i <= numDays; i++) {
                        var currentDay = new Date(rangeStart.getFullYear(), rangeStart.getMonth(), rangeStart.getDate() + i, 12, 00);
                        if (this.isFirstDayOfWeek(currentDay)) {
                            var firstDayOfWeek = currentDay;
                            if (this.select_week && this.isNewDateAllowed(firstDayOfWeek))
                                dayCells += "<tr date='" + this.dateToString(currentDay) + "' class='selectable_week'>";
                            else
                                dayCells += "<tr>";

                            if (this.show_week == 1)
                                dayCells += '<td class="week_num">' + this.getWeekNum(currentDay) + '</td>';
                        }
                        if ((this.select_week == 0 && currentDay.getMonth() == date.getMonth() && this.isNewDateAllowed(currentDay) && !this.isHoliday(currentDay)) || (this.select_week == 1 && currentDay.getMonth() == date.getMonth() && this.isNewDateAllowed(firstDayOfWeek))) {
                            isAvailable = false;
                            isToday = this.isToday(currentDay);
                            var tooltip = '';
                            if (typeof (POOL.search[this.dateToString(currentDay)]) != 'undefined') {
                                isAvailable = true;
                                currentFEDay = POOL.search[this.dateToString(currentDay)];

                                tooltip = this.buildDayEventList(currentFEDay);
                                totalEvents = currentFEDay.length;

                                sPhrase = '';
                                if (totalEvents > 2) {
                                    if (totalEvents == 3) {
                                        sPhrase = oTranslations['and'] + ' 1 ' + oTranslations['more event'];

                                    } else {
                                        sPhrase = oTranslations['and'] + ' ' + (totalEvents - 2) + ' ' + oTranslations['more events'];
                                    }
                                }

                            }
                            dayCells += '<td class="selectable_day' + (isAvailable ? ' available p-fevent-popup-calendar-parent' : '') + (isToday ? ' today' : '') + '" date="' + this.dateToString(currentDay) + '"><div class="item-box-outer"><span class="item-box-inner">' + currentDay.getDate() + '</span>' + tooltip + '</div></td>';

                        } else {
                            dayCells += '<td class="unselected_month" date="' + this.dateToString(currentDay) + '"><span class="item-box-outer"><span class="item-box-inner">' + currentDay.getDate() + '</span></span></td>';
                        }
                        ;

                        if (this.isLastDayOfWeek(currentDay)) dayCells += "</tr>";
                    }
                    ;
                    this.tbody.empty().append(dayCells);

                    let getFullMonthName = this.input.data('fullmonthname');
                    this.monthNameSpan.empty().append(getFullMonthName ? this.getFullMonthName(date) : this.monthName(date));

                    this.yearNameSpan.empty().append(this.currentMonth.getFullYear());
                    if (this.select_week == 0) {
                        $(".selectable_day", this.tbody).click(this.bindToObj(function (event) {
                            this.changeInput($(event.target).attr("date"));
                            var url = window.location.href;
                            url = url.split("?")[0];
                            url += '?date=' + $(event.target).closest('td').attr("date").replace(/\//g, '-') + '&';
                            url += 'when=all-time&view=all';
                            window.location = url;
                        }));
                    } else {
                        $(".selectable_week", this.tbody).click(this.bindToObj(function (event) {
                            this.changeInput($(event.target.parentNode).attr("date"));
                        }));
                    }

                    if (this.select_week == 1) {
                        $("tr", this.tbody).mouseover(function () {
                            $(this).addClass("hover");
                        });
                        $("tr", this.tbody).mouseout(function () {
                            $(this).removeClass("hover");
                        });
                    } else {
                        $("td.selectable_day", this.tbody).mouseover(function () {
                            $(this).addClass("hover");
                        });
                        $("td.selectable_day", this.tbody).mouseout(function () {
                            $(this).removeClass("hover");
                        });
                    }
                }
                ;

                $('.selected', this.tbody).removeClass("selected");
                $('td[date="' + this.selectedDateString + '"], tr[date="' + this.selectedDateString + '"]', this.tbody).addClass("selected");
            } else
                this.show_error(this.error_out_of_range);
        },

        selectDate: function (date) {
            if (typeof (date) == "undefined") {
                date = this.stringToDate(this.input.val());
            }
            ;
            if (!date) {
                var flag_date_check = true;
                date = new Date();
            }

            if (this.select_week == 1 && !this.isFirstDayOfWeek(date))
                date = new Date(date.getFullYear(), date.getMonth(), (date.getDate() - date.getDay() + this.start_of_week), 12, 00);

            if (this.isNewDateAllowed(date)) {
                this.selectedDate = date;
                this.selectedDateString = this.dateToString(this.selectedDate);
                if(flag_date_check) {
                    this.selectedDateString = null;
                }
                this.selectMonth(this.selectedDate);
            } else if ((this.date_min) && this.daysBetween(this.date_min, date) < 0) {
                this.selectedDate = this.date_min;
                this.selectMonth(this.date_min);
                this.input.val(" ");
            } else {
                this.selectMonth(this.date_max);
                this.input.val(" ");
            }
        },

        isNewDateAllowed: function (date) {
            return ((!this.date_min) || this.daysBetween(this.date_min, date) >= 0) && ((!this.date_max) || this.daysBetween(date, this.date_max) >= 0);
        },

        isHoliday: function (date) {
            return ((this.indexFor(this.selectable_days, date.getDay()) === false || this.indexFor(this.non_selectable, this.dateToString(date)) !== false) || this.indexFor(this.rec_non_selectable, this.dateToShortString(date)) !== false);
        },

        changeInput: function (dateString) {
            this.input.val(dateString).change();
            if (this.input.context.type != "hidden")
                this.hide();
        },

        show: function () {
            $('.error_msg', this.rootLayers).css('display', 'none');
            this.rootLayers.slideDown();
            $([window, document.body]).click(this.hideIfClickOutside);
            this.input.unbind("focus", this.show);
            this.input.attr('readonly', true);
            $(document.body).keydown(this.keydownHandler);
            this.setPosition();
        },

        hide: function () {
            if (this.input.context.type != "hidden") {
                this.input.removeAttr('readonly');
                this.rootLayers.slideUp();
                $([window, document.body]).unbind("click", this.hideIfClickOutside);
                this.input.focus(this.show);
                $(document.body).unbind("keydown", this.keydownHandler);
            }
        },

        hideIfClickOutside: function (event) {
            if (event.target != this.input[0] && !this.insideSelector(event)) {
                this.hide();
            }
            ;
        },

        insideSelector: function (event) {
            var offset = this.dateSelector.position();
            offset.right = offset.left + this.dateSelector.outerWidth();
            offset.bottom = offset.top + this.dateSelector.outerHeight();

            return event.pageY < offset.bottom &&
                event.pageY > offset.top &&
                event.pageX < offset.right &&
                event.pageX > offset.left;
        },

        keydownHandler: function (event) {
        },

        stringToDate: function (string) {
            var matches;

            if (matches = string.match(this.reg)) {
                if (matches[3] == 0 && matches[2] == 0 && matches[1] == 0)
                    return null;
                else
                    return eval(this.date_decode);
            } else {
                return null;
            }
            ;
        },

        dateToString: function (date) {
            return eval(this.date_encode);
        },

        dateToShortString: function (date) {
            return eval(this.date_encode_s);
        },

        setPosition: function () {
            var offset = this.input.offset();
            this.rootLayers.css({
                top: offset.top + this.input.outerHeight(),
                left: offset.left
            });

            if (this.ieframe) {
                this.ieframe.css({
                    width: this.dateSelector.outerWidth(),
                    height: this.dateSelector.outerHeight()
                });
            }
            ;
        },

        moveDateBy: function (amount) {
            var newDate = new Date(this.selectedDate.getFullYear(), this.selectedDate.getMonth(), this.selectedDate.getDate() + amount);
            this.selectDate(newDate);
        },

        moveDateMonthBy: function (amount) {
            var newDate = new Date(this.selectedDate.getFullYear(), this.selectedDate.getMonth() + amount, this.selectedDate.getDate());
            if (newDate.getMonth() == this.selectedDate.getMonth() + amount + 1) {
                newDate.setDate(0);
            }
            ;
            this.selectDate(newDate);
        },

        moveMonthBy: function (amount) {
            if (amount < 0)
                var newMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + amount + 1, -1);
            else
                var newMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + amount, 1);
            this.selectMonth(newMonth);
        },

        monthName: function (date) {
            return this.short_month_names[date.getMonth()];
        },

        getFullMonthName: function (date) {
            return this.month_names[date.getMonth()];
        },

        getMonthSelect: function () {
            var month_select = '<select>';
            for (var i = 0; i < this.short_month_names.length; i++) {
                if (i == this.currentMonth.getMonth())
                    month_select += '<option value="' + (i) + '" selected="selected">' + this.short_month_names[i] + '</option>';
                else
                    month_select += '<option value="' + (i) + '">' + this.short_month_names[i] + '</option>';
            }
            month_select += '</select>';

            return month_select;
        },

        bindToObj: function (fn) {
            var self = this;
            return function () {
                return fn.apply(self, arguments)
            };
        },

        bindMethodsToObj: function () {
            for (var i = 0; i < arguments.length; i++) {
                this[arguments[i]] = this.bindToObj(this[arguments[i]]);
            }
            ;
        },

        indexFor: function (array, value) {
            for (var i = 0; i < array.length; i++) {
                if (value == array[i]) return i;
            }
            ;
            return false;
        },

        monthNum: function (month_name) {
            return this.indexFor(this.month_names, month_name);
        },

        shortMonthNum: function (month_name) {
            return this.indexFor(this.short_month_names, month_name);
        },

        shortDayNum: function (day_name) {
            return this.indexFor(this.short_day_names, day_name);
        },

        daysBetween: function (start, end) {
            start = Date.UTC(start.getFullYear(), start.getMonth(), start.getDate());
            end = Date.UTC(end.getFullYear(), end.getMonth(), end.getDate());
            return (end - start) / 86400000;
        },

        changeDayTo: function (dayOfWeek, date, direction) {
            var difference = direction * (Math.abs(date.getDay() - dayOfWeek - (direction * 7)) % 7);
            return new Date(date.getFullYear(), date.getMonth(), date.getDate() + difference);
        },

        rangeStart: function (date) {
            return this.changeDayTo(this.start_of_week, new Date(date.getFullYear(), date.getMonth()), -1);
        },

        rangeEnd: function (date) {
            return this.changeDayTo((this.start_of_week - 1) % 7, new Date(date.getFullYear(), date.getMonth() + 1, 0), 1);
        },

        isFirstDayOfWeek: function (date) {
            return date.getDay() == this.start_of_week;
        },

        getWeekNum: function (date) {
            date_week = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 6);
            var firstDayOfYear = new Date(date_week.getFullYear(), 0, 1, 12, 00);
            var n = parseInt(this.daysBetween(firstDayOfYear, date_week)) + 1;
            return Math.floor((date_week.getDay() + n + 5) / 7) - Math.floor(date_week.getDay() / 5);
        },

        isLastDayOfWeek: function (date) {
            return date.getDay() == (this.start_of_week - 1) % 7;
        },

        isToday: function (date) {
            var currentDate = new Date();
            return date.getDate() == currentDate.getDate() && date.getMonth() == currentDate.getMonth() && date.getFullYear() == currentDate.getFullYear();
        },

        show_error: function (error) {
            $('.error_msg', this.rootLayers).html(error);
            $('.error_msg', this.rootLayers).slideDown(400, function () {
                setTimeout("$('.error_msg', this.rootLayers).slideUp(200);", 2000);
            });
        },

        adjustDays: function (days) {
            var newDays = [];
            for (var i = 0; i < days.length; i++) {
                newDays[i] = days[(i + this.start_of_week) % 7];
            }
            ;
            return newDays;
        },

        strpad: function (num) {
            if (parseInt(num) < 10) return "0" + parseInt(num);
            else return parseInt(num);
        }

    };
    $.fn.jdPicker = function (opts) {
        return this.each(function () {
            new jdPicker(this, opts);
        });
    };
    $.jdPicker = {
        initialize: function (opts) {
            $("input.jdpicker").jdPicker(opts);
        }
    };

    return jdPicker;
})(jQuery);

$($.jdPicker.initialize);