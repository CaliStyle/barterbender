if (typeof $Core.fanot == 'undefined') {
    $Core.fanot = {
        fanotDelay: 5000,
        fanotUpdateDelay: 10000
    };
}

$Core.fanot.bFirst = 1;

$Core.fanot.hideFanot = function (id) {
    $("#fanot_item_" + id).stop(true, true).fadeOut("medium", function () {
        $(this).remove();
    });
}

$Core.fanot.updateSeen = function (id, e, t) {
    var l = $(e).attr('href');
    $.ajaxCall("fanot.updateSeen", "id=" + id + '&l=' + l + '&t=' + t);
    return false;
}

$Core.fanot.hideAll = function () {
    var eles = $('.fanot_item:not(.delayed)');
    var c = eles.length;
    if (c > 0) {
        eles.each(function (i) {
            $(this).addClass('delayed').delay($Core.fanot.fanotDelay + i * 700).fadeOut("medium", function () {
                $(this).remove();
            });
        });
    }
}

$Core.fanot.update = function () {
    var urldomain = oParams['sJsHome'];
    $.ajax({
        type: "POST",
        data: 'first=' + $Core.fanot.bFirst,
        url: urldomain + "module/fanot/static/php/update.php",
        success: function (sHtml) {
            try {
                var aContent = $.parseJSON(sHtml);
            } catch (e) {
                aContent = null;
            }
            if (aContent != null) {
                var hasNotification = false;
                if (aContent['iTotalNotifications'] > 0) {
                    $('#js_total_new_notifications').html(aContent['iTotalNotifications']).css({
                        display: 'block'
                    }).show();
                    hasNotification = true;
                }
                if (aContent['iTotalFriendRequests'] > 0) {
                    $('#js_total_new_friend_requests').html(aContent['iTotalFriendRequests']).css({
                        display: 'block'
                    }).show();
                    hasNotification = true;
                }
                var sContent = aContent['sHtml'].replace("\'", "'");
                $('#fanot_box').prepend(sContent).fadeIn('medium');

                // alert sound
                var soundname = urldomain + 'module/fanot/static/audio/notification_alert';
                if (hasNotification && (element = document.getElementById("fanot_sound"))) {
                    element.innerHTML = '<audio autoplay="autoplay"><source src="' + soundname + '.mp3" type="audio/mpeg" /><source src="' + soundname + '.ogg" type="audio/ogg" /><embed hidden="true" autostart="true" loop="false" src="' + soundname + '.mp3" /></audio>';
                }
                
                // defer images
                if (typeof ($Behavior.defer_images) != "undefined") {
                    $Behavior.defer_images();                    
                }
                $('.fanot_icon .image_deferred').attr('src',$('.fanot_icon .image_deferred').data('src'));
            }
            $Core.fanot.hideAll();
        },
        complete: function () {
            $Core.fanot.bFirst = 0;
            setTimeout($Core.fanot.update, $Core.fanot.fanotUpdateDelay);
        }
    });
}

$(document).ready($Core.fanot.update);
