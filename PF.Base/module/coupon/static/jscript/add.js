$Behavior.advancedmarketplaceAdd = function()
{
	$('.js_mp_category_list').change(function()
	{
		var $this = $(this);
		var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
		iCatId = $this.val();
		if(!iCatId) {
			iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
		}

		$.ajaxCall('advancedmarketplace.frontend_loadCustomFields', 'catid='+iCatId+'&lid='+$("#ilistingid").val());
		$('.js_mp_category_list').each(function()
		{
			if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
			{
				$('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();				
				
				this.value = '';
			}
		});
		
		$('#js_mp_holder_' + $(this).val()).show();
	});	
}

function yncInitPrintSlide() {
	$('.yns-print-slider').carouFredSel({
		auto: false,
		height : '100px',
		scroll : 1,
		prev: '#ync-prev3',
		next: '#ync-next3',
		items : {
			width : '310px',
			visible : {
				min : 1,
				max : 1
			}
		}
	});
	
	if ($('#print_option_photo').val()=='1') {
		$('.ync-style').removeClass('no-photo');
		$('#checkbox_option_photo').attr('checked','checked');
	} else {
		$('.ync-style').addClass('no-photo');
		$('#checkbox_option_photo').removeAttr('checked');
	}

	if ($('#print_option_site_url').val()=='1') {
		$('.print_option_site_url').show();
		$('#checkbox_option_site_url').attr('checked','checked');
	} else {
		$('.print_option_site_url').hide();
		$('#checkbox_option_site_url').removeAttr('checked');
	}
    
    if ($('#print_option_location').val()=='1') {
		$('.print_option_location').show();
		$('#checkbox_option_location').attr('checked','checked');
	} else {
		$('.print_option_location').hide();
		$('#checkbox_option_location').removeAttr('checked');
	}

	if ($('#print_option_category').val()=='1') {
		$('.print_option_category').show();
		$('#checkbox_option_category').attr('checked','checked');
	} else {
		$('.print_option_category').hide();
		$('#checkbox_option_category').removeAttr('checked');
	}
	
	$('.yns-print-slider').find('li').removeClass('active');
	$('.yns-print-slider').find('[print_style="' + $('#print_option_style').val() + '"]').addClass('active');

	$('#checkbox_option_photo').on('change', function(){
		if ($(this).is(':checked')) {
            $('#print_option_photo').val('1');
            $('.ync-style').removeClass('no-photo');
        } else {
            $('#print_option_photo').val('0');
            $('.ync-style').addClass('no-photo');
        }
	});
	
	$('#checkbox_option_site_url').on('change', function(){
		if ($(this).is(':checked')) {
            $('#print_option_site_url').val('1');
            $('.print_option_site_url').show();
        } else {
            $('#print_option_site_url').val('0');
            $('.print_option_site_url').hide();
        }
	});
	
	$('#checkbox_option_location').on('change', function(){
		if ($(this).is(':checked')) {
            $('#print_option_location').val('1');
            $('.print_option_location').show();
        } else {
            $('#print_option_location').val('0');
            $('.print_option_location').hide();
        }
	});
	
	$('#checkbox_option_category').on('change', function(){
		if ($(this).is(':checked')) {
            $('#print_option_category').val('1');
            $('.print_option_category').show();
        } else {
            $('#print_option_category').val('0');
            $('.print_option_category').hide();
        }
	});
	
	$('.yns-print-slider li').click(function(){
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		$('#print_option_style').val($(this).attr('print_style'));
	});		
}

