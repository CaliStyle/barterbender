if (typeof $Core.custom !== 'undefined') {
    $Core.custom.action = function (oObj, sAction) {
        aParams = $.getParams(oObj.href);
        $(".dropContent").hide();
        switch (sAction) {
          case "delete":
              $Core.jsConfirm({message: oTranslations['core.are_you_sure']}, function () {
                  $.ajaxCall("fevent.deleteField", "id=" + aParams.id);
              }, function () {
              });
              return false;
            break;
            case "edit":
             window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
            break;
          default:
            $.ajaxCall("fevent.toggleActiveField", "id=" + aParams.id);
            break;
        }
        return false;
    };
}

$Behavior.onInitAdminMenu = function()
{

    $('.js_drop_down').click(function()
    {
        eleOffset = $(this).offset();

        aParams = $.getParams(this.href);

        $('#js_cache_menu').remove();

        $('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');

        $('#js_cache_menu .link_menu li a').each(function()
        {
            if (this.hash == '#active' && (($('#js_field_' + aParams['id']).html() && $('#js_field_' + aParams['id']).html().match(/<del>/i))))
            {
                $(this).html('Set to Active');
            }
            this.href = '#?id=' + aParams['id'];


            this.href = '#?id=' + aParams['id'] + '&type=' + aParams['type'] + '';
        });

        $('.dropContent').show();

        $('.dropContent').mouseover(function()
        {
            $('.dropContent').show();

            return false;
        });

        $('.dropContent').mouseout(function()
        {
            $('.dropContent').hide();
            $('.sJsDropMenu').removeClass('is_already_open');
        });

        return false;
    });

};