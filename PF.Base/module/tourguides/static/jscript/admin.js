var wrapper = $('#yntour_admin_draggable_wrapper');
var wrapperSize = {
    width: wrapper.width(),
    height: wrapper.height()
};
var button = $('#yntour_admin_draggable_button');
var buttonSize = {
    width: button.width(),
    height: button.height()
};
function resetPosition(bIsDefault) {
    var r, t;
    if(bIsDefault) {
        $('#yntour_admin_position_right').val('0.04');
        $('#yntour_admin_position_top').val('0.06');
    }
    r = parseFloat($('#yntour_admin_position_right').val());
    t = parseFloat($('#yntour_admin_position_top').val());
    $('#yntour_admin_draggable_button').css('right', r * (wrapperSize.width - buttonSize.width) + 'px').css('top', t * (wrapperSize.height - buttonSize.height) + 'px').css('left','auto').show();
}
window.setTimeout(function() {
    resetPosition(false);
    $('#yntour_admin_draggable_button').draggable({
        containment: "parent",
        snap: '.gridlines',
        stop: function () {
            var r = ((parseFloat(wrapperSize.width) - parseFloat($(this).position().left) - buttonSize.width) /  (parseFloat(wrapperSize.width) - buttonSize.width)).toFixed(2);
            var t = (parseFloat($(this).position().top) /   (parseFloat(wrapperSize.height) - buttonSize.height)).toFixed(2);
            $('#yntour_admin_position_right').val(r);
            $('#yntour_admin_position_top').val(t);
        }
    });
}, 500);