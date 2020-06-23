var ynfeAddPage = {
    dataPrint: {},
    success: false,
    isEditEventConfirmBox: false,
    successIds: '',
    init: function (initTimePicker) {
        $('#p-fevent-categories').change(function () {
            if ($(this).val() === '') {
                var comboboxes = $("#categories .js_mp_fevent_category_list");
                for (var i = 0; i < comboboxes.length; i++) {
                    if (comboboxes[i].id === this.id && i > 0) {
                        $(comboboxes[i - 1]).change();
                    }
                }
                return;
            }
            // Display custom fields if available
            var event_id = $('#category_event_id').val();

            var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
            $.ajaxCall("fevent.getCustomFields", "id=" + $(this).val() + "&event_id=" + event_id + "&parent_id=" + iParentId);
            $('.js_mp_fevent_category_list').each(function () {
                if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId) {
                    $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

                    this.value = '';
                }
            });
            $('#js_mp_holder_' + $(this).val()).show();
        });
        $('#p-fevent-categories').trigger('change');

        var $p_fevent_has_ticket = $('#p_fevent_has_ticket'),
            $ticket_info_group = $('#ticket_info_group');
        if ($p_fevent_has_ticket.length && $ticket_info_group.length) {
            $p_fevent_has_ticket.change(function () {
                $ticket_info_group.toggle($p_fevent_has_ticket.prop('checked'));
            });
            $ticket_info_group.toggle($p_fevent_has_ticket.prop('checked'));
        }

        var $ticket_type = $('#ticket_type'),
            $ticket_price = $('#ticket_price');

        if ($ticket_type.length && $ticket_price.length) {
            $ticket_type.change(function () {
                $ticket_price.prop('disabled', $ticket_type.val() == 'free');
            });
            $ticket_price.prop('disabled', $ticket_type.val() == 'free');
        }

        var $p_fevent_repeat_select = $('#p_fevent_repeat_select'),
            $p_event_end_repeat = $('#p_event_end_repeat')
        ;
        if ($p_fevent_repeat_select.length && $p_event_end_repeat.length) {
            $p_fevent_repeat_select.change(function () {
                $p_event_end_repeat.toggle($p_fevent_repeat_select.val() != '-1');
            });
            $p_event_end_repeat.toggle($p_fevent_repeat_select.val() != '-1');
        }

        var $p_fevent_has_notification = $('#p_fevent_has_notification'),
            $p_fevent_notification_value = $('#p_fevent_notification_value')
        ;
        if ($p_fevent_has_notification.length && $p_fevent_notification_value.length) {
            $p_fevent_has_notification.change(function () {
                $p_fevent_notification_value.toggle($p_fevent_has_notification.prop('checked'));
            });
            $p_fevent_notification_value.toggle($p_fevent_has_notification.prop('checked'));
        }
        if (initTimePicker) {
            $("#start_time").picktim({
                mode: 'h24',
                formName: 'val[start_time]',
                defaultValue: start_time ? start_time : 'now',
            });
            var today = new Date();
            today = new Date(today.getTime() + 60*60*1000);
            let minute = today.getMinutes();
            var current_plus_one_hour = today.getHours() + ":" + (minute < 10 ? ('0' + minute) : minute);
            $("#end_time").picktim({
                mode: 'h24',
                formName: 'val[end_time]',
                defaultValue: end_time ? end_time : current_plus_one_hour,
            });
        }

        var $photoConfirmBtn = $('#p_fevent_confirm_photo');
        if ($photoConfirmBtn.length) {
            $photoConfirmBtn.click(function () {
                var totalDropZonePhotos = 0,
                    totalCurrentPhotos = $('.fevent-manage-photo .js_mp_photo').length,
                    $currentPhotos = $('#js_p_fevent_total_photos')
                ;

                if (typeof $Core.dropzone !== 'undefined' && typeof $Core.dropzone.instance['fevent'] !== 'undefined') {
                    totalDropZonePhotos += $Core.dropzone.instance['fevent'].files.length;
                }

                if ($currentPhotos.length) {
                    totalDropZonePhotos += $currentPhotos.val();
                }

                var totalPhoto = totalCurrentPhotos + totalDropZonePhotos;
                window.location.href = photoConfirmLink;
            });
        }

        var $inviteConfirmBtn = $('#p-fevent-confirm-invite');
        if ($inviteConfirmBtn.length) {
            $inviteConfirmBtn.click(function () {
                window.location.href = inviteConfirmLink;
            });
        }

        $('.p-step-nav-container a.p-step-link').click(function () {
            // remove error message when click another tab
            var sRel = $(this).attr('rel');
            if (empty(sRel)) {
                return true;
            }

            $('#core_js_messages').remove();

            ynfeAddPage.switchStep(sRel);

            return false;
        });
    }
    , getSameDayInNextMonth: function (day, month, year) {
        //  if date is invalid, date will be become last date in month
        if (month == 12) {
            month = 1;
            year = year + 1;
        } else {
            month = month + 1;
        }

        var exist = false;
        while (exist == false) {
            var date = new Date(year, month - 1, day);
            if (date.getFullYear() == year && date.getMonth() + 1 == month && date.getDate() == day) {
                exist = true;
                break;
            } else {
                exist = false;
                day = day - 1;
            }
        }

        return {day: day, month: month, year: year};
    }
    , daysToDate: function (day1, month1, year1, day2, month2, year2) {
        var oneDay = 1 * 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var firstDate = new Date(year1, month1 - 1, day1);
        var secondDate = new Date(year2, month2 - 1, day2);

        var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

        return diffDays;
    }
    , confirmEditEvent: function () {
        if (ynfeAddPage.isEditEventConfirmBox) {
            return true;
        }

        sHtml = "";
        sHtml += '<div class="white-popup-block" style="width: 300px;">';
        sHtml += '<div class="ynfevent-edit-confirm-box-title">';
        sHtml += oTranslations['fevent.edit_apply_for'];
        sHtml += '</div>';

        sHtml += '<div>';
        sHtml += oTranslations['fevent.please_choose_the_type_of_event_to_edit'];
        sHtml += '</div>';

        sHtml += '<div id="ynfevent_editconfirmboxoption">';
        sHtml += '<input type="radio" name="popup_confirmeditevent" value="only_this_event" checked="checked" />';
        sHtml += oTranslations[' fevent.only_this_event'];
        sHtml += '</br> <input type="radio" name="popup_confirmeditevent" value="all_events_uppercase" />';
        sHtml += oTranslations[' fevent.all_events_uppercase'];
        sHtml += '</br> <input type="radio" name="popup_confirmeditevent" value="following_events" />';
        sHtml += oTranslations[' fevent.following_events'];
        sHtml += '</div>';

        sHtml += '<div class="ynfevent-edit-confirm-box-hint">';
        sHtml += oTranslations['fevent.note_apply_only_data_in_event_details_tab'];
        sHtml += '</div>';

        sHtml += '<div class="ynfevent-edit-confirm-box-button">';
        sHtml += '<button class="btn btn-sm btn-primary" onclick="ynfeAddPage.yesConfirmEditEvent();">';
        sHtml += oTranslations['fevent.confirm'];
        sHtml += '</button>';
        sHtml += '<button class="btn btn-sm btn-danger" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
        sHtml += oTranslations['fevent.cancel'];
        sHtml += '</button>';
        sHtml += '</div>';

        sHtml += '</div>';

        $.magnificPopup.open({
            items: {
                src: sHtml,
                type: 'inline'
            }
        });

        return false;
    }
    , yesConfirmEditEvent: function () {
        var selected = $("#ynfevent_editconfirmboxoption input[type='radio']:checked");
        if (selected.length > 0) {
            $('#ynfevent_editconfirmboxoption_value').val(selected.val());
        }

        if (custom_js_event_form()) {
            ynfeAddPage.isEditEventConfirmBox = true;
            $("#js_event_form").submit();
        }
    },
    dropzoneOnAddedFile: function () {
        $('#js_fevent_done_upload').show();
    },

    dropzoneOnSending: function (data, xhr, formData) {
        $('#js_event_form').find('input[type="hidden"]').each(function () {
            formData.append($(this).prop('name'), $(this).val());
        });
    },

    dropzoneOnSuccess: function (ele, file, response) {
        response = JSON.parse(response);
        if (typeof response.id !== 'undefined') {
            file.item_id = response.id;
        }
        // show error message
        if (typeof response.errors != 'undefined') {
            for (var i in response.errors) {
                if (response.errors[i]) {
                    $Core.dropzone.setFileError('fevent', file, response.errors[i]);
                    return;
                }
            }
        }
        ynfeAddPage.success = true;
        ynfeAddPage.successIds += response.id + ',';
        return file.previewElement.classList.add('dz-success');
    },

    dropzoneOnError: function (ele, file) {

    },
    dropzoneQueueComplete: function () {
        if (ynfeAddPage.success) {
            $('#js_fevent_succes_message').fadeIn().fadeOut(2000);
            ynfeAddPage.success = false;
            if ($('input[name="val[ynfevent_editconfirmboxoption_value]"]:checked').val() !== 'undefined') {
                $Core.ajax('fevent.copyRecurringImage',
                    {
                        type: 'POST',
                        params:
                            {
                                'sIds': ynfeAddPage.successIds,
                                'event_id': $('#eventID').val(),
                                'confirm_type': $('input[name="val[ynfevent_editconfirmboxoption_value]"]:checked').val()
                            },
                        success: function () {
                            $('#js_fevent_done_upload').show();
                            ynfeAddPage.successIds = '';
                        }
                    }
                );
            } else {
                ynfeAddPage.successIds = '';
                $('#js_fevent_done_upload').show();
            }
        }
    },
    setDefault: function (el, id) {
        var $holder = $(el).closest('#js_photo_holder_' + id);
        $('.js_mp_photo').removeClass('row_focus');
        $holder.addClass('row_focus');
        $('.js_mp_photo .js_delete').show();
        $holder.find('.js_delete').hide();
        $.ajaxCall('fevent.setDefault', 'id=' + id);
    },
    deleteImage: function (ele, event_id, id) {
        $.ajaxCall('fevent.deleteImage', 'id=' + id + '&event_id=' + event_id);

        return false;
    },
    switchStep: function (step) {
        var $stepLink = $('.p-step-link[rel="' + step + '"]');

        $('.p-step-item').removeClass('active');
        $stepLink.closest('.p-step-item').addClass('active');

        $('.page_section_menu_holder').hide();
        $('#' + step).show();

        if ($('#page_section_menu_form').length > 0) {
            $('#page_section_menu_form').val(step);
        }
        // set current tab
        $('#current_tab').val($stepLink.attr('href').replace('#', ''));
    },
    toggleCreatingUploadMorePhotos: function() {
        $('#p_fevent_back_to_manage_container').show();
        $('#p_fevent_confirm_photo').html(oTranslations['finish_photo_uploading']);
    },
    toggleCreatingBackToManagePhotos: function() {
        $Core.dropzone.instance['fevent'].files = [];
        $('#p_fevent_back_to_manage_container').hide();
        $('#p_fevent_confirm_photo').html(oTranslations['next']);
    },
    toggleUploadSection: function (id, show_upload, is_creating) {
        $.ajaxCall('fevent.toggleUploadSection', 'show_upload=' + show_upload + '&id=' + id + '&is_creating=' + is_creating);
    },
};


