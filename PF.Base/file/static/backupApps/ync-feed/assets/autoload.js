// Disable core feed js
$Behavior.activityFeedProcess = function () {
};
$Core.forceLoadOnFeed = function () {
};
$Core.loadMoreFeed = function () {
};
$Behavior.checkForNewFeed = function () {
};

var $ynfeedLoadedSetting = false;
var $sFormAjaxRequest = null;
var $bButtonSubmitActive = true;
if (typeof $ActivityFeedCompleted === 'undefined') {
  var $ActivityFeedCompleted = {};
}
var $sCurrentSectionDefaultPhrase = null;
var $sCssHeight = 'auto';
var $sCustomPhrase = null;
var $sCurrentForm = null;
var $sStatusUpdateValue = null;
var $iReloadIteration = 0;
var $oLastFormSubmit = null;
var bCheckUrlCheck = false;
var bCheckUrlForceAdd = false;
var bAddingFeed = false;
var $sCacheFeedErrorMessage = [];
var $checkForNewFeedInterval = null;

if (typeof $Core.Photo !== 'undefined') {
  $Core.Photo.dropzoneOnCompleteInFeed = function () {
    if ($Core.Photo.sAjax && $Core.Photo.aPhotos.length > 0) {
      let isEditPhoto = $('.ynfeed_form_edit').closest('.js_box_content').length;
      let tempPhotos = $Core.Photo.aPhotos;
      var ajax = $Core.Photo.sAjax + '&photos=' + JSON.stringify($Core.Photo.aPhotos);

      $.fn.ajaxCall('ynfeed.processFeedPhoto', ajax, true, 'POST', function () {
        $Core.Photo.dropzoneOnFinishInFeed();
        if (isEditPhoto) {
          let photoIds = [];
          $.each(tempPhotos, function (key, value) {
            photoIds.push(value['photo_id']);
          });
          let form = $('.ynfeed_form_edit').closest('.js_box_content').find('form:first');
          let ajaxParams = form.serialize() + '&uploaded_photos=' + JSON.stringify(photoIds);
          $.ajaxCall(form.parents('.ynfeed_form_edit').find('.activity_feed_link_form_ajax').text(), ajaxParams);
        }
      });
      $Core.Photo.sAjax = '';
      $Core.Photo.aPhotos = [];
    }
  };
}

$Core.Like = {};
$Core.Like.Actions = {
  doLike: function (bIsCustom, sItemTypeId, iItemId, iParentId, oObj) {
    if ($(oObj).closest('.comment_mini_link_like').find('.like_action_unmarked').is(':visible')) {
      $(oObj).closest('.comment_mini_link_like').find('.like_action_marked').show();
      $(oObj).closest('.comment_mini_link_like').find('.like_action_unmarked').hide();
    }
    $(oObj).parent().find('.js_like_link_unlike:first').show();
    $(oObj).hide();
    $.ajaxCall('like.add', 'type_id=' + sItemTypeId + '&item_id=' + iItemId + '&parent_id=' + iParentId + '&custom_inline=' + bIsCustom, 'GET');
  }
};

$Core.isInView = function (elem, item) {
  if (!$Core.exists(elem)) {
    return false;
  }

  var docViewTop = $(window).scrollTop();
  var docViewBottom = docViewTop + $(window).height();

  var elemTop = $(elem).offset().top;
  var elemBottom = (elemTop + $(elem).height());
  if (item) {
    elemBottom = (elemBottom - parseInt(item));
  }

  return ((docViewTop < elemTop) && (docViewBottom > elemBottom));
};

$Core.ynfeedResetActivityFeedForm = function () {
  if ($sCacheFeedErrorMessage.length > 0) {
    $('.activity_feed_form_share_process').hide();
    $('.activity_feed_form_button .button').removeClass('button_not_active');
    $('#activity_feed_upload_error').empty();
    $bButtonSubmitActive = true;
    $sCacheFeedErrorMessage.forEach(function (item, index) {
      $('#activity_feed_upload_error').append('<div class="error_message">' + item + '</div>').show();
    });

    $sCacheFeedErrorMessage = [];
  }
  else {
    bAddingFeed = false;
    $('._load_is_feed').removeClass('active');
    $('#panel').hide();
    $('body').removeClass('panel_is_active');

    $('.activity_feed_form_attach li a').removeClass('active');
    $('.activity_feed_form_attach li:not(.share) a:first').addClass('active');
    $('.global_attachment_holder_section').hide();
    $('#global_attachment_status').show();
    $('.ynfeed_compose_status .contenteditable').html('');
    $('#ynfeed_status_content').val('');

    $('.activity_feed_form_button_status_info').hide();
    $('.activity_feed_form_button_status_info textarea').val('');
    $('.ynfeed_highlighter').html('');

    $Core.resetActivityFeedErrorMessage();

    $sFormAjaxRequest = $('.activity_feed_form_attach li a.active').find('.activity_feed_link_form_ajax').html();

    $Core.activityFeedProcess(false);

    $('.js_share_connection').val('0');
    $('.feed_share_on_item a').removeClass('active');

    $.each($ActivityFeedCompleted, function () {
      this(this);
    });

    $('#js_add_location, #js_location_input, #js_location_feedback').hide("fast");
    $('#hdn_location_name, #ynfeed_val_location_name ,#ynfeed_val_location_latlng').val('');
    $('#btn_ynfeed_display_check_in').removeClass('is_active');

    $Core.ynfeedCheckin.cancelCheckin();
    $Core.ynfeedTagging.cancelTagging();

    $Core.ynfeedFeeling.cancelFeeling();
    $Core.ynfeedBusiness.cancelBusiness();
    $('.activity_feed_form_button_position a[type=button]').removeClass('is_active');
    $Core.ynfeed.removeFocusForm();
  }
};

$Core.resetActivityFeedErrorMessage = function () {
  $('#activity_feed_upload_error').hide();
  $('#activity_feed_upload_error_message').html('');
};

$Core.resetActivityFeedError = function (sMsg) {
  $('.activity_feed_form_share_process').hide();
  $('.activity_feed_form_button .button').removeClass('button_not_active');
  $bButtonSubmitActive = true;
  $('#activity_feed_upload_error').append('<div class="error_message">' + sMsg + '</div>').show();
};

$Core.cacheActivityFeedError = function (sMsg) {
  $sCacheFeedErrorMessage.push(sMsg);
};


$Core.addNewPollOption = function (iMaxAnswers, sLimitReached) {
  if (iMaxAnswers >= ($('#js_poll_feed_answer li').length + 1)) {
    $('.js_poll_feed_answer').append('<li><input type="text" name="val[answer][][answer]" value="" size="30" class="js_feed_poll_answer v_middle" /></li>');
  }
  else {
    alert(oTranslations['you_have_reached_your_limit']);
  }

  return false;
};

$Core.ynfeedForceLoadOnFeed = function () {
  if (bAutoloadFeed == '0')
    return;

  if (!$Core.exists('#js_feed_pass_info')) {
    return;
  }

  $iReloadIteration++;
  $('#feed_view_more_loader').show();
  $('.global_view_more').remove();

  setTimeout(function () {
    $Core.ynfeedLoadMoreFeed($iReloadIteration);
  }, 1000);
};


$.urlParam = function (name) {
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  if (results == null) {
    return null;
  }
  else {
    return results[1] || 0;
  }
};
$Core.ynfeedLoadMoreFeed = function (iReloadIteration) {
  var oLastFeed = $('.js_parent_feed_entry').last();
  var iLastFeedId = (oLastFeed) ? oLastFeed.attr('data-feed-id') : null;
  $.ajaxCall('ynfeed.viewMore', $('#js_feed_pass_info').html().replace(/&amp;/g, '&') + '&iteration="' + iReloadIteration + '"&last-feed-id=' + iLastFeedId + '&filter-id=' + ynfeed_filter_id + '&filter-module=' + ynfeed_filter_module + '&filter-type=' + ynfeed_filter_type, 'GET');
};

var postingFeedUrl = false;


/**
 * Editor on comments
 */
$Core.loadCommentButton = function () {
  $('.feed_comment_buttons_wrap div input.button_set_off').show().removeClass('button_set_off');
};

var __ = function (e) {
  $('.feed_stream[data-feed-url="' + e.url + '"]').replaceWith(e.content);
  $Core.loadInit();
};

$Core.resetFeedForm = function (f) {
  f.get()[0].reset();
  $('.feed_form_share').removeClass('.active');
  $('.feed_form_textarea textarea').removeClass('dont-unbind');
};

var load_feed_entries = false;
var load_feed_action = function () {
  var total = $('.feed_stream:not(.built)').length, iteration = 0;
  $('.feed_stream:not(.built)').each(function () {
    var t = $(this);

    t.addClass('built');
    iteration++;
    if (iteration === 2) {
      return false;
    }

    var s = document.createElement('script');
    s.type = 'application/javascript';
    s.src = t.data('feed-url');
    document.head.appendChild(s);
  });
};

