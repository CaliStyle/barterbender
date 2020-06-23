var ynmember = {

    getSearchData: function(obj) {
        $Core.ajaxMessage();
        $.ajaxCall('ynmember.filterAdminFilterMember', $(obj).serialize() + '&global_ajax_message=true', 'post');
        return false;
    },

    getSearchReviewData: function(obj) {
        $Core.ajaxMessage();
        $.ajaxCall('ynmember.filterAdminFilterReview', $(obj).serialize() + '&global_ajax_message=true', 'post');
        return false;
    },

    updateFeatured: function(el)
    {
        var iUserId = $(el).data('user_id'),
            iIsFeatured = $(el).data('is_featured');
        $.ajaxCall('ynmember.updateFeaturedInAdmin', 'iUserId=' + iUserId + '&iIsFeatured=' + iIsFeatured + '&global_ajax_message=true');
    },

    updateMod: function(el)
    {
        var iUserId = $(el).data('user_id'),
            iIsMod = $(el).data('is_mod');
        $.ajaxCall('ynmember.updateModInAdmin', 'iUserId=' + iUserId + '&iIsMod=' + iIsMod + '&global_ajax_message=true');
    },

    clearMod: function(excluded)
    {
        $("td[id^=ynmember_member_update_mod]").each(function(){
            $(this).find('div').first().removeClass('js_item_is_active').addClass('js_item_is_not_active');
            var anchor = $(this).find('a.js_item_active_link');
            anchor.data('is_mod', 0);
        });
    },

    checkAllReview: function(el)
    {
        var status = el.checked;
        $('.review_row_checkbox').each(function(index, element){
            element.checked = status;
        });
        this.checkDisableStatus();
    },

    checkDisableStatus: function()
    {
        console.log('checek');
        var status = false;
        $('.review_row_checkbox').each(function(index, element){
            if (element.checked)
            {
                status = true;
            }
        });
        this.setButtonStatus(status);
    },

    setButtonStatus: function(enabled)
    {
        var delete_selected = $('.delete_selected');
        if (enabled) {
            delete_selected.removeClass('disabled');
            delete_selected.attr('disabled', false);
        }
        else {
            delete_selected.addClass('disabled');
            delete_selected.attr('disabled', true);
        }
    },
    actionMultiSelect : function(obj)
    {
        $.ajaxCall('ynmember.actionMultiSelectReview',$(obj).serialize(),'post');
        return false;
    },

    deleteReview: function(review_id)
    {
        var message = oTranslations['are_you_sure'];
        var form = $('#ynmember_review_list');
        $Core.jsConfirm({message: message}, function() {
            $.ajaxCall('ynmember.deleteReviewInAdmin', 'review_id=' + review_id);
        }, function(){});
        return false;
    },
    deleteSelectedReview : function (obj,sType)
    {
        var message = oTranslations['are_you_sure'];
        var form = $('#ynmember_review_list');
        $Core.jsConfirm({message: message}, function() {
            form.submit();
        }, function(){});
        return false;
    },
};

$Behavior.ynmemberInitSearchAdmin = function() {
    $("#from").datepicker({
        dateFormat: 'mm/dd/yy',
        onSelect: function(dateText, inst) {
            var $dateTo = $("#to").datepicker("getDate");
            var $dateFrom = $("#from").datepicker("getDate");
            if($dateTo)
            {
                $dateTo.setHours(0);
                $dateTo.setMilliseconds(0);
                $dateTo.setMinutes(0);
                $dateTo.setSeconds(0);
            }

            if($dateFrom)
            {
                $dateFrom.setHours(0);
                $dateFrom.setMilliseconds(0);
                $dateFrom.setMinutes(0);
                $dateFrom.setSeconds(0);
            }

            if($dateTo && $dateFrom && $dateTo < $dateFrom) {
                tmp = $("#to").val();
                $("#to").val($("#from").val());
                $("#from").val(tmp);
            }
            return false;
        }
    });
    $("#to").datepicker({
        dateFormat: 'mm/dd/yy',
        onSelect: function(dateText, inst) {
            var $dateTo = $("#to").datepicker("getDate");
            var $dateFrom = $("#from").datepicker("getDate");

            if($dateTo)
            {
                $dateTo.setHours(0);
                $dateTo.setMilliseconds(0);
                $dateTo.setMinutes(0);
                $dateTo.setSeconds(0);
            }

            if($dateFrom)
            {
                $dateFrom.setHours(0);
                $dateFrom.setMilliseconds(0);
                $dateFrom.setMinutes(0);
                $dateFrom.setSeconds(0);
            }

            if($dateTo && $dateFrom && $dateTo < $dateFrom) {
                tmp = $("#to").val();
                $("#to").val($("#from").val());
                $("#from").val(tmp);
            }
            return false;
        }
    });

    $("#js_from_date_anchor").click(function () {
        $("#from").focus();
        return false;
    });
    $("#js_to_date_anchor").click(function () {
        $("#to").focus();
        return false;
    });
};