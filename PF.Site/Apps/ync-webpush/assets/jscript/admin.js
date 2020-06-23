$Ready(function () {
    $('.js_audience_type').on('change', function () {
        var select = $(this).data('select-box');
        $('.js_audience_select').hide();
        if (select != '') {
            $(select).show();
        }
        return true;
    });
    $('#js_save_template').on('change', function () {
        var oName = $('#js_save_template_name');
        if ($(this).prop('checked')) {
            oName.show();
        } else {
            oName.hide();
        }
    });
    $('#js_set_schedule').off('click').on('click', function () {
        $('#js_schedule_date_time').toggle();
        var oHidden = $('#js_is_schedule');
        if (oHidden.val() == 1) {
            oHidden.val(0);
        } else {
            oHidden.val(1);
        }
        return false;
    });
    $(document).on("change", "#js_ync_web_push_send_notification", function (event) {
        var ele = $(this),
            title = ele.find('#title'),
            message = ele.find('#message'),
            icon_src = ele.find('.icon_image:first').length ? ele.find('.icon_image:first').attr('src') : '',
            photo_src = ele.find('.photo_image:first').length ? ele.find('.photo_image:first').attr('src') : '',
            redirect_url = ele.find('#redirect_url'),
            preview = ele.find('#preview_notification'),
            submit = ele.find('#js_submit'),
            target = $(event.target),
            bCanPreview = true;
        if (!target.is('input') && !target.is('form') || (!title.length && !redirect_url.length)) {
            return false;
        }
        if (yncwebpush_admin.uploadingIcon === false) {
            bCanPreview = false;
        } else if (icon_src.length && yncwebpush_admin.uploadingIcon == '') {
            yncwebpush_admin.uploadingIcon = icon_src;
        }
        if (yncwebpush_admin.uploadingPhoto === false) {
            bCanPreview = false;
        } else if (photo_src.length && yncwebpush_admin.uploadingPhoto == '') {
            yncwebpush_admin.uploadingPhoto = photo_src;
        }
        if (title.val().length && redirect_url.val().length && bCanPreview) {
            preview.removeClass('disabled').removeAttr('disabled');
            submit.removeClass('disabled').removeAttr('disabled');
        } else {
            preview.addClass('disabled').attr('disabled','disabled');
            submit.addClass('disabled').attr('disabled','disabled');
        }
        return false;
    });
    $('#js_ync_web_push_send_notification #icon').on('change', function () {
        yncwebpush_admin.readURL($(this), true);
    });
    $('#js_ync_web_push_send_notification #photo').on('change', function () {
        yncwebpush_admin.readURL($(this));
    });
    setTimeout(function(){
        $('#js_ync_web_push_send_notification #title').trigger('change');
    },1200);
    $('body').on('change', '.js_ync_checkbox_subscribers, .js_ync_checkbox_all_subscribers', function () {
        var aSelectedIds = [],
            sSelected = '',
            oSubs = $('.js_ync_checkbox_subscribers'),
            oSendLink = $('#js_send_selected_link');
        if (!oSubs.length) return false;
        oSubs.each(function () {
            var obj = $(this);
            if (obj.prop('checked')) {
                aSelectedIds.push(obj.val());
            }
        });
        if (aSelectedIds.length) {
            sSelected = aSelectedIds.join(',');
            console.log(sSelected);
        }
        if (sSelected.length) {
            oSendLink.removeAttr('disabled');
        }
        var sHref = oSendLink.data('href'),
            iTemplate = oSendLink.data('template');
        oSendLink.attr('href', sHref + '?send_to=' + sSelected + '&template=' + iTemplate);
    });
});

