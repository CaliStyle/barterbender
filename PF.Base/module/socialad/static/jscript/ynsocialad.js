;(function(window, undefined) {
	if (typeof jQuery != 'undefined') {
	var ynsocialad = { 
		setParams : function(params) {
			ynsocialad.params = JSON.parse(params);
		},
		confirmPurchaseByPoints: function(){
			 sHtml = "";
	        sTextInfoPurchase =  $('#sTextInfoPurchase').val();
	        sUrlPay =  $('#sUrlPay').val();
	        sHtml += '<div class="white-popup-block" style="width: 300px;">'; 
	            sHtml += '<div class="ynfevent-edit-confirm-box-title" style="font-size:12px;">'; 
	                sHtml += sTextInfoPurchase; 
	            sHtml += '</div><br/>'; 

	            sHtml += '<div class="ynsocialad-edit-confirm-box-button">'; 
	                sHtml += '<button class="btn btn-sm btn-primary" onclick="ynsocialad.yesConfirmPurchase(sUrlPay);">'; 
	                    sHtml += oTranslations['socialad.purchase']; 
	                sHtml += '</button>'; 
	                sHtml += '<button class="btn btn-sm btn-warning" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">'; 
	                    sHtml += oTranslations['socialad.cancel']; 
	                sHtml += '</button>'; 
	            sHtml += '</div>'; 

	        sHtml += '</div>'; 

	        $.magnificPopup.open({
	              items: {
	                src: sHtml, 
	                type: 'inline'
	              }
	            });

	        return false;
		},
		yesConfirmPurchase : function(sUrlPay){
			window.location.href = sUrlPay;
		}
	};
	
	jQuery.fn.extend({
		ynsaWaiting: function(type) {
			var imageHtml = '<img src="' + ynsocialad.params['fb_small_loading_image_url'] + '" class="v_middle" />';

			if(typeof type != 'undefined') {
				switch(type) {
					case 'prepend': 
						$(this).prepend(imageHtml);
						break;
				}
			} else {
				this.html(imageHtml);
			}
		},
		ynsaStopWaiting: function() {
			this.html('');
		}
	});


	var helper = { 
		addLoadingImage : function($ele, type) {
			var loadingImageHtml = '';
			switch(type) {
				case 'big' : 
					loadingImageHtml = $('#js_ynsa_loading_large_image').html();
					break;
				case 'small-fb':
					loadingImageHtml = $('#js_ynsa_loading_fb_small_image').html();
					break;
			}

			$ele.html(loadingImageHtml);
		},
		initDropdownMenu : function() { 
			$('.ynsaActionList').each(function() {                                
			   if($(this).find('.link_menu li').length <= 0) {
				   $(this).hide();
			   }
			});
			$('.js_ynsa_drop_down_link').click(function()
		    {
		    	if ($(this).hasClass('clicked')) {
		    		$('#js_drop_down_cache_menu').remove();
		    		$(this).removeClass('clicked');
		    		return false;
		    	}
		    	var ele = $(this);
		    	$(this).addClass('clicked');
		    	eleOffset = $(this).offset();
		    	
		    	$('#js_drop_down_cache_menu').remove();
		    	
		    	$('body').prepend('<div id="js_drop_down_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:9999;"><div class="link_menu dropdown open" style="display:block;position:relative;">' + $(this).parent().find('.link_menu:first').html() + '</div></div>');
		    	
				$('#js_drop_down_cache_menu .link_menu').hover(function()
				{

				},
				function()
				{
					$('#js_drop_down_cache_menu').remove();
					ele.removeClass('clicked');
				});	    	
		    	
		    	return false;
		    });
		}, 
		getTimestamp: function(prefix, isShort) {
			if(typeof isShort != 'undefined' && isShort) {
				var timeAttrib = ['year', 'month', 'day'];
			} else {
				var timeAttrib = ['year', 'month', 'day', 'hour', 'minute'];
			}
			var timeCal = {'hour': 0, 'minute': 0};
			var name;
			for(var i=0; i < timeAttrib.length; i++) {
				name = prefix + timeAttrib[i];
				var val = $("[name='val[" + name + "]']").val();
				timeCal[timeAttrib[i]] = val;
			} 

			var timeString = timeCal.year + '/' + timeCal.month + '/' + timeCal.day + ' ' + timeCal.hour + ':' + timeCal.minute;
			var timestamp = new Date(timeString).getTime();
			
			return timestamp;
		},
        getTimezoneOffset: function(){
            var offset = new Date().getTimezoneOffset();
            var sign = offset < 0 ? '+' : '-';
            offset = Math.abs(offset);
            var timezone_offset = ((Math.round((offset/60) + "e+2")  + "e-2")) * (sign == '+' ? -1 : 1);
			return timezone_offset;
        }
		
	};

	ynsocialad.helper = helper;

	var initValidator = function(element) {
		jQuery.validator.messages.required  = oTranslations['socialad.this_field_is_required'];
		jQuery.validator.messages.url       = oTranslations['socialad.please_enter_a_valid_url'];
		jQuery.validator.messages.accept    = oTranslations['socialad.validator_accept'] ;
		jQuery.validator.messages.minlength = oTranslations['socialad.validator_minlength'] ;
		jQuery.validator.messages.min       = oTranslations['socialad.validator_min'] ;
		jQuery.validator.messages.number    = oTranslations['socialad.validator_number'] ;

		jQuery.validator.messages.maxlength = oTranslations['socialad.validator_maxlength'] ;

		element.validate({
			errorPlacement: function (error, element) {
				if(element.is(":radio") || element.is(":checkbox")) {
					error.appendTo(element.parent());
				} else {
					error.appendTo(element.parent());
				}
			},
			errorClass: 'ynsaError',
			errorElement: 'span'
		});

		jQuery.validator.addClassRules("yn_positive_number", {
			range:[0,10000000000]
		});

		jQuery.validator.addClassRules("yn_positive_number_greater_than_0", {
			range:[1,10000000000]
		});

		jQuery.validator.addClassRules("yn_validation_file_type", {
			accept: "jpg|gif|jpeg|png"
		});


		jQuery.validator.addClassRules("yn_contest_title_max_length", {maxlength:255});
		jQuery.validator.addClassRules("yn_contest_short_description_max_length", {maxlength:160});
	};
	ynsocialad.initValidator= initValidator;

	var addForm = {
		isInEdit: 0,
		"$addForm" : $("#ynsa_js_add_ad_form"),
		init : function() {
			addForm.isInEdit = parseInt($("#js_ynsa_is_in_edit").val(), 10);

			$('#ynsa_location').chosen();
			$('#js_ynsa_choose_module').chosen();
			addForm.selectItem.init();
			addForm.basicInfo.init();
			addForm.display.init();
			addForm.preview.init();
			addForm.pricing.init();
			addForm.audience.init();
			ynsocialad.initValidator(addForm.$addForm);

			jQuery.validator.addMethod('checkStartCurrent', function() {
				var current = + new Date();
				var currentTimezoneOffset = ynsocialad.helper.getTimezoneOffset();
				var current = current + (currentTimezoneOffset * 3600000) + (sUserOffset * 3600000);

				var startTime = ynsocialad.helper.getTimestamp('ad_expect_start_time_');

				if(current >= startTime) {
					return false;
				} else {
					return true;
				}
			}, oTranslations['socialad.the_start_time_must_be_greater_than_current_time']);


			jQuery.validator.addMethod('checkPositiveInteger', function() {
				var str = $('#js_ynsa_number_of_package').val();
				var n = ~~Number(str);
    			return String(n) === str && n >= 0;
			}, 'Please enter a positive integer number.');

			jQuery.validator.addMethod('checkStartEnd', function() {
				var startTime = ynsocialad.helper.getTimestamp('ad_expect_start_time_');
				var endTime = ynsocialad.helper.getTimestamp('ad_expect_end_time_');
				if(startTime >= endTime) { 
					return false;
				} else {
					return true;
				}

			}, oTranslations['socialad.the_end_time_must_be_greater_than_the_start_time']);
			
			$('#ad_external_url').rules('add', {
				required: true,
				url: true
			});

			$('#js_ynsa_ad_title').rules('add', {
				required: true,
				maxlength: $('#js_ynsa_ad_title').data('limit-char')
			});

			$('#js_ynsa_ad_text').rules('add', {
				required: true,
				maxlength: $('#js_ynsa_ad_text').data('limit-char')
			});

			$('#js_campaign_name').rules('add', {
				required: true,
			});

			if($('#js_ynsa_number_of_package').length > 0) {
				$('#js_ynsa_number_of_package').rules('add', {
					required: true,
					number: true,
					checkPositiveInteger: true,
					min: 1, 
				});
			}

			$('#ad_expect_end_time_minute').rules('add', {
				checkStartEnd: true,
				checkStartCurrent: true,
			});

			ynsocialad.initValidator($('#js_ynsa_add_form_upload_image_form'));

			jQuery.validator.addMethod('checkHaveImage', function() {
				if( $('#js_ynsa_ad_image_path').val() == '') {
					return false;
				} else {
					return true;
				}

			}, oTranslations['socialad.this_field_is_required']);

			$('#js_upload_ad_size').rules('add', {
				checkHaveImage: true,
			});

			addForm.$addForm.on('submit', function() {
				if($('#js_ynsa_ad_image_path').val() == '') {
                    window.scrollTo($('#socialad-dropzone').offset());
                    $('#socialad-dropzone').addClass('dz-error');
                    ynsocialad.addForm.uploadImage.changePosition();
                    return false;
				}
				return true;
			});
			var checkTimeOut = false;
			$("#ad_external_url").on("change paste keyup", function() {
				clearTimeout(checkTimeOut);
		        checkTimeOut = setTimeout(function () {
		            ynsocialad.addForm.uploadImage.changePosition();
		        }, 300);
			});

			addForm.$addForm.on('submit', function() {
				if($('#ad_external_url').is(':visible')) {
					return true;
				} else {
					if($('#js_ynsa_ad_item').length == 0) {
						return false;
					} else {
						return true;
					}
				}

			});
		},
		getRootImageUrl : function() {
			return $('#js_ynsa_socialad_image_root').val();

		},
		getCurrenImageUrl : function(adTypeName, idxBlock) {
			var rootPath = addForm.getRootImageUrl();
			var imagePath = $('#js_ynsa_ad_image_path').val();

			if(imagePath) {
				imagePath = addForm.getRealImagePathForEachAdType(imagePath, adTypeName, idxBlock);
				return rootPath + imagePath;
			} else {
				return false;
			}
		},
		selectItem  : {
			socialad_changeItem : 0,
			socialad_changeText : 0,
			iItemTypeIdSelected : null,
			$selectItemType : $('.jsItemTypeRadio'),
			$selectItem : $.extend($('#js_ynsa_ad_select_item'), {
				refresh : function(iItemTypeId) {
					ynsocialad.addForm.selectItem.iItemTypeIdSelected = iItemTypeId;
					ynsocialad.helper.addLoadingImage(addForm.selectItem.$selectItem, 'big');
					$.ajaxCall('socialad.getItemsOfType', 'item_type_id=' + iItemTypeId);
				},
			}),
			'$inputUrl' : $('#js_ynsa_ad_external_url'),
			onChangeItemType: function() {
				var $selected = addForm.selectItem.$selectItemType.map(function() {
					if($(this).is(':checked')) {
						return $(this);
					}
				})[0];
				$selected = $($selected);
				var	itemTypeName = $selected.attr('data-name');
				var	itemTypeId = $selected.val();
				if(itemTypeName === 'external_url' ) {
					addForm.selectItem.$selectItem.hide();
					addForm.selectItem.$inputUrl.show();
                    addForm.basicInfo.changeText(oTranslations['example_ad_text']);
                    addForm.basicInfo.changeTitle(oTranslations['example_ad_title']);
				} else {
					addForm.selectItem.$selectItem.show();
					addForm.selectItem.$inputUrl.hide();
					addForm.selectItem.$selectItem.refresh(itemTypeId);
				}
			},
			selectExternalUrl: function() {

			},
			selectNormalItem: function() {
			},
			onChangeItem : function() {
				var $selected  = $(this).find(":selected"),
					title      = $selected.attr('data-title'),
					itemId     = $selected.val(),
					itemTypeId = $selected.attr('data-item-type-id'),
					text       = $selected.attr('data-description'),
					isHaveImage  = $selected.attr('data-is-have-image');

				if(isHaveImage == 1) {
					/*delete old image*/
					addForm.uploadImage.deleteCurrentImage();
					var adID = 0;
					if($('#js_ynsa_is_in_edit').val() == 1){
						/*when editing*/ 
						adID = $('#js_ynsa_ad_id').val();

						if(ynsocialad.addForm.selectItem.socialad_changeItem == 0){
							ynsocialad.addForm.selectItem.socialad_changeItem = 1;
						} else {
							ynsocialad.addForm.selectItem.socialad_changeItem = 2;
						}
						if(ynsocialad.addForm.selectItem.socialad_changeText == 0){
							ynsocialad.addForm.selectItem.socialad_changeText = 1;
						} else {
							ynsocialad.addForm.selectItem.socialad_changeText = 2;
						}
					} else {
						/*when creating*/
						ynsocialad.addForm.selectItem.socialad_changeItem = 2;
						ynsocialad.addForm.selectItem.socialad_changeText = 2;
					}

					if(ynsocialad.addForm.selectItem.socialad_changeItem == 2){
						$.ajaxCall("socialad.changeItem", "item_type_id=" + itemTypeId +
								"&item_id=" + itemId);
					}

				}

					addForm.basicInfo.changeText(text);
					addForm.basicInfo.changeTitle(title);
				

				ynsocialad.helper.addLoadingImage($('#js_ynsa_action_holder'), 'small-fb');
				$.ajaxCall("socialad.changeActionOfPreview", "item_type_id=" + itemTypeId +
							"&item_id=" + itemId);


			},
			initSelectItemList : function() {
				var $adItem =  $('#js_ynsa_ad_item');
				var sParams = '&' + getParam('sGlobalTokenName') + '[ajax]=true&' 
					+ getParam('sGlobalTokenName') + '[call]=' + 'socialad.getItemsOfType' + '';				
				sParams += '&' + getParam('sGlobalTokenName') + '[security_token]=' + oCore['log.security_token'];				
				sParams += '&' + getParam('sGlobalTokenName') + '[is_admincp]=' + (oCore['core.is_admincp'] ? '1' : '0');
				sParams += '&' + getParam('sGlobalTokenName') + '[is_user_profile]=' + (oCore['profile.is_user_profile'] ? '1' : '0');
				sParams += '&' + getParam('sGlobalTokenName') + '[profile_user_id]=' + (oCore['profile.user_id'] ? oCore['profile.user_id'] : '0');	
				if (getParam('bJsIsMobile')){
					sParams += '&js_mobile_version=true';					
				}
				sParams += '&item_type_id=' + ynsocialad.addForm.selectItem.iItemTypeIdSelected;

				var sUrl = getParam('sJsAjax');
				if (typeof oParams['im_server'] != 'undefined' && sCall.indexOf('im.') > (-1))
				{
					sUrl = getParam('sJsAjax').replace(getParam('sJsHome'),getParam('im_server'));
				}

			      $($adItem).ajaxChosen({
			         type: 'POST',
			         url: sUrl,
			         dataType: "script",	
			         data: sParams,
			      }, 
			      function (data) 
			      {
			      	var oOutput = $.parseJSON(data);
			         var terms = {};
			 
			         $.each(oOutput, function (i, val) {
			            terms[i] = val;
			         });
			 
			         return terms;
			      });
				$adItem.on('change', this.onChangeItem);
				$adItem.trigger('change');
			},
			init: function() { 
				this.$selectItemType.on('change', this.onChangeItemType );
				if(addForm.isInEdit === 0) {
					this.onChangeItemType();
				};

			}
		},
		basicInfo : { 
			'$selectCampaign' : $('#js_select_campaign'),
			'$campaignName' : $('#js_campaign_name'),
			'$campaignHolder' : $('#js_ynsa_campaign_name_holder'),
			'$adTitle' : $('#js_ynsa_ad_title'),
			'$adText' : $('#js_ynsa_ad_text'),
			'$selectAdTypeRadios' : $('.jsYnsaSelectAdType'),
			'$selectBlock' : $('#js_ynsa_select_block'),
			'$selectModule' : $('#js_ynsa_select_module'),
			'$adTextHolder' : $('#js_ynsa_text_holder'),
			'$previewHtml' : $('#js_ynsa_preview_html'),
			'$previewBanner' : $('#js_ynsa_preview_banner'),
			'$previewFeed' : $('#js_ynsa_preview_feed'),
			init: function() { 
				this.$selectCampaign.on('change', addForm.basicInfo.onChangeCampaign );
				this.$selectAdTypeRadios.on('change', addForm.basicInfo.onChangeAdType );
				$('.type_ads .ynsaSelectBigDiv').on('click', function() {
					$(this).find('input:radio').attr('checked', 'checked').trigger('change');
				});
				this.onChangeCampaign();
				this.onChangeAdType();
			},
			changeText : function(text) {
				this.$adText.val(text);
				this.$adText.trigger('keyup');
			},
			changeTitle : function (title) {
				this.$adTitle.val(title);
				this.$adTitle.trigger('keyup');
			},
			onSelectFeed: function() {
				addForm.basicInfo.$selectBlock.hide();
				addForm.basicInfo.$selectModule.hide();
				addForm.basicInfo.$adTextHolder.show();
			},
			onSelectHTML: function() {

				addForm.basicInfo.$selectBlock.hide();
				addForm.basicInfo.$selectModule.show();
				addForm.basicInfo.$adTextHolder.show();
			},
			onSelectBanner: function() {

				addForm.basicInfo.$selectBlock.show();
				addForm.basicInfo.$selectModule.show();
				addForm.basicInfo.$adTextHolder.show();
			},
			onChangeAdType: function() { 
				addForm.basicInfo.$selectAdTypeRadios.each(function() {
					if($(this).is(':checked')) {
						$(this).parent().parent().addClass('ynsaOn');
					} else {
						$(this).parent().parent().removeClass('ynsaOn');
					}
				});
				var $selected = addForm.basicInfo.$selectAdTypeRadios.map(function() {
					if($(this).is(':checked')) {
						return $(this);
					}
				})[0];
				$selected = $($selected);
				var name = $selected.attr('data-name');
				var adTypeId = $selected.val();
				switch(name) {
					case 'feed':
						addForm.basicInfo.onSelectFeed();
						break;
					case 'html':
						addForm.basicInfo.onSelectHTML();
						break;
					case 'banner' :
						addForm.basicInfo.onSelectBanner();
						break;
				}

				addForm.preview.changePreview(adTypeId, name);
			},
			onChangeCampaign: function() {
				var $selected = addForm.basicInfo.$selectCampaign.find(":selected");
				var campaignId = parseInt($selected.val(), 10);
				if(campaignId === 0 ) {
					addForm.basicInfo.$campaignHolder.show();
				} else {
					addForm.basicInfo.$campaignHolder.hide();
				}

			},
			onSelectAdTypeHtml: function() {
				var toHide = [],
					toShow = [];

				for( var i in toHide) {
					$('#' + toHide[i]).hide();
				}

				for( var i in toShow) {
					$('#' + toHide[i]).show();
				}
			}

		},

		pricing : {
			init: function() {
				$('#js_ynsa_number_of_package').on('keyup', this.onChangePrice);
				this.onChangePrice();
			},
			onChangePrice: function() {
				var $priceInput = $('#js_ynsa_number_of_package'),
					numberOfPackage = parseInt($priceInput.val(), 10),
					pricePerPackage = parseFloat($priceInput.data('price-per-package'), 10),
					benefitPerPackage = parseInt($priceInput.data('benefit-per-package'), 10);

				if(isNaN(numberOfPackage)) {
					numberOfPackage = 1;
				}

				var totalBenefit = numberOfPackage * benefitPerPackage;
				var totalPrice = numberOfPackage * pricePerPackage;

				$('#js_ynsa_total_number_of_benefit').html(totalBenefit);
				$('#js_ynsa_price').html(totalPrice);
			}

		},
		uploadImage : {
			"$uploadImageHolder": $('#js_ynsa_ad_upload_image_holder_frame'),
			"$uploadImage": $('#js_ynsa_ad_upload_image_input'),
			"$imagePath" : $('#js_ynsa_ad_image_path'),
			"$image" : $('#js_ynsa_display_ad_image'),
			onChangeImage : function() { 
				/*delete olde image*/
				if($(this).val() != '') {
					addForm.uploadImage.deleteCurrentImage();
					$(this).parent('form').submit();
				}
			},
			init: function() {
				this.$uploadImage.on('change', this.onChangeImage);
			},
			deleteCurrentImage: function() {
				
				var $imageDiv = addForm.preview.getImageDiv();
				ynsocialad.helper.addLoadingImage($imageDiv, 'small-fb');
				$.ajaxCall('socialad.deleteImage', 'image=' +  addForm.uploadImage.$imagePath.val());
                addForm.uploadImage.$imagePath.val('');
			},
            changePosition: function() {
                var position = $( "#containerUploadImage" ).position();
                $("#js_ynsa_ad_upload_image_holder_frame").css({
                    top: position.top + 4
                    , left: position.left
                    , position:'absolute'
                });                            
            }
 
		},
		dropzoneOnSending: function(data, xhr, formData) {
            addForm.uploadImage.deleteCurrentImage();
		},
		dropzoneOnComplete: function(ele, file, response) {
            response = JSON.parse(response);
            if (typeof response.file_name != 'undefined') {
            	addForm.changeImage(response.file_name);
			}
		},
        dropzoneOnError: function() {

		},
        dropzoneRemoveCurrent: function() {
            addForm.uploadImage.deleteCurrentImage();
		},
		getRealImagePathForEachAdType : function(imagePath, adTypeName, idxBlock) {
			if(addForm.preview.adTypeName == 'html') {

				imagePath = imagePath.replace('%s', '_html');

			} else if(addForm.preview.adTypeName == 'banner') {

				 /*imagePath = imagePath.replace('%s', '');*/
				imagePath = imagePath.replace('%s', '_block' + idxBlock);

			} else if(addForm.preview.adTypeName == 'feed') {

				imagePath = imagePath.replace('%s', '_feed');

			}

			return imagePath;
		},
		changeImage: function(imagePath) {
			var adCoreUrl = addForm.getRootImageUrl();
			var imageFullUrl = adCoreUrl + imagePath;
			console.log(imageFullUrl);

		
			var html = '<img id="js_ynsa_display_ad_image" src="' + imageFullUrl + '" />';  /*it is just evil*/
			addForm.uploadImage.$imagePath.val(imagePath);
			$(ynsocialad).trigger('image_changed');

		},
		display : {
			"$title" : $('#js_ynsa_display_ad_title'),
			"$text" : $('#js_ynsa_display_ad_text'),
			"$image" : $('#js_ynsa_display_ad_image'),
			"$imageDiv" : $('#js_ynsa_display_ad_image_div'),
			onChangeValue : function() {
				var title = addForm.basicInfo.$adTitle.val(),
					text = addForm.basicInfo.$adText.val();
				addForm.preview.updatePreviewTitle(title);
				addForm.preview.updatePreviewText(text);

				addForm.display.showMessage(this);
			},
			showMessage: function(ele) {
				$ele = $(ele);
				var limit = parseInt($ele.attr("data-limit-char"), 10);
				var currentChar = $ele.val().length;
				var remain = limit - currentChar;
				var $infoDiv = $ele.parent().find('.js_limit_info');
				if(remain >= 0) {
					$infoDiv.html(oTranslations['socialad.number_character_left'].replace("{number}", remain));
				} else {
					remain = 0 - remain;

					$infoDiv.html("<span style='color:red'>" + oTranslations['socialad.number_character_over_limit'].replace("{number}", remain) + "</span>");

				}

			},
			init : function() {
				addForm.basicInfo.$adTitle.on('keyup' , this.onChangeValue);
				addForm.basicInfo.$adText.on('keyup' , this.onChangeValue);
			}
		},
		audience: {
			init : function() {
				$('.ynsaAudience').on('change', this.onChangeAudience);
				$(document).bind('audiencechanged', this.audienceChanged);
				this.onChangeAudience();

			},
			audienceChanged: function(evt, numAffected) {
				$('#js_ynsa_number_affected_audience').html( numAffected);
			},
			onChangeAudience: function() {
				var sParam = '';
				if(ynsocialad.params) { 
					$('#js_ynsa_number_affected_audience').ynsaWaiting();
				}
				$('.ynsaAudience').each(function() {
					var name = this.name;
					sParam += '&';
					if(this.type == 'select-multiple') {
						for (var j = 0; j < this.length; j++) {
							if (this.options[j].selected == true) {
								sParam += name+"="+encodeURIComponent(this.options[j].value)+"&";
							}
						}
					} else {
						sParam += name+"="+encodeURIComponent(this.value);
					}
				});

				$.ajaxCall('socialad.changeAudience', sParam);

			},
		},

		preview : {
			$previewHolder : $('#js_ynsa_preview_holder'),
			$currenImageHolder: false,
			adTypeName: false,
			$defaultImageDiv : $('#js_ynsa_display_ad_image_div'),
			changePreview : function(adTypeId, adTypeName) { 
				switch(adTypeName) {
					case 'feed':
						$('#ynsa_is_show_guest').show();
						$('#ynsa_label_is_show_guest').show();
						break;
					case 'html':
						$('#ynsa_is_show_guest').prop('checked', false);
						$('#ynsa_is_show_guest').hide();
						$('#ynsa_label_is_show_guest').hide();
						break;
					case 'banner' :
						$('#ynsa_is_show_guest').prop('checked', false);
						$('#ynsa_is_show_guest').hide();
						$('#ynsa_label_is_show_guest').hide();
						break;
				}

				ynsocialad.helper.addLoadingImage(addForm.preview.$previewHolder, 'small-fb');
				ynsocialad.addForm.preview.adTypeName = adTypeName;
				var ynsa_ad_id = $('#js_ynsa_ad_id').val(); 
				$.ajaxCall('socialad.changePreviewBox', 'ad_type_id=' + adTypeId + '&ynsa_ad_id=' + ynsa_ad_id);
			},
			setPreview : function(html) {
				this.$previewHolder.html(html);
				this.disablePreviewSide();
				this.updatePreview();

			},
			disablePreviewSide: function() {
				addForm.preview.$previewHolder.find('*').click(function() {return false;});
				addForm.preview.$previewHolder.find('*').attr('onclick', '');
			},
			getTitlePreviewDiv: function() {
				if(this.adTypeName == 'html') {
					return $('#js_ynsa_display_ad_title');
				} else if (this.adTypeName == 'feed') {
					return $('.js_add_feed_title');
				}
			},
			getTextPreviewDiv: function() {
				if(this.adTypeName == 'html') {
					return $('#js_ynsa_display_ad_text');
				} else if (this.adTypeName == 'feed') {
					return $('.js_add_feed_text');
				}
			},
			updatePreviewTitle: function(title) {
				var div = this.getTitlePreviewDiv();	
				if(!div) return false;
				title = title.substring(0, 25);
				div.text(title);
			},
			updatePreviewText: function(text) {
				var div = this.getTextPreviewDiv();	
				text = text.substring(0, 90);
				if(this.adTypeName == 'html') {
					div.text(text);
				} else if (this.adTypeName == 'feed') {
					var spanHtml = $('<div />').append(div.find('span').clone()).html();
					div.html(spanHtml + ' ' + text);
				}
			},
			changePreviewImage: function() {
				var $imageDiv = this.getImageDiv();
				var idxBlock = 0;
				if (this.adTypeName == 'banner') {
					idxBlock = parseInt($('#js_ynsa_placement_block_id').val());
				}

				var imageUrl = addForm.getCurrenImageUrl(this.adTypeName, idxBlock);
				if(!imageUrl) {
					return false;
				}

				if(this.adTypeName == 'feed') {
					$imageDiv.css("background-image", "url('"+imageUrl+"')");
				}
				else if (this.adTypeName == 'banner') {
					var idxBlock = $imageDiv.data('block-list');
					addForm.preview.removeAllBannerCurrentPreviewImage();
					var width = $imageDiv.width();
					var height = $imageDiv.height();

					var html = '<img id="js_ynsa_display_ad_image" src="' + imageUrl + '" />'; 
					$imageDiv.html(html);
				}
				else { 
					var html = '<img id="js_ynsa_display_ad_image" src="' + imageUrl + '" />'; 
					$imageDiv.html(html);
				}


			},
			removeAllBannerCurrentPreviewImage : function() {
				$('#js_ynsa_preview_holder .ynsaPreviewBlock img').each(function() {
					$(this).remove();
				});
			},
			getImageDiv: function() {
				var $imageDiv = '';
				if(this.adTypeName == 'feed') {
					$imageDiv = $('.social-ad-feed-image .item-media');
				}
				else if (this.adTypeName == 'banner') {
					var chosenBlock = $('#js_ynsa_placement_block_id').val();
					$imageDiv = $('#js_ynsa_preview_holder .ynsaPreviewBlock').map(function() {
						if($(this).data('block-list')) {
							var data = $(this).data('block-list').toString().split(',');
							if($.inArray(chosenBlock.toString(), data) !== -1) {
								return $(this);
							}
						}
					})[0];
				}
				else { 
					$imageDiv = $('#js_ynsa_display_ad_image_div');
				}

				return $imageDiv;
			},
			updatePreview: function() {
				this.changePreviewImage();
				addForm.display.onChangeValue();
			},
			init: function() {
				$(ynsocialad).bind('image_changed', function() { 
					addForm.preview.changePreviewImage();
				});

				$('#js_ynsa_placement_block_id').on('change', function() {
					addForm.preview.changePreviewImage();	
				});

			},
		},

	};




	var chart = {
		divPrefix: "ynsa_chart_holder_",
		$waitingSymbol: $('#js_ynsa_chart_waiting_symbol'),
		adId : null,
		init: function(adId) { 
			chart.adId = adId;

			google.setOnLoadCallback(chart.getAdData);
			$('#js_ynsa_chart_control_form').find('select').on('change', function() { 
				ynsocialad.chart.getAdData();
			});

		},
		getAdData : function() {
			/*
			 for phpfox problem with ajax, we cannot specify the callback here
			 for ease of use, the callback when data returned is onHandleAdData
			*/
			if($('#ynsa_period_history').val() == 4){
				 /*Range of dates*/
				$('#ynsa_range_of_dates_picker').show();
			} else {
				$('#ynsa_range_of_dates_picker').hide();
				$('#js_ynsa_chart_control_form').ajaxCall('socialad.chartGetData');
				ynsocialad.chart.$waitingSymbol.ynsaWaiting();								
			}
		},
		updateWithRangeOfDates : function() {
			$('#js_ynsa_chart_control_form').ajaxCall('socialad.chartGetData');
			ynsocialad.chart.$waitingSymbol.ynsaWaiting();								
		}, 
		onHandleAdData : function(jsonData) {
			var div = document.getElementById(chart.divPrefix + chart.adId + '');
			var width = div.clientWidth;
			 /*Create the data table.*/
			var data = new google.visualization.DataTable(jsonData);
			 /*Set chart options*/
			var options = {'title':'',
			             'width'  : width,
			             'height' : 300,
						 'vAxis'  : {
							 'format'         : "####.#",
							 'viewWindowMode' : "explicit",
							  viewWindow:{ min: 0 }
						 } };

			 /*Instantiate and draw our chart, passing in some options.*/
			var chartDiv = new google.visualization.LineChart(document.getElementById(chart.divPrefix + chart.adId + ''));
			ynsocialad.chart.currentChart = chartDiv;
			chartDiv.draw(data, options);
			ynsocialad.chart.$waitingSymbol.ynsaStopWaiting();
		}

	};


	var package = { 
		addForm : {
			$addForm: $('#js_ynsa_add_package_form'),
			$freeCheckbox : $('#js_ynsa_free_checkbox'),
			$unlimitedCheckbox : $('#js_ynsa_unlimited_checkbox'),
			$priceDiv : $('#js_ynsa_price_div'),
			$benefitDiv : $('#js_ynsa_benefit_div'),
			onFreeCheckboxChange: function() {
				if(package.addForm.$freeCheckbox.is(':checked')) {
					package.addForm.$priceDiv.hide();	
				} else {
					package.addForm.$priceDiv.show();	
				}
			},
			onUnlimitedCheckboxChange: function() {
				if(package.addForm.$unlimitedCheckbox.is(':checked')) {
					package.addForm.$benefitDiv.hide();	
				} else {
					package.addForm.$benefitDiv.show();	
				}
			},
			init : function() {
				$('#js_ynsa_package_choose_module').chosen();
				$('#js_ynsa_package_choose_block').chosen();
				$('#js_ynsa_package_choose_item_type').chosen();
				$('#js_ynsa_package_choose_ad_type').chosen();
				package.addForm.$freeCheckbox.change(package.addForm.onFreeCheckboxChange);
				package.addForm.$unlimitedCheckbox.change(package.addForm.onUnlimitedCheckboxChange);
				ynsocialad.initValidator(package.addForm.$addForm);

				$('#package_name').rules('add', {
					required: true
				});
				
				$('#js_ynsa_package_price').rules('add', {
					required: true,
					number: true,
					min: 1
				});

				$('#js_ynsa_package_benefit_number_input').rules('add', {
					required: true, 
					number: true,
					min: 1
				});
				package.addForm.onFreeCheckboxChange();  /*to change in case of editing */
				package.addForm.onUnlimitedCheckboxChange();  /*to change in case of editing */
			}
		}
	};
	ynsocialad.addForm = addForm;
	ynsocialad.chart = chart;
	ynsocialad.package = package;

	var report = {
		selectCampaignId : '#js_ynsa_report_select_campaign',
		selectAdHolderId : '#js_ynsa_report_select_ad_holder',
		selectAdId : '#js_ynsa_report_select_ad',
		isBindCalendar : false,
		reportTableHolderId : '#js_ynsa_report_table_holder',
		initReportForm : function() {
			$(report.selectCampaignId).chosen();
			$(report.selectCampaignId).on('change', report.updateAdFromCampaign);
			ynsocialad.initValidator($('#js_ynsa_report_form'));

			jQuery.validator.addMethod('checkStartEnd', function() {
				var isShort = true;
				var startTime = ynsocialad.helper.getTimestamp('start_', isShort);
				var endTime = ynsocialad.helper.getTimestamp('end_', isShort);
				if(startTime >= endTime) { 
					return false;
				} else {
					return true;
				}

			}, oTranslations['socialad.the_end_time_must_be_greater_than_the_start_time']);
			
			if ($('#js_ynsa_report_summary').length > 0) {
				$('#js_ynsa_report_summary').rules('add', {
					checkStartEnd: true,
				});
			}
			
			$('#js_ynsa_export_report_btn').on('click', function() {
				var actionUrl = $(this).data('action-url');
				$('#js_ynsa_report_form').attr('action', actionUrl);	
				$('#js_ynsa_report_form').attr('method', 'POST');	
				$('#js_ynsa_report_form').submit();	
				return true;
			});
			
			$(".ynsaAdTimeContent").on('click', function () {
				if(!ynsocialad.report.isBindCalendar) {
					$('#ui-datepicker-div').on('click', function(e) { 
						if($(e.target).hasClass('ui-state-default')) {
							$('#js_ynsa_report_form').trigger('change');
						};
					});
					ynsocialad.report.isBindCalendar = true;
				};
				
			} );


			setTimeout(function() {
				report.updateReportData();
			}, 500);
		},
		
		updateAdFromCampaign: function() {
			var campaignId = $(report.selectCampaignId).val();
			$.ajaxCall('socialad.reportChangeAdList', 'campaign_id=' + campaignId);
			$(report.selectAdHolderId).ynsaWaiting();
		},
		changeHtmlOfSelectAd: function(html) {
			$(report.selectAdHolderId).html(html);
			$(report.selectAdId).chosen();
		},
		updateReportData: function() {
			$('#js_ynsa_report_form').ajaxCall('socialad.reportLoadData');
			$(report.reportTableHolderId).ynsaWaiting();
		},
		changeHtmlOfReportTable: function(html) {
			$(report.reportTableHolderId).html(html);
		}
	};
	ynsocialad.report = report;
	
	$.fn.extend({
		clickableTable: function(options) {
			return this.each(  /*in case we want to init multiple elements*/
				function() {
					var $this;
					$this = $(this);
					$this.on('click', function(e) {
						if($(e.target).hasClass('jsYnsaSortingClick')) {
							sorting.sort.apply(e.target);
						}
					});
				}
			);
		},
	});


	var sorting = {

		sort : function() {
			var field     = $(this).data('field-name'),
				action    = $(this).data('action-type'),
				name      = 'val[order]',
				delimiter = '|',
				order     = '',
				nextOrder = '';

			if(action === 'down') {
				var order = 'DESC';
				var nextOrder =  'up';

			} else {
				var order = 'ASC';
				var nextOrder =  'down';

			}

			var data = field + delimiter + order;
			$(document).trigger('changeCustom', [name, data]);

		},
	};

	ynsocialad.sorting = sorting;

	var review = {
		setPreviewBannerBlock: function(blockId, imageFullUrl) {
			var chosenBlock = blockId;
			var chosenDiv = $('.ynsaPreviewBlock').map(function() {
				if($(this).data('block-list')) {
					var data = $(this).data('block-list').toString().split(',');
					if($.inArray(chosenBlock.toString(), data) !== -1) {
						return $(this);
					}
				}
			})[0];

			var html = '<img id="js_ynsa_display_ad_image" src="' + imageFullUrl + '" />';  /*it is just evil*/
			if (typeof(chosenDiv) != 'undefined' && chosenDiv.length > 0)
			chosenDiv.html(html);
			
		}
	};
	ynsocialad.review = review;

	var faq = {
		submitAddNewFAQ: function(isAdmin) {
			var params = {				
                js_ynsa_question: $("#js_ynsa_question").val()
            };

			$('#js_ynsa_faq_confirmbtn').attr('disabled','disabled');
			$("#js_ynsa_question_err").html('');

			$.ajaxCall('socialad.submitAddNewFAQ',$.param(params));
			return false;			
		}
	};
	ynsocialad.faq = faq;

	var addrequest = {
		submitAddRequest: function(isAdmin) {
			var params = {				
                amount: $("#js_ynsa_addrequest_amount").val()
                , reason: $("#js_ynsa_addrequest_reason").val()
                , creditmoney_id: $("#js_ynsa_creditmoney_id").val()
            };

			if(isAdmin == 1){
				params.yncm_user_id = $("#yncm_user_id").val();
			}

			$('#js_ynsa_addrequest_confirmbtn').addClass('disabled');
			$("#js_ynsa_addrequest_amount_err").html('');
			$("#js_ynsa_addrequest_reason_err").html('');

			$.ajaxCall('socialad.submitAddRequest',$.param(params),'POST');
			return false;			
		}
		, acceptPendingCreditMoneyRequest: function(id, creditmoneyrequest_creditmoney_id) {
			$.ajaxCall('socialad.acceptPendingCreditMoneyRequest',$.param({
				id: id,
				creditmoneyrequest_creditmoney_id: creditmoneyrequest_creditmoney_id
			}),'POST');
			return false;			
		}
		, rejectPendingCreditMoneyRequest: function(id, creditmoneyrequest_creditmoney_id) {
			$.ajaxCall('socialad.rejectPendingCreditMoneyRequest',$.param({
				id: id
                , creditmoneyrequest_creditmoney_id: creditmoneyrequest_creditmoney_id
            }),'POST');
			return false;			
		}
	};
	ynsocialad.addrequest = addrequest;

	window.ynsocialad = ynsocialad;

	}
})(window, undefined);

$Behavior.remove_dropdown_cache_menu = function() {
    $(document).mouseup(function(e) {
        if($("#js_drop_down_cache_menu").length > 0){
            var container = $("#js_drop_down_cache_menu");
            if (!container.is(e.target) && container.has(e.target).length === 0)
            {
                $('#js_drop_down_cache_menu').remove();
            }
        }
    });
}