$Behavior.ynfeedActivityFeedProcess = function () {
  $('.comment-limit:not(.is_checked)').each(function () {
    var t = $(this);
    t.addClass('is_checked');
    var total = t.children('.js_mini_feed_comment').length;
    var limit = t.data('limit');
    var iteration = total;
    var totalHidden = 0;
    t.children('.js_mini_feed_comment').each(function () {
      var l = $(this);
      iteration--;
      if (iteration < limit) {
        return false;
      }

      totalHidden++;
      l.addClass('hidden');
    });

    if (totalHidden) {
      var cHolder = t.parent().find('.comment_pager_holder:first');
      cHolder.hide();
      var viewMore = $('<a href="#" class="load_more_comments dont-unbind">' + oTranslations['view_previous_comments'] + '</a>');
      cHolder.before(viewMore);
      viewMore.click(function () {
        t.find('.js_mini_feed_comment').removeClass('hidden');
        $(this).remove();
        cHolder.show();
        return false;
      });
    }
  });


  $('.comment_mini_link_like_empty').each(function () {
    var p = $(this).closest('.comment_mini_content_border');
  });

  if ($Core.exists('.global_view_more')) {
    if ($Core.isInView('.global_view_more')) {
      $Core.ynfeedForceLoadOnFeed();
    }
    $(window).scroll(function () {
      if ($Core.isInView('.global_view_more')) {
        $Core.ynfeedForceLoadOnFeed();
      }
    });
  }

  $('.like_count_link').each(function () {
    var sHtml = $(this).parent().find('.like_count_link_holder:first').html();
  });

  $sFormAjaxRequest = $('.activity_feed_form_attach li a.active').find('.activity_feed_link_form_ajax').html();
  if (typeof Plugin_sFormAjaxRequest == 'function') {
    Plugin_sFormAjaxRequest();
  }

  if ($Core.exists('.profile_timeline_header')) {
    $(window).scroll(function () {
      if (isScrolledIntoView('.profile_timeline_header')) {
        $('.timeline_main_menu').removeClass('timeline_main_menu_fixed');
        $('#timeline_dates').removeClass('timeline_dates_fixed');
      }
      else {
        if (!$('.timeline_main_menu').hasClass('timeline_main_menu_fixed')) {
          $('.timeline_main_menu').addClass('timeline_main_menu_fixed');

          if ($('#content').height() > 600) {
            $('#timeline_dates').addClass('timeline_dates_fixed');
          }
        }
      }
    });
  }

  $('#js_activity_feed_form, #js_activity_feed_edit_form').off('submit').on('submit', function () {
    if ($sCurrentForm == 'global_attachment_status' || $sCurrentForm == null) {
      var oStatusUpdateTextareaFilled = $('#global_attachment_status .ynfeed_compose_status .contenteditable');
      if ($sStatusUpdateValue == oStatusUpdateTextareaFilled.val()) {
      }
    }
    else {
      var oCustomTextareaFilled = $('.activity_feed_form_button_status_info .ynfeed_compose_status .contenteditable');
      if ($sCustomPhrase == oCustomTextareaFilled.val()) {
        oCustomTextareaFilled.val('');
      }
    }
    $Core.ynfeed.updateFormValue(this);

    /*edit post*/
    if ($(this).parents('.ynfeed_form_edit').length) {
      if (!($Core.dropzone.instance['photo_feed'].files.length && $Core.dropzone.instance['photo_feed'].getAcceptedFiles().length)) {
        $(this).ajaxCall($(this).parents('.ynfeed_form_edit').find('.activity_feed_link_form_ajax').text(), sExtra);
      }
    } /*add post*/
    else {
      if ($bButtonSubmitActive === false) {
        return false;
      }
      $Core.activityFeedProcess(true);

      if (typeof $sFormAjaxRequest === 'undefined' || $sFormAjaxRequest === null) {
        return true;
      }
      $('.js_no_feed_to_show').remove();
      var sExtra = '';
      if (bCheckUrlForceAdd) {
        $sFormAjaxRequest = 'ynfeed.addLinkViaStatusUpdate';
        if ($('#js_activity_feed_edit_form').length > 0) {
          sExtra = 'force_form=1';
        }
      }
      bAddingFeed = true;
      $(this).ajaxCall($sFormAjaxRequest, sExtra);

      if (bCheckUrlForceAdd) {
        $('#js_preview_link_attachment_custom_form_sub').remove();
        bCheckUrlForceAdd = false;
      }
    }
    $Core.ynfeed.removeFocusForm(1);
    return false;
  });

  $('.ynfeed-bg-focus,.js_ynf_form_feed_close_btn').off('click').on("click", function () {
    $('.activity_feed_form_attach li a.global_attachment_status').click();
    setTimeout(function () {
      $Core.ynfeed.removeFocusForm();
    }, 300);
  });
  $(window).scroll(function (event) {
    //auto remove focus when scroll
    if ($('#ynfeed_form_share_holder').length > 0) {
      var rmCheckScroll = $(window).scrollTop();
      var rmCheckHeight = $('#ynfeed_form_share_holder').outerHeight() + $('#ynfeed_form_share_holder').offset().top;
      if (rmCheckScroll > rmCheckHeight) {
        if ($('body').hasClass('ynfeed-form-focus')) {
          $('.activity_feed_form_attach li a.global_attachment_status').click();
          $Core.ynfeed.removeFocusForm();
        }
      }
    }

  });
  $('.ynfeed_activity_feed_form , #ynfeed_form_share_holder .ynfeed-form-button-box-wrapper >div a, .ynfeed-form-btn-activity-viewmore').click(function () {
    if ($('body').hasClass('ynfeed-form-focus')) {
      return;
    }
    $('.contenteditable').focus();
  });
  $('.activity_feed_form_attach li a').click(function () {
    $('body').addClass('ynfeed-form-focus');
    $('.activity_feed_form:not(.ynfeed_detached) .activity_feed_form_button').show();
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_select_emojis').show();
    $sCurrentForm = $(this).attr('rel');
    if ($sCurrentForm == 'custom') {
      return false;
    }

    if ($sCurrentForm == 'view_more_link') {

      $('.view_more_drop').toggle();

      return false;
    }
    else {
      $('.view_more_drop').hide();
    }

    $('#js_preview_link_attachment_custom_form_sub').remove();
    $('#activity_feed_upload_error').hide();

    $('.global_attachment_holder_section').hide();
    $('.activity_feed_form_attach li a').removeClass('active');
    $(this).addClass('active');

    if ($(this).find('.activity_feed_link_form').length > 0) {
      $('#js_activity_feed_form').attr('action', $(this).find('.activity_feed_link_form').html()).attr('target', 'js_activity_feed_iframe_loader');
      $sFormAjaxRequest = null;
      if (empty($('.activity_feed_form_iframe').html())) {
        $('.activity_feed_form_iframe').html('<iframe id="js_activity_feed_iframe_loader" name="js_activity_feed_iframe_loader" height="200" width="500" frameborder="1" style="display:none;"></iframe>');
      }
    }
    else {
      $sFormAjaxRequest = $(this).find('.activity_feed_link_form_ajax').html();
    }

    $('#' + $(this).attr('rel')).show();
    $('.activity_feed_form_holder_attach').show();
    $('.activity_feed_form_button').show();

    var $oStatusUpdateTextarea = $('#global_attachment_status .contenteditable');
    var $sStatusUpdateTextarea = $oStatusUpdateTextarea.html();
    $sStatusUpdateValue = $('#global_attachment_status_value').html();

    var $oCustomTextarea = $('.activity_feed_form_button_status_info .contenteditable');
    var $sCustomTextarea = $oCustomTextarea.html();

    $sCustomPhrase = $(this).find('.activity_feed_extra_info').html();

    var $bHasDefaultValue = false;
    $('.activity_feed_extra_info').each(function () {
      if ($(this).html() == $sCustomTextarea) {
        $bHasDefaultValue = true;

        return false;
      }
    });

    if ($(this).attr('rel') != 'global_attachment_status') {
      $('.activity_feed_form_button_status_info').show();
      $('.core-egift-wrapper').hide();
      if ((empty($sCustomTextarea) && ($sStatusUpdateTextarea == $sStatusUpdateValue
        || empty($sStatusUpdateTextarea)))
        || ($sStatusUpdateTextarea == $sStatusUpdateValue && $bHasDefaultValue)
        || (!$bButtonSubmitActive && $bHasDefaultValue)
      ) {
        $oCustomTextarea.css({height: $sCssHeight}).html('').prop({placeholder: $sCustomPhrase});
      }
      else if ($sStatusUpdateTextarea != $sStatusUpdateValue && $bButtonSubmitActive && !empty($sStatusUpdateTextarea)) {
        $oCustomTextarea.html($sStatusUpdateTextarea);
      }

      if ($sCurrentForm == 'global_attachment_photo' && $('#global_attachment_photo .dz-image-preview').length) {
        $('.activity_feed_form_button .button').removeClass('button_not_active');
      }
      else {
        $('.activity_feed_form_button .button').addClass('button_not_active');
      }

      $bButtonSubmitActive = false;
    }
    else {
      $('.activity_feed_form_button_status_info').hide();
      $('.activity_feed_form_button .button').removeClass('button_not_active');
      $('.core-egift-wrapper').show();
      if (!$bHasDefaultValue && !empty($sCustomTextarea)) {
        $oStatusUpdateTextarea.html($sCustomTextarea);
      }
      else if ($bHasDefaultValue && empty($sStatusUpdateTextarea)) {
        $oStatusUpdateTextarea.html($sStatusUpdateValue).css({height: $sCssHeight});
      }

      $bButtonSubmitActive = true;
    }

    if ($(this).hasClass('no_text_input')) {
      $('.activity_feed_form_button_status_info').hide();
    }

    $('.activity_feed_form_button .button').show();
    $('#js_piccup_upload').hide();
    return false;
  });
};

