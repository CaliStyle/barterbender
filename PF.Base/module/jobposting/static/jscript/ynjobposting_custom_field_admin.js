
$Core.jobposting =
{
	sUrl: '',
	
	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},	
	
	action: function(oObj, sAction)
	{
		aParams = $.getParams(oObj.href);
		$('.dropContent').hide();	
		
		switch (sAction)
		{
			case 'edit':
				tb_show('Edit field/question', $.ajaxBox('jobposting.controllerAddFieldBackEnd', 'height=300&amp;width=300&action=edit&id='+ aParams['id'] + '&objtype=' + aParams['objtype']));
				
				break;
			case 'delete':
                $Core.jsConfirm({
                }, function () {
                    window.location.href = $Core.jobposting.sUrl + 'delete_' + aParams['id'] + '/?objtype=' + aParams['objtype'];
                }, function () {
                });
                break;
			default:
			
				break;	
		}
		
		return false;
	}
};
var ynjobposting_init = {
	init_sort: function (className) {
	    if (typeof(className) == 'undefined') {
            className = 'sortable';
        }
        $('.' + className + ' ul').sortable({
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
    },
	init_dropdown: function () {
        $('.js_drop_down').click(function()
        {
            eleOffset = $(this).offset();

            aParams = $.getParams(this.href);

            $('#js_cache_menu').remove();

            $('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');

            $('#js_cache_menu .link_menu li a').each(function()
            {
                this.href = '#?id=' + aParams['id'] + '&objtype=' + aParams['objtype'];
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
    }
}