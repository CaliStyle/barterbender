$Core.custom.action = function (oObj, sAction) {
    aParams = $.getParams(oObj.href);
    $(".dropContent").hide();
    switch (sAction) {
      case "delete":
            $Core.jsConfirm({message: oTranslations['are_you_sure']}, function(){
                $.ajaxCall("resume.deleteField", "id=" + aParams.id);
            },function(){});
            break;
        case "edit":
            console.log(this.sUrl);
            window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
            break;
        default:
            $.ajaxCall("resume.toggleActiveField", "id=" + aParams.id);
            break;
    }
    return false;
};

$Behavior.onInitAdminMenu = function()
{
    $('.sortable ul').sortable({
            axis: 'y',
            update: function(element, ui)
            {
                var iCnt = 0;
                $('.js_mp_order').each(function()
                {
                    iCnt++;
                    this.value = iCnt;
                });
            },
            opacity: 0.4
        }
    );

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
    $('.js_resume_delete_current_option').click(function()
    {
        var obj = this;
        $Core.jsConfirm({message: oTranslations['are_you_sure_you_want_to_delete_this_custom_option']}, function() {
            aParams = $.getParams(obj.href);

            $.ajaxCall('resume.deleteOption', 'id=' + aParams['id']);
        }, function(){});

        return false;
    });
};