function plugin_addFriendToSelectList(sId) {
    var ele = $('#js_friend_' + sId + ''),
        imgele = ele.parent().find('img'),
        spanele = ele.parent().find('.no_image_user');
    if (imgele.length) {
        imgele.css('margin-right', '5px');
        ele.prepend(imgele);
    } else {
        ele.css('display', 'flex').css('align-items', 'center');
        spanele.css('margin-right', '5px');
        ele.prepend(spanele);
    }
    ele.css('max-width', '110px');
}

function custom_js_event_form(form) {
    var $js_event_form_msg = $('#js_event_form_msg');
    $js_event_form_msg.html('');
    var bIsValid = true;
    var fields = eval($("#required_custom_fields").val());
    if (fields != null)
        for (var i = 0; i < fields.length; i++) {
            var passed = true;
            switch (fields[i]['var_type']) {
                case "radio":
                case "checkbox":
                    if ($('input[id="cf_' + fields[i]['field_name'] + '"]:checked').length === 0) {
                        passed = false;
                    }
                    break;
                default:
                    var value = $.trim($('#cf_' + fields[i]['field_name']).val());
                    if (value === '' || value == null) {
                        passed = false;
                    }
            }
            if (!passed) {
                bIsValid = false;
                $js_event_form_msg.message(oTranslations['fevent.the_field_field_name_is_required'].replace('{field_name}', fields[i]['phrase_name']), 'error');
                $('#cf_' + fields[i]['field_name']).addClass('alert_input');
            }
        }
    if (!bIsValid) {
        $js_event_form_msg.show();
        window.scrollTo(0, 0);
    } else {
        $js_event_form_msg.hide('');
    }
    return bIsValid && Validation_js_event_form(form);
};