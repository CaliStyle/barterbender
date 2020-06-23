$Core.jobposting =
{
	sUrl: '',

	url: function (sUrl) {
		this.sUrl = sUrl;
	},

	action: function (oObj, sAction) {
		aParams = $.getParams(oObj.href);

		$('.dropContent').hide();

		switch (sAction) {
			case 'edit':
				window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
				break;
			case 'delete':
				var url = this.sUrl;
				$Core.jsConfirm({}, function () {
					window.location.href = url + 'delete_' + aParams['id'] + '/';
				}, function () {
				});
				break;
			default:

				break;
		}

		return false;
	},

	dropzoneOnSending: function (data, xhr, formData) {
		$('#js_jobposting_form_upload_images').find('input[type="hidden"]').each(function () {
			formData.append($(this).prop('name'), $(this).val());
		});
	},

	dropzoneOnSuccess: function (ele, file, response) {
		$Core.jobposting.processResponse(ele, file, response);
	},

	dropzoneOnError: function (ele, file) {

	},
	dropzoneQueueComplete: function () {
		$('#js_listing_done_upload').show();
	},
	processResponse: function (t, file, response) {
		response = JSON.parse(response);
		if (typeof response.id !== 'undefined') {
			file.item_id = response.id;
			if (typeof t.data('submit-button') !== 'undefined') {
				var ids = '';
				if (typeof $(t.data('submit-button')).data('ids') !== 'undefined') {
					ids = $(t.data('submit-button')).data('ids');
				}
				$(t.data('submit-button')).data('ids', ids + ',' + response.id);
			}
		}
		// show error message
		if (typeof response.errors != 'undefined') {
			for (var i in response.errors) {
				if (response.errors[i]) {
					$Core.dropzone.setFileError('jobposting', file, response.errors[i]);
					return;
				}
			}
		}
		return file.previewElement.classList.add('dz-success');
	}
};

$Behavior.addNewCompany = function()
{
	$('.js_jobposting_company_change_group').click(function()
	{
		if ($(this).parent().hasClass('locked'))
		{
			return false;
		}
		
		aParts = explode('#', this.href);
		
		$('.js_jobposting_company_block').hide();
		$('#js_jobposting_company_block_' + aParts[1]).show();
		$(this).parents('.header_bar_menu:first').find('li').removeClass('active');
		$(this).parent().addClass('active');
		$('#js_jobposting_company_add_action').val(aParts[1]);
	});
	
	$('.js_mp_jobindustry_category_list').change(function()
	{
		var iNo = parseInt(this.id.substr(9, 1));
        var iParentId = parseInt(this.id.replace('js_mp_id_' + iNo + '_', ''));
		
		$('.js_mp_category_list').each(function()
		{
			if (parseInt(this.id.replace('js_mp_id_' + iNo + '_', '')) > iParentId)
			{
				$('#js_mp_holder_' + iNo + '_' + this.id.replace('js_mp_id_' + iNo + '_', '')).hide();				
				
				this.value = '';
			}
		});
		
		$('#js_mp_holder_' + iNo + '_' + $(this).val()).show();
	});	
};

$Core.searchFriendsInput.processClick = function($oObj, $iUserId)
	{
		if (!isset(this.aFoundUsers[$iUserId]))
		{
			return false;
		}

		this.bNoSearch = false;
		
		var $aUser = this.aFoundUser = this.aFoundUsers[$iUserId];
		var $oPlacement = $(this._get('placement'));
		
		//$($oObj).parents('.js_friend_search_form:first').find('.js_temp_friend_search_input').val('').focus();
		$($oObj).parents('.js_friend_search_form:first').find('.js_temp_friend_search_form').html('').hide();		
		
		var $sHtml = '';
		$sHtml += '<li>';
		
		if (!this._get('inline_bubble'))
		{
			$sHtml +='<a href="#" class="friend_search_remove" title="Remove" onclick="$(this).parents(\'li:first\').remove(); return false;">'+oTranslations['remove']+'</a>';
			$sHtml += '<div class="friend_search_image" style = "position: relative;"> ';
			//$sHtml += '<img src="' + $aUser['user_image'] + '" alt="" style="width:50px; height:50px;" /></div>';
			//$sHtml += '<img src="" alt="" style="width:50px; height:50px;" /></div>';
			//$sHtml += '<img src="" alt="" style="width:50px; height:50px;" /></div>';
			$sHtml += $aUser['user_image'] +'</div>';

		}
		console.log($aUser);
		
		if (!this._get('inline_bubble'))
		{
			$sHtml += '<div class="clear"></div>';
		}
		$sHtml += '<span>' + $aUser['full_name'] + '</span>';
		$sHtml += '<div><input type="hidden" name="' + this._get('input_name') + '[]" value="' + $aUser['user_id'] + '" /></div>';
		$sHtml += '</li>';
		this.sHtml = $sHtml;
		
		if (empty($oPlacement.html()))
		{
			$oPlacement.html('<div class="js_custom_search_friend_holder" style="clear:both;"><ul' + (this._get('inline_bubble') ? ' class="inline_bubble"' : '') + '></ul>' + (this._get('inline_bubble') ? '<div class="clear"></div>' : '') + '</div>');
		}
		
		if (this._get('onBeforePrepend'))
		{			
			this._get('onBeforePrepend')(this._get('onBeforePrepend'));
		}
		
		$oPlacement.find('ul').prepend(this.sHtml);
		
		if (this._get('onclick'))
		{
			this._get('onclick')(this._get('onclick'));	
		}
		
		if (this._get('global_search')){
			window.location.href = $aUser['user_profile'];
			$($oObj).parents('.js_temp_friend_search_form:first').hide();
		}
		
		this.aFoundUsers = {};
		
		if (this._get('inline_bubble')){
			$('#' + this._get('search_input_id') + '').val('').focus();
		}
        $('.js_temp_friend_search_form').hide();
		return false;
	};
