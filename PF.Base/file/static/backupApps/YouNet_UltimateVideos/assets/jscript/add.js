var ultimatevideo = {
    changeCustomFieldByCategory: function (iCategoryId) {
        var $video_id = $('#ynuv_videoid'),
            iVideoId = $video_id.length ? $video_id.val() : 0;

        $Core.ajax('ultimatevideo.changeCustomFieldByCategory',
            {
                type: 'POST',
                params:
                    {
                        action: 'changeCustomFieldByCategory'
                        , iCategoryId: iCategoryId
                        , iVideoId: iVideoId
                    },
                success: function (sOutput) {
                    var oOutput = $.parseJSON(sOutput);
                    if (oOutput.status == 'SUCCESS') {
                        $('#ynuv_customfield_category').html(oOutput.content);
                        // add validate each custom field
                        $('#ynuv_customfield_category').find('[data-isrequired="1"]').each(function () {
                            var type = $(this).data('type');
                            switch (type) {
                                case 'text':
                                    $(this).rules('add', {
                                        checkCustomFieldText: true
                                    });
                                    break;
                                case 'textarea':
                                    $(this).rules('add', {
                                        checkCustomFieldTextarea: true
                                    });
                                    break;
                                case 'select':
                                    $(this).rules('add', {
                                        checkCustomFieldSelect: true
                                    });
                                    break;
                                case 'multiselect':
                                    $(this).rules('add', {
                                        checkCustomFieldMultiselect: true
                                    });
                                    break;
                                case 'radio':
                                    $(this).rules('add', {
                                        checkCustomFieldRadio: true
                                    });
                                    break;
                                case 'checkbox':
                                    $(this).rules('add', {
                                        checkCustomFieldCheckbox: true
                                    });
                                    break;
                            }
                        });

                    } else {
                    }
                }
            });
    },
    showValidProcess: function (type) {
        $("#ynuv_add_processing").css('display', 'block');
        $("#ynuv_add_submit").attr('disabled', 'disabled');
        $('#ynuv_add_error_link').css('display', 'none');
        $('#ynuv_add_error_embed').css('display', 'none');
    },
    emptyOldValueWhenChangeSource: function () {
        $('#ynuv_add_video_input_mp4').val('');
        $('#ynuv_add_video_input_link').val('');
        $('#ynuv_add_video_input_embed').val('');
        $("#ynuv_add_submit").attr('disabled', 'disabled');
    },
    hideAllMessage: function () {
        $("#ynuv_add_processing_embed").css('display', 'none');
        $("#ynuv_add_processing").css('display', 'none');
        $("#ynuv_add_submit").attr('disabled', 'disabled');
        $('#ynuv_add_error_link').css('display', 'none');
        $('#ynuv_add_error_embed').css('display', 'none');
    },
    initValidator: function (element) {
        jQuery.validator.messages.required = "This field is required";
        $.data(element[0], 'validator', null);
        element.validate({
            errorPlacement: function (error, element) {
                if (element.is(":radio") || element.is(":checkbox")) {
                    // error.appendTo(element.parent());
                    error.appendTo($(element).closest('.table_right'));
                } else {
                    error.appendTo(element.parent());
                }
            },
            errorClass: 'ultimatevideo-error',
            errorElement: 'span',
            debug: false
        });
    },
    init: function () {
        ultimatevideo.initValidator($('#ynuv_add_video_form'));
        jQuery.validator.addMethod('checkCustomFieldText', function (value, element, params) {
            var result = false;
            if (element.value.length > 0) {
                result = true;
            }
            return result;
        }, 'This field is required');
        jQuery.validator.addMethod('checkCustomFieldTextarea', function (value, element, params) {
            var result = false;
            if ($(element).val().length > 0) {
                result = true;
            }
            return result;
        }, 'This field is required');
        jQuery.validator.addMethod('checkCustomFieldSelect', function (value, element, params) {
            var result = false;
            if ($(element).val().length > 0) {
                result = true;
            }
            return result;
        }, 'This field is required');
        jQuery.validator.addMethod('checkCustomFieldMultiselect', function (value, element, params) {
            var result = false;
            var select = $(element).val();
            if (undefined != select && null != select && select.length > 0) {
                result = true;
            }
            return result;
        }, 'This field is required');
        jQuery.validator.addMethod('checkCustomFieldCheckbox', function (value, element, params) {
            var result = false;
            var name = element.name;
            if ($('input[name="' + name + '"]:checkbox').is(':checked')) {
                result = true;
            }
            return result;
        }, 'This field is required');
        jQuery.validator.addMethod('checkCustomFieldRadio', function (value, element, params) {
            var result = false;
            var name = element.name;
            if ($('input[name="' + name + '"]:radio').is(':checked')) {
                result = true;
            }
            return result;
        }, 'This field is required');
        jQuery.validator.addMethod('checkCategory', function () {
            return true;
        }, 'This field is required');
        // $('#ynuv_add_video_form #ynuv_add_video_title').rules('add', {
        // 	required: true
        // });
        // $('#ynuv_add_video_form #ynuv_section_category.table_right:first #js_mp_id_0').rules('add', {
        // 	checkCategory: true
        // });
    },
    ultimatevideoAddVideo: function () {
        if (!$("#js_ultimatevideo_block_detail").length) return;
        $("#js_ultimatevideo_block_detail").addClass('dont-unbind-children');
        var addVideoInterval;
        addVideoInterval = window.setInterval(function () {
            if (typeof (jQuery.validator) != 'undefined') {
                ultimatevideo.init();
                window.clearInterval(addVideoInterval);
            }

        }, 300);
        if ($('#ynuv_add_video_input_embed').val() == "") {
            $('#ynuv_add_video_input_embed').val("1");
        }
        var $video_categories = $('#video_categories');
        if ($video_categories.length) {
            ultimatevideo.changeCustomFieldByCategory($video_categories.val());
            $video_categories.change(function () {
                var $this = $(this);
                ultimatevideo.changeCustomFieldByCategory($this.val());
            });
        }
        $('#ynav_js_source_video').change(function () {
            ultimatevideo.emptyOldValueWhenChangeSource();
            ultimatevideo.hideAllMessage();
            iSoureVideo = $(this).val();
            switch (iSoureVideo) {
                case 'Youtube':
                case 'Vimeo':
                case 'Dailymotion':
                case 'Facebook':
                    $('#ynuv_add_video_input_link').val('');
                    if ($('#ynuv_add_video_input_embed').val() == "") {
                        $('#ynuv_add_video_input_embed').val("1");
                    }
                    $('#ynav_add_video_link').css('display', 'block');
                    $('#ynav_add_video_embed').css('display', 'none');
                    $('#ynav_add_video_upload').css('display', 'none');
                    $('#ynav_add_video_mp4_url').css('display', 'none');
                    $('#ynuv_help_block_link').css('display', 'block');
                    $('#ynuv_help_block_url').css('display', 'none');
                    break;
                case 'VideoURL':
                    $('#ynuv_add_video_input_link').val('');
                    if ($('#ynuv_add_video_input_embed').val() == "") {
                        $('#ynuv_add_video_input_embed').val("1");
                    }
                    $('#ynav_add_video_link').css('display', 'block');
                    $('#ynav_add_video_embed').css('display', 'none');
                    $('#ynav_add_video_upload').css('display', 'none');
                    $('#ynav_add_video_mp4_url').css('display', 'none');
                    $('#ynuv_help_block_link').css('display', 'none');
                    $('#ynuv_help_block_url').css('display', 'block');
                    break;
                    break;
                case 'Embed':
                    if ($('#ynuv_add_video_input_link').val() == "") {
                        $('#ynuv_add_video_input_link').val("1");
                    }
                    $('#ynuv_add_video_input_embed').val('');
                    $('#ynav_add_video_embed').css('display', 'block');
                    $('#ynav_add_video_upload').css('display', 'none');
                    $('#ynav_add_video_link').css('display', 'none');
                    $('#ynav_add_video_mp4_url').css('display', 'none');
                    break;
                case 'Uploaded':
                    if ($('#ynuv_add_video_input_link').val() == "") {
                        $('#ynuv_add_video_input_link').val("1");
                    }
                    if ($('#ynuv_add_video_input_embed').val() == "") {
                        $('#ynuv_add_video_input_embed').val("1");
                    }
                    if ($('#ynuv_add_video_code').val() == "") {
                        $('#ynuv_add_video_code').val("1");
                    }
                    $('#ynav_add_video_upload').css('display', 'block');
                    $('#ynav_add_video_embed').css('display', 'none');
                    $('#ynav_add_video_link').css('display', 'none');
                    $('#ynav_add_video_mp4_url').css('display', 'none');
                    break;
                default:
                    $('#ynav_add_video_link').css('display', 'none');
                    $('#ynav_add_video_embed').css('display', 'none');
                    $('#ynav_add_video_upload').css('display', 'none');
                    $('#ynav_add_video_mp4_url').css('display', 'none');
                    break;
            }
        });
        //Get video data for Youtube, Vimeo, Dailymotion, Facebook
        $('#ynuv_add_video_input_link').change(function () {
            var url = $(this).val();
            // url = url.replace(/\\|\'|\(\)|\"|$|\#|%|<>/gi, "");
            var type_code = ynultimatevideo_extract_code(url);

            var type = type_code.type;
            var code = type_code.code;

            $("#ynuv_add_video_source").val(type);
            $("#ynuv_add_video_code").val(code);
            $("#ynuv_add_video_url").val(url);
            if (type == 'Embed' || type == 'VideoURL') {
                $("#ynuv_add_submit").removeAttr('disabled');
            } else {
                ultimatevideo.showValidProcess(type);
                $Core.ajax('ultimatevideo.validationUrl',
                    {
                        type: "POST",
                        params:
                            {
                                url: url,
                            },
                        success: function (sOutput) {
                            $("#ynuv_add_processing").css('display', 'none');
                            var oOutput = $.parseJSON(sOutput);
                            if (oOutput.status == "SUCCESS") {
                                $("#ynuv_add_submit").removeAttr('disabled');
                                if (oOutput.title != "")
                                    $('#ynuv_add_video_title').val(oOutput.title);
                                if (oOutput.description != "") {
                                    if (typeof (CKEDITOR) !== 'undefined') {
                                        if (typeof (CKEDITOR.instances["description"]) !== 'undefined') {
                                            var description = oOutput.description.replace(/(\r\n|\n|\r)/gm, '<br>');
                                            CKEDITOR.instances["description"].setData(description);
                                        }
                                    } else {
                                        $('#description').val(oOutput.description);
                                    }
                                }
                            } else {
                                $('#ynuv_add_error_link').html(oOutput.error_message);
                                $('#ynuv_add_error_link').css('display', 'block');
                            }
                        }
                    });
            }
        });
    }
};

var ynultimatevideo_extract_code = function (url) {

    var code = url.match(/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/);
    if (code && code.length > 2) {
        return {
            'type': 'Embed',
            'code': code[3]
        };
    }

    var videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
    if (videoid) {
        return {
            'type': 'Youtube',
            'code': videoid[1]
        };
    }

    videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?vimeo.com\/(ondemand\/)?(.+\/)?(.+)($|\/)/);
    if (videoid) {
        return {
            'type': 'Vimeo',
            'code': videoid[3]
        };
    }

    videoid = url.match(/^.+dailymotion.com\/(video|hub)\/([^_\/\?]+)[^#]*(#video=([^_&]+))?/);
    if (videoid) {
        return {
            'type': 'Dailymotion',
            'code': videoid[2]
        };
    }

    videoid = url.match(/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/);
    if (videoid) {
        return {
            'type': 'Facebook',
            'code': videoid[2]
        };
    }

    var ext = url.substr(url.lastIndexOf('.') + 1);
    if (ext.toUpperCase() === 'MP4') {
        return {
            'type': 'VideoURL',
            'code': url
        };
    }

    return {
        'type': '',
        'code': ''
    };
};