$Behavior.ynfeedActivityFeedLoader = function () {
  if (empty($('.view_more_drop').html())) {
    $('.timeline_view_more').parent().hide();
  }

  /**
   * Click on adding a new comment link.
   */
  $('.js_feed_entry_add_comment').click(function () {
    $('.js_comment_feed_textarea').each(function () {
      if ($(this).val() == $('.js_comment_feed_value').html()) {
        $(this).removeClass('js_comment_feed_textarea_focus');
        $(this).val($('.js_comment_feed_value').html());
      }

      $(this).parents('.comment_mini').find('.feed_comment_buttons_wrap').hide();
    });

    $(this).parents('.js_parent_feed_entry:first').find('.comment_mini_content_holder').show();
    $(this).parents('.js_parent_feed_entry:first').find('.feed_comment_buttons_wrap').show();

    if ($(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea').val() == $('.js_comment_feed_value').html()) {
      $(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea').val('');
    }
    $(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea').focus().addClass('js_comment_feed_textarea_focus');
    $(this).parents('.js_parent_feed_entry:first').find('.comment_mini_textarea_holder').addClass('comment_mini_content');

    var iTotalComments = 0;
    $(this).parents('.js_parent_feed_entry:first').find('.js_mini_feed_comment').each(function () {
      iTotalComments++;
    });

    if (iTotalComments > 2) {
      $.scrollTo($(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea_browse:first'), 340);
    }

    return false;
  });

  /**
   * Comment textarea on focus.
   */
  $('.js_comment_feed_textarea').focus(function () {
    $Core.commentFeedTextareaClick(this);
  });

  $('#js_captcha_load_for_check_submit').submit(function () {

    if (function_exists('' + Editor.sEditor + '_wysiwyg_feed_comment_form')) {
      eval('' + Editor.sEditor + '_wysiwyg_feed_comment_form(this);');
    }

    $oLastFormSubmit.parent().parent().find('.js_feed_comment_process_form:first').show();
    $(this).ajaxCall('comment.add', $oLastFormSubmit.getForm());
    isAddingComment = false;
    return false;
  });

  $('.js_comment_feed_form').unbind().submit(function () {
    var t = $(this);

    t.addClass('in_process');

    if ($Core.exists('#js_captcha_load_for_check')) {
      $('#js_captcha_load_for_check').css({
        top: getPageScroll()[1] + (getPageHeight() / 5),
        left: '50%',
        'margin-left': '-' + (($('#js_captcha_load_for_check').width() / 2) + 12) + 'px',
        display: 'block'
      });

      $oLastFormSubmit = $(this);

      return false;
    }

    if (function_exists('' + Editor.sEditor + '_wysiwyg_feed_comment_form')) {
      eval('' + Editor.sEditor + '_wysiwyg_feed_comment_form(this);');
    }

    $(this).parent().parent().find('.js_feed_comment_process_form:first').show();
    $(this).ajaxCall('comment.add', null, null, null, function (e, self) {
      $(self).find('textarea').blur();
      isAddingComment = false;
      $('.js_feed_comment_process_form').fadeOut();
    });

    $(this).find('.error_message').remove();
    $(this).find('textarea:first').removeClass('dont-unbind');

    return false;
  });

  $('.js_comment_feed_new_reply').click(function () {

    var oParent = $(this).parents('.js_mini_feed_comment:first').children('.js_comment_form_holder:first');
    var oGrand = oParent.parent();
    oParent.detach().appendTo(oGrand);

    if ((Editor.sEditor == 'tiny_mce' || Editor.sEditor == 'tinymce') && isset(tinyMCE) && isset(tinyMCE.activeEditor)) {
      $('.js_comment_feed_form').find('.js_feed_comment_parent_id:first').val($(this).attr('rel'));
      tinyMCE.activeEditor.focus();
      if (typeof($.scrollTo) == 'function') {
        $.scrollTo('.js_comment_feed_form', 800);
      }
      return false;
    }

    var sCommentForm = $(this).parents('.js_feed_comment_border:first').find('.js_feed_comment_form:first').html();
    oParent.html(sCommentForm);
    oParent.find('.js_feed_comment_parent_id:first').val($(this).attr('rel'));

    oParent.find('.js_comment_feed_textarea:first').focus();
    oParent.find('.js_comment_feed_textarea:first').attr('placeholder', oTranslations['write_a_reply']);
    $Core.commentFeedTextareaClick(oParent.find('.js_comment_feed_textarea:first'));

    $('.js_feed_add_comment_button .error_message').remove();

    oParent.find('.button_set_off:first').show().removeClass('button_set_off');

    $Core.loadInit();
    /*$Behavior.activityFeedLoader();*/

    return false;
  });
};

var isAddingComment = false;
$Core.commentFeedTextareaClick = function ($oObj) {
  $($oObj).addClass('dont-unbind');
  $($oObj).blur(function () {
    $(this).removeClass('dont-unbind');
  });
  $($oObj).keydown(function (e) {
    if (isAddingComment) {
      return false;
    }
    if (e.which == 13) {

      e.preventDefault();
      $($oObj).parents('form:first').trigger('submit');
      $($oObj).removeClass('dont-unbind');
      $Core.loadInit();
      isAddingComment = true;

      return false;
    }

    if ($(this).hasClass('no_resize_textarea')) {
      return null;
    }
    $Core.resizeTextarea($(this));
  });

  $($oObj).addClass('js_comment_feed_textarea_focus').addClass('is_focus');
  $($oObj).parents('.comment_mini').find('.feed_comment_buttons_wrap:first').show();

  $($oObj).parent().parent().find('.comment_mini_textarea_holder:first').addClass('comment_mini_content');
};

$Behavior.ynfeedActivityFeedAttachLink = function () {
  $('#js_global_attach_link').click(function () {
    $Core.activityFeedProcess(true);

    $Core.ajax('link.preview',
      {
        params: {
          'no_page_update': '1',
          value: $('#js_global_attach_value').val()
        },
        type: 'POST',
        success: function ($sOutput) {
          $('#js_global_attachment_link_cancel').show();

          if (substr($sOutput, 0, 1) == '{') {
            var $oOutput = $.parseJSON($sOutput);
            $Core.resetActivityFeedError($oOutput['error']);
            $bButtonSubmitActive = false;
            $('.activity_feed_form_button .button').addClass('button_not_active');
          }
          else {
            $Core.activityFeedProcess(false);

            $('#js_preview_link_attachment').html($sOutput);
            $('#global_attachment_link_holder').hide();
          }
        }
      });
  });

  $('#js_global_attachment_link_cancel').click(function () {
    $('#js_global_attachment_link_cancel').hide();
  });
};

$ActivityFeedCompleted.link = function () {
  $bButtonSubmitActive = true;

  $('#global_attachment_link_holder').show();
  $('.activity_feed_form_button .button').removeClass('button_not_active');
  $('#js_preview_link_attachment').html('');
  $('#js_global_attach_value').val('http://');
};

$ActivityFeedCompleted.photo = function () {
  $bButtonSubmitActive = true;

  $('#global_attachment_photo_file_input').val('');
};

$ActivityFeedCompleted.privacy = function () {
  var $form = $('#ynfeed_form_share_holder'),
    $privacyHolder = $form.find('.privacy_setting_div'),
    $firstPrivacyItem = $privacyHolder.find('a[data-toggle="privacy_item"]').first(),
    $button = $privacyHolder.find('[data-toggle="dropdown"]')
  ;

  $form.find('#privacy').val(0);
  $privacyHolder.find('a[data-toggle="privacy_item"]').removeClass('is_active_image').first().addClass('is_active_image');
  $button.find('span.txt-label').text($firstPrivacyItem.html());
  $privacyHolder.find('.fa.fa-privacy').replaceWith($('<i/>', {class: 'fa fa-privacy fa-privacy-0'}));
};

var sToReplace = '', sInputAfterCursor = '', ynfeedBuildingCache = false;

function ynfeedAttachFunctionTagger(sSelector) {
  if ($(sSelector).length && !ynfeedBuildingCache) {
    ynfeedBuildingCache = true;
    if (typeof $Cache.friends == 'undefined')
      $.ajaxCall('friend.buildCache', '', 'GET');
    if (typeof $Cache.businesses == 'undefined')
      $.ajaxCall('ynfeed.buildBusinessCache', '', 'GET');
    if (typeof $Cache.feelings == 'undefined')
      $.ajaxCall('ynfeed.buildFeelingCache', '', 'GET');
    if (typeof $Cache.pages == 'undefined')
      $.ajaxCall('ynfeed.buildFeelingCache', '', 'GET');
    if (typeof $Cache.mentions == 'undefined')
      $.ajaxCall('ynfeed.buildMentionCache', '', 'GET');
  }
  var customSelector = function () {
    return '_' + Math.random().toString(36).substr(2, 9);
  };
  var increment = 0;
  $(sSelector).each(function () {
    increment++;
    var t = $(this), selector = '_custom_' + customSelector() + '_' + increment;
    if (t.data('selector')) {
      t.removeClass(t.data('selector').replace('.', ''));
    }
    t.addClass(selector);
    t.data('selector', '.' + selector);
  });

  $(sSelector).keyup(function () {
    var t = $(this);
    var sInput = t.val();
    var iInputLength = sInput.length;
    var iAtSymbol = sInput.lastIndexOf('@');
    if (sInput == '@' || empty(sInput) || iAtSymbol < 0 || iAtSymbol == (iInputLength - 1)) {
      $($(this).data('selector')).siblings('.chooseFriend').hide(function () {

        $(this).remove();
      });
      return;
    }

    var sNameToFind = sInput.substring(iAtSymbol + 1, iInputLength);

    /* loop through friends */
    var aFoundFriends = [], sOut = '';
    for (var i in $Cache.friends) {
      if ($Cache.friends[i]['full_name'].toLowerCase().indexOf(sNameToFind.toLowerCase()) >= 0) {
        var sNewInput = sInput.substr(0, iAtSymbol).replace(/\'/g, '&#39;').replace(/\"/g, '&#34;');
        sToReplace = sNewInput;
        aFoundFriends.push({
          user_id: $Cache.friends[i]['user_id'],
          full_name: $Cache.friends[i]['full_name'],
          user_image: $Cache.friends[i]['user_image']
        });
        if (($Cache.friends[i]['user_image'].substr(0, 5) == 'http:') || ($Cache.friends[i]['user_image'].substr(0, 6) == 'https:')) {
          PF.event.trigger('urer_image_url', $Cache.friends[i]);

          $Cache.friends[i]['user_image'] = '<img src="' + $Cache.friends[i]['user_image'] + '" class="_image_32 image_deferred">';
        }

        sOut += '<div class="tagFriendChooser" onclick="$(\'' + $(this).data('selector') + '\').val(sToReplace + \'\' + (false ? \'@' + $Cache.friends[i]['user_name'] + '\' : \'[user=' + $Cache.friends[i]['user_id'] + ']' + $Cache.friends[i]['full_name'].replace(/\&#039;/g, '\\\'') + '[/user]\') + \' \').putCursorAtEnd();$(\'' + $(this).data('selector') + '\').siblings(\'.chooseFriend\').remove();"><div class="tagFriendChooserImage">' + $Cache.friends[i]['user_image'] + '</div><span>' + $Cache.friends[i]['full_name'] + '</span></div>';
        /* just delete the fancy choose your friend and recreate it */
        sOut = sOut.replace("\n", '').replace("\r", '');
      }
    }

    $($(this).data('selector')).siblings('.chooseFriend').remove();
    if (!empty(sOut)) {
      $($(this).data('selector')).after('<div class="chooseFriend" style="width: ' + $(this).parent().width() + 'px;">' + sOut + '</div>');
    }
  });
}


$Behavior.ynfeedTagger = function () {
  var selectors = '#js_activity_feed_form > .activity_feed_form_holder > #global_attachment_status > .ynfeed_compose_status > div.contenteditable, .js_comment_feed_textarea, .js_comment_feed_textarea_focus';
  ynfeedAttachFunctionTagger(selectors);
};

$Core.ynfeedCheckNewFeedAfter = function (aFeedIds) {
  var iNewCounter = 0;
  for (var i = 0; i < aFeedIds.length; ++i) {
    if ($('#js_item_feed_' + aFeedIds[i]).length) continue;
    iNewCounter++;
  }
  if (!iNewCounter) return;

  // update number
  var btn = $('#feed_check_new_count_link');
  btn.text(btn.text().replace(/\d+/, iNewCounter));

  $('#feed_check_new_count').removeClass('hide');
};

$Behavior.ynfeedCheckForNewFeed = function () {
  if (typeof window.isRegisteredCheckForNewFeed != 'undefined' || typeof window.$iCheckForNewFeedsTime === 'undefined')
    return;

  window.isRegisteredCheckForNewFeed = true;
  var iCheckForNewFeedsTime = parseInt(window.$iCheckForNewFeedsTime) * 1000;

  function _isHomePage() {
    return !!$('body#page_core_index-member #js_feed_content').length
      && !$('#sHashTagValue').length;
  }

  function _getLastFeedUpdate() {
    //jquery .data() may cached in some case, so we can't use it.
    var val = $('#js_feed_content .js_parent_feed_entry:not(".sponsor"):first').attr('data-feed-update');
    return val ? val : 0;
  }

  function _checkForNewFeed() {
    var $ele = $('#js_feed_content');

    if (bAddingFeed == true) return;
    if (!_isHomePage()) return;

    if ($ele.data('loading'))
      return;

    $ele.data('loading', true);
    $.ajaxCall('ynfeed.checkNew', 'iLastFeedUpdate=' + _getLastFeedUpdate() + '&filter-id=' + ynfeed_filter_id + '&filter-module=' + ynfeed_filter_module + '&filter-type=' + ynfeed_filter_type)
      .always(function () {
        $ele.data('loading', false);
      }).done(function () {

    });
  }

  if ($checkForNewFeedInterval)
    window.clearInterval($checkForNewFeedInterval);
  $checkForNewFeedInterval = window.setInterval(_checkForNewFeed, iCheckForNewFeedsTime);

  window.ynfeedloadNewFeeds = function () {
    $('#js_new_feed_update').html('');
    $.ajaxCall('ynfeed.loadNew', 'iLastFeedUpdate=' + _getLastFeedUpdate() + '&filter-id=' + ynfeed_filter_id + '&filter-module=' + ynfeed_filter_module + '&filter-type=' + ynfeed_filter_type);
  };
  window.ynfeedCheckNewReturn = function (filterModule, filterType, html64) {
    if ((filterModule === ynfeed_filter_module) && (filterType === ynfeed_filter_type)) {
      var html = atob(html64);
      $('#js_new_feed_update').html(html);
      $Core.loadInit();
    }
  }
};

$Core.ynfeedCheckin = {
  bGoogleReady: false,
  sIPInfoDbKey: '',
  sGoogleKey: '',
  cancelCheckin: function () {
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_val_location_latlng').val('');
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_val_location_name').val('');
    $('.activity_feed_form:not(.ynfeed_detached) #hdn_location_name').val('').focus();
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_checkin').html('').hide();
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_btn_delete_checkin').hide();
    $('#btn_ynfeed_display_check_in').removeClass('has_data');
  },
  initMap: function (ele) {
    if ($(ele).hasClass('ynfeed_map_generated'))
      return;
    var latlng = {lat: $(ele).data('lat'), lng: $(ele).data("lng")};
    var map = new google.maps.Map(ele, {
      zoom: 13,
      center: latlng
    });
    var marker = new google.maps.Marker({
      position: latlng,
      map: map
    });
    $(ele).addClass('ynfeed_map_generated');
  },
  googleReady: function () {
    if ($Core.ynfeedCheckin.bGoogleReady) {
      return false;
    }
    if ((typeof google !== 'undefined') && (typeof google.maps !== 'undefined')) {
      $Core.ynfeedCheckin.bGoogleReady = true;
      return false;
    }
    sAddr = 'http://';
    if (window.location.protocol == "https:") {
      sAddr = 'https://';
    }
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = sAddr + "maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=" + $Core.ynfeed.setting.sGoogleKey;
    document.body.appendChild(script);
    $Core.ynfeedCheckin.bGoogleReady = true;
  }
};

$Core.ynfeedTagging = {
  cancelTagging: function () {
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra.ynfeed_compose_tagging input').val('').trigger('change');
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra.ynfeed_compose_tagging .ynfeed_tagged_items').html('');
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_tagged').html('');
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra.ynfeed_compose_tagging').hide("fast");
  }
};
$Core.ynfeedFeeling = {
  cancelFeeling: function () {
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling input').val('').trigger('change');
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling .ynfeed_selected_feeling').html('');
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_feeling').html('');
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling').hide("fast");
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_feeling').show('fast');
  }
};

$Core.ynfeedBusiness = {
  cancelBusiness: function () {
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_business').val('').trigger('change');
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business .ynfeed_tagged_items').html('');
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_business').html('');
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business').hide("fast");
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_business').show('fast');
  }
};

$Core.ynfeedEmoticon = {
  init: function (ele) {
    $(ele).find('.ynfeed_emojis_popup').on({
      "click": function (e) {
        e.stopPropagation();
      }
    });
  },
  showEmojiTitle: function (title, code) {
    $('.ynfeed_emoji_title').html(title);
    $('.ynfeed_emoji_code').html(code);
  },
  selectEmoji: function (e) {
    var composeElem = $(e).closest('.ynfeed_compose_status').find('.contenteditable');
    composeElem.append($(e).find('img').clone().addClass('ynfeed_content_emoji'));
    //prevent firefox rezize emoji
    if ($.browser.mozilla) {
      document.execCommand('enableObjectResizing', false, 'false');
    }
    ;
    //end fix
    $Core.ynfeed.updateFormValue($(e).closest('form'));
    composeElem.trigger('mouseup');

    this.moveCursorToEnd(composeElem);
  },
  moveCursorToEnd: function(ele) {
    let domEle = $(ele)[0];
    let range, selection;
    if (document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
    {
      range = document.createRange();//Create a range (a range is a like the selection but invisible)
      range.selectNodeContents(domEle);//Select the entire contents of the element with the range
      range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
      selection = window.getSelection();//get the selection object (allows you to change selection)
      selection.removeAllRanges();//remove any selections already made
      selection.addRange(range);//make the range you have just created the visible selection
    } else if (document.selection)//IE 8 and lower
    {
      range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
      range.moveToElementText(domEle);//Select the entire contents of the element with the range
      range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
      range.select();//Select the range (make it the visible selection
    }
  }
};
$Core.ynfeed = {
  setting: {
    sHomeUrl: '',
    sGoogleKey: '',
    currentWindowScrollY: '',
  },
  resetBackground: function () {
    if (typeof yncstatusbg !== 'undefined' && typeof yncstatusbg.resetBackground() == 'function') {
      yncstatusbg.resetBackground();
    }
    else {
      yncstatusbg.bDisabling = false;
      yncstatusbg.bHasPreview = false;

      $(yncstatusbg.sToggleBtn).removeClass('force-hide').addClass('active').parent().show();
      $(yncstatusbg.sListCollectionId).removeClass('force-hide').show();
      $('#js_activity_feed_form').find(yncstatusbg.sBackgroundPost).removeClass('has-background').removeAttr('style');
      $(yncstatusbg.sBackgroundId).val(0);
      $(yncstatusbg.sBackgroundPost).addClass('ync-statusbg-bg-container').find('.ync-statusbg-toggle-holder:first').hide();
      if ($('.ync-status-bg-original-emoji').length) {

        $('.ynfeed_extra_preview').addClass('empty-info');
        $('.ync-status-bg-original-emoji').removeClass('ynfeed_select_emojis').hide();
        $('.ync-status-bg-emoji').hide();
      }
      yncstatusbg.initCollectionSelection();
    }
  },
  detachComposeForm: function () {
    $('#ynfeed_form_share_holder .activity_feed_form').addClass('ynfeed_detached');
    if (!$('.ynfeed_over').length)
      $('<div class="ynfeed_over"></div>').prependTo('body');
  },
  attachComposeForm: function () {
    $('#ynfeed_form_share_holder .activity_feed_form.ynfeed_detached').removeClass('ynfeed_detached');
    $('.ynfeed_over').hide().remove();
    window.setTimeout(function () {
      $Core.ynfeed.init();
    }, 500);

  },
  finishLoadEditForm: function (formId) {
    if (typeof formId == 'undefined')
      return;
    let form = $('#' + formId);
    $Core.ynfeed.setting.currentWindowScrollY = window.scrollY;
    $('body').css({
      top: 0
    });
    form.draggable('disable');
    $('.ynfeed_form_edit').closest('.js_box').addClass('ynfeed_tb');
    $('.activity_feed_form:not(.ynfeed_detached) .activity_feed_form_button').show();
    $('.activity_feed_form:not(.ynfeed_detached) .contenteditable').focus();
    $('#activity_feed_submit, .js_box .js_box_close i').on('click', function () {
      $Core.ynfeed.attachComposeForm();
      if (!($(this).closest('.ynfeed_form_edit') && typeof $Core.dropzone.instance['photo_feed'] != 'undefined' && $Core.dropzone.instance['photo_feed'].getAcceptedFiles().length > 0)) {
        $(this).closest('.js_box').hide();
      }
      if ($(this).closest('.js_box_close').length && $(this).closest('.js_box').find('.js_box_content .ynfeed_form_edit').length && $(this).closest('.js_box').find('.js_box_content .ynfeed_form_edit').hasClass('photo-type')) {
        setTimeout(function () {
          $Core.Photo.processUploadImageForAdvFeed.resetFormAfterEdit(true);
        }, 100);
      }
      $(window).scrollTop($Core.ynfeed.setting.currentWindowScrollY);
    });
    if(form.find('.contenteditable').html()) {
      $Core.ynfeedEmoticon.moveCursorToEnd(form.find('.contenteditable'));
    }
  },
  updateSavedStatus: function (iFeedId, sPhrase, bSaved) {
    var saveButton = $('#ynfeed_btn_save_feed_' + iFeedId + ' > a');
    if (bSaved) {
      saveButton.attr('onclick', '$.ajaxCall(\'ynfeed.unsave\',\'id=' + iFeedId + '\') ;return false;');
      saveButton.html('<i class="fa fa-bookmark" aria-hidden="true"></i> ' + sPhrase);
    } else {
      saveButton.attr('onclick', '$.ajaxCall(\'ynfeed.save\',\'id=' + iFeedId + '\') ;return false;');
      saveButton.html('<i class="fa fa-bookmark-o" aria-hidden="true"></i> ' + sPhrase);
    }
  },
  updateNotificationStatus: function (iFeedId, sPhrase, bTurnedOff) {
    var saveButton = $('#ynfeed_btn_turnoff_noti_feed_' + iFeedId + ' > a');
    var onclick = saveButton.attr('onclick');
    if (bTurnedOff) {
      onclick = onclick.replace('turnoffNotification', 'turnonNotification');
      saveButton.attr('onclick', onclick);
      saveButton.html('<i class="fa fa-bell" aria-hidden="true"></i> ' + sPhrase);
    } else {
      onclick = onclick.replace('turnonNotification', 'turnoffNotification');
      saveButton.attr('onclick', onclick);
      saveButton.html('<i class="fa fa-bell-slash" aria-hidden="true"></i> ' + sPhrase);
    }
  },
  prepareHideFeed: function (aFeedIds, aUserIds) {
    aFeedIds.forEach(function (id) {
      $('.js_parent_feed_entry[data-feed-id=' + id + ']').addClass('ynfeed_prepare_hidding');
    });
    aUserIds.forEach(function (id) {
      $('.js_parent_feed_entry[data-user-id=' + id + ']').addClass('ynfeed_prepare_hidding');
    })
  },
  hideFeedFail: function (aFeedIds, aUserIds) {
    aFeedIds.forEach(function (id) {
      $('.js_parent_feed_entry[data-feed-id=' + id + ']').removeClass('ynfeed_prepare_hidding');
    });
    aUserIds.forEach(function (id) {
      $('.js_parent_feed_entry[data-user-id=' + id + ']').removeClass('ynfeed_prepare_hidding');
    })
  },
  hideFeed: function (aFeedIds, aUserIds) {
    aFeedIds.forEach(function (id) {
      $('.js_parent_feed_entry[data-feed-id=' + id + ']').hide('fast');
      var user_id = $('.js_parent_feed_entry[data-feed-id=' + id + ']').data('user-id');
      var hide_all_phrase = oTranslations['hide_all_from_somebody'].replace('{somebody}', $('.js_parent_feed_entry[data-feed-id=' + id + ']').data('user-fullname'));
      var hide_all_action = '$Core.ynfeed.prepareHideFeed([], [' + user_id + ']); $.ajaxCall(\'ynfeed.hideAllFromUser\', \'id=' + user_id + '\'); return false;';
      var undo_phrase = oTranslations['you_wont_see_this_post_in_news_feed_undo'].replace('{undo}', '<a href="javascript:void(0)" onclick="$Core.ynfeed.unhideFeed(' + id + ')">' + oTranslations['undo'] + '</a>');
      /*Show undo form*/
      var sUndo = '<div class="ynfeed_undo_hide_feed_' + id + '">' +
        '<span>' + undo_phrase + '</span><br>' +
        '<a class="ynfeed-hide-user" href="javascript:void(0);" onclick="' + hide_all_action + '"><i class="fa fa-eye-slash"></i>&nbsp;' + hide_all_phrase + '</a>' +
        '<span class="ynfeed-delete" onclick="$(this).closest(\'div\').remove();"><i class="ico ico-close"></i></span>' +
        '</div>';

      $(sUndo).insertBefore('.js_parent_feed_entry[data-feed-id=' + id + ']');
    });
    aUserIds.forEach(function (id) {
      $('.js_parent_feed_entry[data-user-id=' + id + ']:hidden').each(function (key, value) {
        var feed_id = $(value).data('feed-id');
        $('.ynfeed_undo_hide_feed_' + feed_id).remove();
        $(value).remove();

      });
      $('.js_parent_feed_entry[data-user-id=' + id + ']').hide('fast');
      var user_id = id;
      var first_feed_id = $('.js_parent_feed_entry[data-user-id=' + id + ']').first().prop('id');
      var fullname = $('#' + first_feed_id).data('user-fullname');

      var user_profile_url = $('#' + first_feed_id).data('user-profile');
      var undo_phrase = oTranslations['you_wont_see_posts_from_somebody_undo'].replace('{somebody}', '<a href="' + user_profile_url + '">' + fullname + '</a>').replace('{undo}', '<a href="javascript:void(0)" onclick="$Core.ynfeed.unhideAllFromUser(' + id + ')">' + oTranslations['undo'] + '</a>');
      /*Show undo form*/
      var sUndo = '<div class="ynfeed_undo_hide_user_' + id + '">' +
        '<span>' + undo_phrase + '</span>' +
        '<span class="ynfeed-delete" onclick="$(this).closest(\'div\').remove();"><i class="ico ico-close"></i></span>' +
        '</div>';

      $(sUndo).insertBefore('#' + first_feed_id);
    })
  },
  unhideFeed: function (iFeedId) {
    /*call to unhide function*/
    $.ajaxCall('ynfeed.undoHideFeed', 'id=' + iFeedId);
    $('.js_parent_feed_entry[data-feed-id=' + iFeedId + ']').show('fast').removeClass('ynfeed_prepare_hidding');
    $('.ynfeed_undo_hide_feed_' + iFeedId).remove();
  },
  unhideAllFromUser: function (iUserId) {
    /*call to unhide function*/
    $.ajaxCall('ynfeed.undoHideAllFromUser', 'id=' + iUserId);
    $('.js_parent_feed_entry[data-user-id=' + iUserId + ']').show('fast').removeClass('ynfeed_prepare_hidding');
    $('.ynfeed_undo_hide_user_' + iUserId).remove();
  },
  updateFormValue: function (form) {
    var parsedStatusInfo = $(form).find('#global_attachment_status_value + .ynfeed_compose_status > .contenteditable').html();
    if (parsedStatusInfo === "") {
      parsedStatusInfo = $('.activity_feed_form_button_status_info .ynfeed_compose_status .contenteditable').html();
    }

    if (parsedStatusInfo) {
      parsedStatusInfo = parsedStatusInfo.replace(new RegExp('<br>', 'g'), '');
      parsedStatusInfo = parsedStatusInfo.replace(/(?:<span id="generated" class="generatedMention")(?:[^<>]*)(?:data-type="([\w]+)")(?:[^<>]*)(?:data-id="([\d]+)")(?:[^<>]*)>([a-zA-Z0-9\s-!$%^&*()_+|~=`{}\[\]:";'<>?,.#@\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)(?:<\/span>)/g, '[$1=$2]$3[/$1]');
      parsedStatusInfo = parsedStatusInfo.replace(/(?:<img )(?:[^<>]*)(?:data-code="([^"]+)")(?:[^<>]*)>/g, function (match, contents) {
        if (contents === '(evilgrin)') {
          return ']:)';
        } else {
          return contents;
        }
      });
      parsedStatusInfo = parsedStatusInfo.replace(new RegExp('<div>', 'g'), '\n');
      parsedStatusInfo = parsedStatusInfo.replace(new RegExp('</div>', 'g'), '');
    }

    $(form).find('#ynfeed_status_content').val(parsedStatusInfo);
    $(form).find('#ynfeed_status_info').val(parsedStatusInfo);
  },
  strip: function (ele) {
    if (ele.hasClass('ynfeed_stripped'))
      return;
    ele.addClass('ynfeed_stripped');
    $.ajax({
      url: $Core.ynfeed.setting.sHomeUrl + 'ynfeed/strip',
      method: 'POST',
      data: {
        text: ele.html()
      },
    }).success(function (response) {
      ele.html(response);
      ele.css('visibility', 'visible');
      $Core.ynfeed.initImage();
    });
  },
  initImage: function () {
    $('.image_deferred').each(function () {
      var t = $(this),
        src = t.data('src'),
        i = new Image();

      t.addClass('built');
      if (!src) {
        t.addClass('no_image');
        return;
      }

      t.addClass('has_image');
      i.onerror = function (e, u) {
        t.replaceWith('');
      };
      i.onload = function (e) {
        t.attr('src', src);
      };
      i.src = src;
    });
    $('.image_load').each(function () {
      var t = $(this),
        src = t.data('src'),
        i = new Image();

      t.addClass('built');
      if (!src) {
        t.addClass('no_image');
        return;
      }

      t.addClass('has_image');
      i.onload = function (e) {
        if (t.hasClass('parent-block')) {
          var parentClass = t.data('apply');
          if (parentClass) {
            $('#main .' + parentClass).css('background-image', 'url(' + src + ')');
          }
        } else {
          t.css('background-image', 'url(' + src + ')');
        }
      };
      i.src = src;
    });
  },
  searchHidden: function (form) {
    $.ajaxCall('ynfeed.manageHidden', 'page=1&' + $(form).serialize());
  },
  init: function () {
    // Replace core feed js by ynfeed js
    $Core.activityFeedProcess = $Core.activityFeedProcess;
    $Core.resetActivityFeedForm = $Core.ynfeedResetActivityFeedForm;
    $Behavior.activityFeedProcess = $Behavior.ynfeedActivityFeedProcess;
    $Core.forceLoadOnFeed = $Core.ynfeedForceLoadOnFeed;
    $Core.loadMoreFeed = $Core.ynfeedLoadMoreFeed;
    $Behavior.checkForNewFeed = $Behavior.ynfeedCheckForNewFeed;

    $('.feed_sort_order_link').click(function () {
      $('.ynfeed_sort_holder').toggle();

      return false;
    });

    //hide more action
    var count_feed_action = $('.ynfeed-form-button-box-wrapper #activity_feed_share_this_one').length;
    if (count_feed_action < 3) {
      $('.ynfeed-form-btn-activity-viewmore').hide();
    }
    $('.ynfeed_sort_holder ul li a').click(function () {

      $('.ynfeed_sort_holder ul li a').removeClass('active');
      $('.ynfeed_sort_holder ul li a').removeClass('process');
      $(this).addClass('active');
      $(this).addClass('process');
      $.ajaxCall('ynfeed.userUpdateFeedSort', 'order=' + $(this).attr('rel'));
      $('.ynfeed_sort_holder').hide();

      return false;
    });

    $('body').on('click', function (event) {
      if (!$(event.target).parents('.ynfeed_compose_feeling').length)
        $('.ynfeed_autocomplete').hide();
    });
    (function (ele) {
      if (!ele.length) return;
      $.each(ele, function (key, value) {
        if (ele.hasClass('item_content') && ele.closest('.blog_content').length) {
          return;
        }
        $Core.ynfeed.strip($(value));
      });
    })($('.video-content, div[id^="js_photo_description_"], #photo-detail-view .item_view_content.twa_built'));

    (function (ele) {
      if (!ele.length) return;
      $Core.ynfeedEmoticon.init(ele);
    })($('.ynfeed_select_emojis'));

    (function (ele) {
      if (!ele.length) return;
      if (typeof $.fn.fullTagger == 'undefined') {
        $Core.loadStaticFiles(ele.data('js'));
      } else {
        ele.fullTagger();
      }
    })($('.ynfeed_compose_status .contenteditable'));


    (function (ele) {
      if (!ele.length) return;
      if (typeof $.fn.tagUser == 'undefined') {
        $Core.loadStaticFiles(ele.data('js'));
      } else {
        ele.tagUser($(ele).closest('form'));
      }
    })($('.ynfeed_compose_extra.ynfeed_compose_tagging .ynfeed_input_tagging'));

    (function (ele) {
      if (!ele.length) return;
      if (typeof $.fn.businessCheckin == 'undefined') {
        $Core.loadStaticFiles(ele.data('js'));
      } else {
        ele.businessCheckin();
      }
    })($('.ynfeed_compose_business .ynfeed_input_business'));


    (function (ele) {
      if (!ele.length) return;
      if (typeof $.fn.feeling == 'undefined') {
        $Core.loadStaticFiles(ele.data('js'));
      } else {
        ele.feeling();
      }
    })($('.ynfeed_compose_feeling .ynfeed_input_feeling'));

    (function (ele) {
      if (!ele.length) return;
      var $btn_ynfeed_display_check_in = $('#btn_ynfeed_display_check_in');
      if (typeof google == 'undefined' || typeof google.maps == 'undefined') {
        $Core.ynfeedCheckin.googleReady();
        return;
      }
      if (typeof google.maps == 'undefined') {
        return;
      }
      $('#btn_ynfeed_display_check_in').parent().show();
      if (typeof $.fn.geocomplete == 'undefined') {
        $Core.loadStaticFiles(ele.data('js'));
      } else {
        ele.find("#hdn_location_name").geocomplete({
          details: '#js_add_location',
          location: [$(this).data('lat'), $(this).data('lng')]
        }).bind("geocode:result", function (event, result) {
          $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_val_location_latlng').val(result.geometry.location.lat() + "," + result.geometry.location.lng());
          $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_val_location_name').val(result.formatted_address);

          $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_checkin').html(oTranslations['at_location'].replace('{location}', '<a href="javascript:void(0)" onclick="$(\'#btn_ynfeed_display_check_in\').trigger(\'click\')">' + result.formatted_address + '</a>')).show();
          $('.activity_feed_form:not(.ynfeed_detached) #js_location_input').hide("fast");
          $('.activity_feed_form:not(.ynfeed_detached) #btn_ynfeed_display_check_in').removeClass('is_active');

          $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_btn_delete_checkin').show();
          $btn_ynfeed_display_check_in.addClass('has_data');
          $('.activity_feed_form:not(.ynfeed_detached)').find('#ynfeed_btn_business').removeClass('has_data');
          $Core.ynfeedBusiness.cancelBusiness();
        });
        setTimeout(function () {
          if (typeof $(this).data('lat') != 'undefined' && $(this).data('lat') != '' && $(this).data('lng') != '' && $('#ynfeed_val_location_name').val() != '') {
            $('.activity_feed_form:not(.ynfeed_detached)').find('#ynfeed_extra_preview_checkin').html(oTranslations['at_location'].replace('{location}', '<a href="javascript:void(0)" onclick="$(\'#btn_ynfeed_display_check_in\').trigger(\'click\')">' + $('#ynfeed_val_location_name').val() + '</a>')).show();
          }
        }, 200);
      }
    })($('#js_location_input'));

    (function (ele) {
      if (!ele.length) return;

      if (typeof google == 'undefined') {
        $Core.ynfeedCheckin.googleReady();
        return;
      }
      if (typeof google.maps == 'undefined')
        return;
      $.each(ele, function (key, value) {
        $Core.ynfeedCheckin.initMap(value);
      });
    })($('.ynfeed_map_canvas'));

    $('#ynfeed_btn_tag').off('click').on('click', function () {
      var visible = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra.ynfeed_compose_tagging').is(":visible");
      if (visible) {
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra.ynfeed_compose_tagging').hide('fast');
      } else {
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) #js_location_input').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra.ynfeed_compose_tagging').show('fast', function () {
          $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra.ynfeed_compose_tagging .ynfeed_input_tagging').focus().trigger('click');
        });
      }
    });

    $('#btn_ynfeed_display_check_in').off('click').on('click', function () {
      var visible = $('.activity_feed_form:not(.ynfeed_detached) #js_location_input').is(":visible");
      if (visible) {
        $('.activity_feed_form:not(.ynfeed_detached) #js_location_input').hide('fast');
      } else {
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) #js_location_input').show('fast', function () {
          $('.activity_feed_form:not(.ynfeed_detached) #hdn_location_name').focus().trigger('click');
        });
      }
    });

    $('#ynfeed_btn_business').off('click').on('click', function () {
      var visible = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business').is(":visible");
      if (visible) {
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business').hide('fast');
      } else {
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) #js_location_input').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business').show('fast', function () {
          $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_business').focus().trigger('focus').trigger('click');
        });
      }
    });

    $('#ynfeed_btn_feeling').off('click').on('click', function () {
      var visible = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling').is(":visible");
      if (visible) {
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling').hide('fast');
      } else {
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_extra').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) #js_location_input').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling').show('fast', function () {
          $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_feeling').focus().trigger('focus').trigger('click');
        });
      }
    });

    (function (ele) {
      if (!ele.length) return;
      $(ele).on('click', function () {
        var sTagged = $(this).data("tagged");
        tb_show(oTranslations['people'], $.ajaxBox('ynfeed.showUsers', 'height=300&width=420&ids=' + sTagged));
      });
    })($('.ynfeed_expand_users'));

    (function (ele) {
      $('#pf_v_share_success_message .pf_v_message_cancel').on('click', function () {
        $Core.ynfeedResetActivityFeedForm();
      })
    })($('#pf_v_share_success_message'));

    var $ynfeed_extra_preview = $('.activity_feed_form .ynfeed_extra_preview');

    if ($ynfeed_extra_preview.length) {
      ynfeedShare.addPreviewObserver($ynfeed_extra_preview);
    }

    setTimeout(function () {
      if (typeof PStatusBg !== "undefined") {
        $(document).on('click focus', '.contenteditable', function () {
          if ($('#ynfeed_status_content').parent().find('.p-statusbg-toggle-holder').length) {
            let parent = $('#ynfeed_status_content').closest('#global_attachment_status');
            let container = parent.find('.ynfeed_select_emojis:first');
            let toggle = parent.find('.p-statusbg-toggle-holder:first');
            let tempDiv = $('<div style="display: inline-block; margin-right: 6px;"></div>');
            toggle.detach().prependTo(tempDiv);
            tempDiv.prependTo(container);
            setTimeout(function () {
              toggle.css('display', 'inline-block');
            }, 100);
          }
          PStatusBg.showCollections();
        });
        if ($('.ynfeed_compose_status').length && !$('.ynfeed_compose_status').hasClass('js_textarea_background p-statusbg-container')) {
          $('.ynfeed_compose_status').addClass('js_textarea_background p-statusbg-container');
        }
      }
    }, 700);
  },
  unhide: function (hide_id, resource_id, resource_type) {
    $.ajaxCall('ynfeed.unhide', 'hide_id=' + hide_id + '&resource_id=' + resource_id + '&resource_type=' + resource_type);
  },
  updateSelectedUnhideNumber: function () {
    var $list = $('input#ynfeed_list_unhide');
    var $checkedList = $('input.ynfeed_item_hidden_checkbox:checked');
    var listValues = [];
    $list.val('');
    $.each($checkedList, function (key, value) {
      listValues.push($(value).data("hid"));
    });
    $list.val(listValues.toString());

    if (listValues.length != 1)
      $('.ynfeed-list-headline > span').text(oTranslations['number_items_selected'].replace('{number}', listValues.length));
    else
      $('.ynfeed-list-headline > span').text(oTranslations['one_item_selected']);

    if (listValues.length)
      $('#ynfeed_unhide_button').removeClass('disabled');
    else $('#ynfeed_unhide_button').addClass('disabled');
  },
  selectUnhide: function (item) {
    $Core.ynfeed.updateSelectedUnhideNumber();
  },
  multiUnhide: function () {
    var $list = $('input#ynfeed_list_unhide');
    $.ajaxCall('ynfeed.multiUnhide', 'ids=' + $list.val());
  },
  deleteElemsById: function (prefix, JSONids, callback) {
    JSONids.forEach(function (id) {
      $('#' + prefix + id).hide('fast', function () {
        $(this).remove();
      });
    });
    callback();
  },
  resetSelectedUnhide: function () {
    var $container = $('#ynfeed_list_hidden');
    var $list = $container.find('input#ynfeed_list_unhide');
    $list.val('');
    $container.find('input.ynfeed_item_hidden_checkbox').checked = false;
    $('.ynfeed-list-headline > span').text(oTranslations['number_items_selected'].replace('{number}', 0));
    $('#ynfeed_unhide_button').addClass('disabled');
  },
  removeTag: function (sParams) {
    $Core.jsConfirm({message: oTranslations['remove_tag_confirmation']}, function () {
      $.ajaxCall('ynfeed.removeTag', sParams);
    }, function () {
    });
  },
  turnoffNotification: function (iFeedId, iItemId, sItemType) {
    $.ajaxCall('ynfeed.turnoffNotification', 'feed_id=' + iFeedId + '&item_id=' + iItemId + '&item_type=' + sItemType);
  },
  turnonNotification: function (iFeedId, iItemId, sItemType) {
    $.ajaxCall('ynfeed.turnonNotification', 'feed_id=' + iFeedId + '&item_id=' + iItemId + '&item_type=' + sItemType);
  },
  prepareFiltering: function (item, ajax, params) {
    $('#js_new_feed_update').html('');
    $('.ynfeed_filters li').removeClass('active');

    $('#js_feed_content').slideUp('slow', function () {
      $('#ynfeed_filtering').slideDown('fast');
      $('#js_feed_content').html('');
      $.ajaxCall(ajax, params);
    });
    $(item).parent().addClass('active');

  },
  selectAllHiddens: function (elem) {
    if (elem.checked) {
      $('.ynfeed_item_hidden_checkbox').each(function (key, value) {
        value.checked = true;
      });
    }
    $('.ynfeed_item_hidden_checkbox').trigger('change');
  },
  selectCustomFeelingImage: function (elem) {
    tb_show(oTranslations["select_feeling_icon"], $.ajaxBox('ynfeed.loadFeelingImages', ''));
  },
  replaceFeelingIcon: function (url) {
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_image').val(url);
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_feeling > img').prop('src', url);
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_selected_feeling > img').prop('src', url);
    $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_image').val(url);
  },
  removeFocusForm: function (alsoResetButtons) {
    $('body').removeClass('ynfeed-form-focus');
    $('.ynfeed_compose_status .contenteditable').removeClass('_is_set focus');
    $('.activity_feed_form:not(.ynfeed_detached) .activity_feed_form_button').hide();
    $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_select_emojis').hide();
    $('.ynfeed-form-button-box').removeClass('full');
    $('.ynfeed-location-box').hide();
    $('.ync-statusbg-toggle-holder').hide();
    if (alsoResetButtons) {
      $('.activity_feed_share_this_one_link').removeClass('is_active, has_data');
    }
  },
  checkFilterMoreCut: function () {
    var filter_fix_cut = $('.ynfeed_filter_dropdown .dropdown-menu');
    if (filter_fix_cut.length) {
      var pos = filter_fix_cut.offset();
      var x_pos = pos.left;
      if (x_pos < 10) {
        filter_fix_cut.removeClass('dropdown-menu-right');
      }
    }
  }
};

$Behavior.removeDefaultFeedOptions = function () {
  var isCoreActions = function (el) {
    if ($(el).hasClass('ynfeed_feed_option')) {
      return false;
    }
    var $anchor = $(el).find('a');
    if (typeof $anchor.attr('onclick') !== 'undefined') {
      if ($anchor.attr('onclick').indexOf("'feed.editUserStatus'") != -1) {
        return true;
      }
      if ($anchor.attr('onclick').indexOf("'feed.delete'") != -1) {
        return true;
      }
      if ($anchor.attr('onclick').indexOf("'feed.hideFeed'") != -1) {
        return true;
      }
      if ($anchor.attr('onclick').indexOf("'feed.hideAllFromUser'") != -1) {
        return true;
      }
      if ($anchor.attr('onclick').indexOf("appSavedItem.processItem") != -1 && $anchor.closest('.js_feed_view_more_entry_holder').length) {
        return true;
      }
      if ($anchor.attr('onclick').indexOf("'feed.manageHidden'") != -1) {
        return true;
      }
    }
    if (typeof $anchor.attr('href') !== 'undefined') {
      if ($anchor.attr('href').indexOf("report.add") != -1) {
        return true;
      }
    }
    return false;
  };

  $('.feed-options-holder li, .feed_options_holder li').each(function (index, el) {
    if (isCoreActions(el)) {
      el.remove();
    }
  });

  $('#hd-cof li').each(function (index, el) {
    if (isCoreActions(el)) {
      el.remove();
    }
  });

  if (!$('#page_core_index-member').length) {
    if (!$('#hd-cof .dropdown-menu li.ynfeed_settings').length) {
      $('<li class="ynfeed_filter_more_item ynfeed_settings"><a href="javascript:void(0)" onclick="tb_show(\'' + oTranslations['manage_hidden'] + '\', $.ajaxBox(\'ynfeed.manageHidden\', \'\'));"><i class="fa fa-cog" aria-hidden="true"></i>' + oTranslations['manage_hidden'] + '</a></li>').insertBefore($('#hd-cof .dropdown-menu li.divider').get(0));
    }
  }
  else {
    if ($('#hd-cof .dropdown-menu li.ynfeed_settings').length) {
      $('#hd-cof .dropdown-menu li.ynfeed_settings').remove();
    }
  }
};

$Ready(function () {
  if (typeof sGoogleKey !== 'undefined') {
    $Core.ynfeed.setting.sGoogleKey = sGoogleKey;
  }
  if (typeof sHomeUrl !== 'undefined') {
    $Core.ynfeed.setting.sHomeUrl = sHomeUrl;
  }
  $Core.ynfeed.finishLoadEditForm($('.ynfeed_form_edit').closest('.js_box').prop('id'));
  $Core.ynfeed.init();
  ynfeedShare.init();

  $('.ynfeed_filter_more').click(function () {
    $Core.ynfeed.checkFilterMoreCut();
  });
});

var ynfeedShare = {
  $box: null,
  windowScrollY: 0,
  init: function () {
    setTimeout(function () {
      if (ynfeedShare.shareBoxOpen()) {
        ynfeedShare.disableFeedStatus();
      } else {
        ynfeedShare.enableFeedStatus();
      }
    }, 500);
    if (!this.shareBoxOpen()) {
      return;
    } else {
      this.getBox().closest('.js_box').addClass('ynfeed-popup-share-feed');
      $('body').addClass('has-ynfeed-share-popup');
      if (this.isBound()) {
        return;
      }
    }
    this.bindPostTypeSelect();
    this.initEmojis();

    if (this.$box.find('.yncfeed-feed-item .feed_share_holder').outerHeight() > 140) {
      $('.js-ynfeed-btn-collapse-popup').css("display", "flex");
    }

    this.$box.find('#js_ynfeed_share_form').on('submit', this.submitShare);
    this.addPreviewObserver(this.$box.find('.ynfeed_extra_preview'));
    this.markScrollY();
    this.bindCloseButton();
  },
  bindCloseButton: function () {
    this.$box.closest('.js_box').find('.js_box_close a').removeAttr('onclick').addClass('dont-unbind').off('click').click(ynfeedShare.closeShareBox);
    this.$box.find('#ynfeed_close_btn').off('click').click(ynfeedShare.closeShareBox);
  },
  markScrollY: function () {
    this.windowScrollY = window.scrollY;
    $('body').css({
      top: 0
    });
  },
  isBound: function () {
    var bound = this.$box.data('bound');
    if (!bound) {
      this.$box.data('bound', 1);
    }
    return bound;
  },
  getBox: function () {
    this.$box = $('#ynfeed_share_box');
    return this.$box;
  },
  shareBoxOpen: function () {
    return this.getBox().length;
  },
  disableFeedStatus: function () {
    var $ynfeedForm = $('#ynfeed_form_share_holder .activity_feed_form');
    if ($ynfeedForm.length) {
      $ynfeedForm.removeClass('activity_feed_form').addClass('activity_feed_form_inactive');
    }
  },
  enableFeedStatus: function () {
    var $ynfeedForm = $('#ynfeed_form_share_holder .activity_feed_form_inactive');
    if ($ynfeedForm.length) {
      $ynfeedForm.removeClass('activity_feed_form_inactive').addClass('activity_feed_form');
    }
  },
  bindPostTypeSelect: function () {
    var $postTypeDropdown = this.$box.find('.post-type-dropdown'),
      $friendHolder = this.$box.find('#js_feed_share_friend_holder'),
      $privacyHolder = this.$box.find('.privacy_setting_div')
    ;
    $postTypeDropdown.find('li a').on('click', function () {
      $friendHolder.toggle($(this).attr('rel') === '2');
      $privacyHolder.toggle($(this).attr('rel') === '1');
      if ($(this).attr('rel') === '2') {
        $privacyHolder.find('#privacy').val(0);
      } else {
        $privacyHolder.find('#privacy').val($privacyHolder.find('[data-toggle="privacy_item"].is_active_image').attr('rel'));
      }
    });
    $(document).on('click', '[data-toggle="share_item"]', function () {
      var element = $(this),
        container = element.closest('.open'),
        input = container.find('input:first'),
        button = container.find('[data-toggle="dropdown"]'),
        rel = element.attr('rel');
      // processs data
      input.val(rel);

      container.find('.is_active_image').removeClass('is_active_image');
      element.addClass('is_active_image');

      var $sContent = element.html();

      button.find('span.txt-label').html($sContent);

      container.find('.fa.fa-privacy').replaceWith($('<i/>', {class: 'fa fa-privacy fa-privacy-' + rel}));
    });
  },
  initEmojis: function () {
    this.$box.find('.ynfeed_select_emojis').show();
  },
  submitShare: function () {
    $Core.ynfeed.updateFormValue(this);

    $(this).ajaxCall('ynfeed.share');
    $('body').removeClass('has-ynfeed-share-popup');
    return false;
  },
  addPreviewObserver: function ($preview) {
    var config = {attributes: false, childList: true, subtree: true};

    var callback = function (e) {
      if ($preview.find('#ynfeed_extra_preview_feeling *').length
        || $preview.find('#ynfeed_extra_preview_tagged *').length
        || $preview.find('#ynfeed_extra_preview_checkin *').length
        || $preview.find('#ynfeed_extra_preview_business *').length
      ) {
        $preview.show();
      } else {
        $preview.hide();
      }
    };

    var observer = new MutationObserver(callback);

    observer.observe($preview.get(0), config);
  },
  closeShareBox: function () {
    $(window).scrollTop(ynfeedShare.windowScrollY);
    js_box_remove(this);
    $('body').removeClass('has-ynfeed-share-popup');
  }
};

$Behavior.ynFeedBindShareButton = function () {
  var $js_feed_content = $('#js_feed_content'),
    $ynfeed_form_share_holder = $('#ynfeed_form_share_holder'),
    containerOffsetTop = $ynfeed_form_share_holder.length ? $ynfeed_form_share_holder.offset().top : ($js_feed_content.length ? $js_feed_content.offset().top : 0)
  ;

  $('.js_feed_content .feed-comment-share-holder, .js_feed_content .feed_comment_share_holder').each(function () {
    if ($(this).hasClass('dropup') || $(this).hasClass('dropdown')) {
      return;
    }
    var $shareList = $(this).closest('.activity_feed_content').find('.ynfeed-share-list ul'),
      $link = $(this).find('a'),
      offsetFromContainer = $(this).offset().top - containerOffsetTop,
      requiredSpace = 260;
    $shareList.find('a.ynfeed-share-share').attr('onclick', $link.attr('onclick'));

    $(this).addClass(offsetFromContainer > requiredSpace ? 'dropup' : 'dropdown').append($shareList);

    $link.length && $link
      .removeAttr('onclick')
      .attr('href', 'javascript:void(0);')
      .attr('data-toggle', 'dropdown');
  });

  $('a.ynfeed-share').off('click').click(function () {
    var url = $(this).data('surl');
    var provider = $(this).data('provider');
    var title = '%20';
    var shareUrl;
    if (provider == 'pinterest') {
      shareUrl = 'http://pinterest.com/pin/create/button/?url=' + url + '&description=' + title;
    } else {
      shareUrl = 'http://www.addthis.com/bookmark.php?s=' + provider + '&url=' + url + '&title=' + title;
    }
    var shareWindow = window.open(shareUrl, provider, 'height=400,width=700,top=200,left=400');
    if (window.focus) {
      shareWindow.focus()
    }
  });

  $('a.ynfeed-share-more').off('click').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).hide().closest('li').siblings('li').show();
  });
};

PF.event.on('on_page_change_end', function () {
  $Core.Photo.iTotalError = 0;
  $bButtonSubmitActive = true;
  $('.activity_feed_form_button .button').removeClass('button_not_active');
});

$Core.Photo.processUploadImageForAdvFeed = {
  dropzoneOnRemovedFileInFeedForEditPhoto: function (ele, file) {
    $Core.Photo.dropzoneOnRemovedFileInFeed(ele, file);
    if (ele.find('.dz-image-preview').length) {
      let tempFiles = $Core.dropzone.instance['photo_feed'].files;
      $Core.dropzone.instance['photo_feed'].files = [ele.find('.dz-image-preview').length];
      setTimeout(function () {
        $Core.dropzone.instance['photo_feed'].files = tempFiles;
      }, 10);
    }
  },
  removePhoto: function (obj) {
    let _this = $(obj);
    if (_this.closest('.ynfeed_form_edit').length) {
      let parent = _this.closest('.ynfeed_form_edit');
      _this.closest('.dz-image-preview').remove();
      $Core.dropzone.instance['photo_feed'].options.maxFiles++;
      if (!parent.find('.dz-image-preview').length) {
        parent.find('.dropzone-component').removeClass('dz-started');
        parent.find('#activity_feed_submit').addClass('button_not_active');
      }
    }
  },
  initEditPhotoStatus: function () {
    $Core.Photo.iTotalError = 0;
    $bButtonSubmitActive = true;
    $('.ynfeed_form_edit').find('#activity_feed_submit').removeClass('button_not_active');
  },
  resetFormAfterEdit: function (executeResetFunc) {
    $Core.dropzone.instance["photo_feed"].destroy();
    if ($Core.dropzone.instance["photo_feed"]) {
      delete $Core.dropzone.instance["photo_feed"];
    }
    $Core.dropzone.init($("#photo_feed-dropzone"));
    $Core.Photo.iTotalError = 0;
    if (executeResetFunc) {
      $Core.ynfeedResetActivityFeedForm();
    }
  }
}