var yncwebpush_admin = {
    uploadingIcon: '',
    uploadingPhoto: '',
    loadTemplateDetail: function (obj, id) {
        $('#ync-loading').show();
        $('.ync-webpush-template-items').find('.selected').removeClass('selected');
        obj.addClass('selected');
        if (!id) {
            return false;
        }
        obj.ajaxCall('yncwebpush.loadTemplateDetail', 'id=' + id, 'post', null, function () {
            $('#ync-loading').hide();
        });
    },
    selectTemplate: function (obj) {
        var iTemplateId = obj.val();
        obj.ajaxCall('yncwebpush.selectTemplate', 'template_id=' + iTemplateId, 'post', null, function () {
            yncwebpush_admin.uploadingIcon = yncwebpush_admin.uploadingPhoto = '';
            $('#js_ync_web_push_send_notification #title').trigger('change');
        });
    },
    readURL: function (target, is_icon) {
        var input = target[0];
        if (input.files && input.files[0]) {
            if (input.files[0].type.match(/image/g) == null) {
                if (is_icon) {
                    yncwebpush_admin.uploadingIcon = false;
                } else {
                    yncwebpush_admin.uploadingPhoto = false;
                }
                yncwebpush_admin.updatePreviewImage(is_icon);
                return false;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                if (is_icon) {
                    yncwebpush_admin.uploadingIcon = e.target.result;
                } else {
                    yncwebpush_admin.uploadingPhoto = e.target.result;
                }
                $('#js_ync_web_push_send_notification #title').trigger('change');
                yncwebpush_admin.updatePreviewImage(is_icon);
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            if (is_icon) {
                yncwebpush_admin.uploadingIcon = '';
            } else {
                yncwebpush_admin.uploadingPhoto = '';
            }
            yncwebpush_admin.updatePreviewImage(is_icon);
        }
    },
    previewNotification: function () {
        var oForm = $('#js_ync_web_push_send_notification'),
            sTitle = oForm.find('#title').val(),
            sMessage = oForm.find('#message').val(),
            sIcon = yncwebpush_admin.uploadingIcon,
            sPhoto = yncwebpush_admin.uploadingPhoto;
        var replaceStr = function(str) {
            return str.replace(/"/g,'&quot;').replace(/'/g,'&#039;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        };
        var sHtml = '<div class="js_box_holder" style="background: rgba(0,0,0,0.9); z-index: 99999;" onclick="$(this).remove();"><div class="ync-item-notification js_box_holder" style="width: 300px; background: #fff; top:20%; left: 50%; max-height: 310px; z-index: 99999; margin-left: -150px; bottom: auto; overflow: hidden">' +
            '<div class="item-container" style="display: flex; padding: 8px; align-items: center; border-bottom: 1px solid #eee">';
        if (sIcon.length) {
            sHtml += '<div class="item-icon">' +
                '<img src="' + sIcon + '" alt="" style="max-width: 50px"/>' +
                '</div>';
        }
        sHtml += '<div class="item-content" style="margin-left: 12px;">' +
            '<div class="item-title"><strong>' + replaceStr(sTitle) + '</strong></div>' +
            '<div class="item-message">' + replaceStr(sMessage) + '</div>' +
            '<div class="item-host" style="color: #ababab">' + getParam('sJsHostname') + '</div>' +
            '</div>' +
            '</div>';
        if (sPhoto.length) {
            sHtml += '<div class="item-image">' +
                '<span style="display: block; width: 100%; height: 200px;background-size: contain; background-position: center; background-repeat: no-repeat; background-image: url(\'' + sPhoto +
                '\')"></span>';
        }
        sHtml += '</div></div>';
        $('body').append(sHtml);
        return false;
    },
    updatePreviewImage: function (bIcon) {
        var obj = bIcon ? $('#js_ync_web_push_send_notification .icon_image:first') : $('#js_ync_web_push_send_notification .photo_image:first'),
            sSrc = bIcon ? yncwebpush_admin.uploadingIcon : yncwebpush_admin.uploadingPhoto,
            oHolder = bIcon ? $('#js_icon_holder') : $('#js_photo_holder'),
            sWidth = bIcon ? '50px' : '200px',
            sClass = bIcon ? 'icon_image' : 'photo_image';
        if (obj.length) {
            if (sSrc === false) {
                obj.parent().remove();
            } else if (sSrc.length) {
                obj.attr('src', sSrc);
            }
        } else {
            if (sSrc !== false && sSrc.length) {
                oHolder.append('<div style="padding: 5px 0px"><img src="' + sSrc + '" width="' + sWidth + '" class="' + sClass + '"/></div>');
            }
        }
    }
};