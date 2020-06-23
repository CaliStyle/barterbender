
var ultimatevideo_playlist = {
	initValidator: function(element){
		jQuery.validator.messages.required  = "This field is required";
		$.data(element[0], 'validator', null);
		element.validate({
			errorPlacement: function (error, element) {
				if(element.is(":radio") || element.is(":checkbox")) {
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
	init: function(){
		ultimatevideo_playlist.initValidator($('#ynuv_add_playlist_form'));
		jQuery.validator.addMethod('checkCategory', function() {
			var result = false;
			if($('#ynuv_add_playlist_form #ynuv_section_category.table_right:first #js_mp_id_0').val() != ''){
				result = true;
			}
			return result;
		}, 'This field is required');
		$('#ynuv_add_playlist_form #ynuv_add_playlist_title').rules('add', {
			required: true
		});
	},
	addSort: function()
	{
		$('.sortable ul').sortable({
				axis: 'y',
				update: function (element, ui) {
					var iCnt = 0;
					$('.js_mp_order').each(function () {
						iCnt++;
						this.value = iCnt;
					});
				},
				opacity: 0.4
			}
		);	
	
	},
	ultimatevideoAddPlaylist : function()
	{	
		if(!$("#js_ultimatevideo_playlist_block_detail").length) return;
        var addPlaylistInterval;
        addPlaylistInterval = window.setInterval(function(){
            if(typeof(jQuery.validator) != 'undefined')
            {
                ultimatevideo_playlist.init();
                ultimatevideo_playlist.addSort();
                window.clearInterval(addPlaylistInterval);
            }

        },300);
	}
};
$Core.loadInit();