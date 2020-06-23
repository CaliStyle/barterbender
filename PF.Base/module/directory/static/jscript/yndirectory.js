$Core.yndirectory =
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
		$('#js_yndirectory_form_upload_images').find('input[type="hidden"]').each(function () {
			formData.append($(this).prop('name'), $(this).val());
		});
	},

	dropzoneOnSuccess: function (ele, file, response) {
		$Core.yndirectory.processResponse(ele, file, response);
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
					$Core.dropzone.setFileError('yndirectory', file, response.errors[i]);
					return;
				}
			}
		}
		return file.previewElement.classList.add('dz-success');
	}
};

(function(window, undefined) {
	$.fn.extend({
		yndWaiting: function(type) {
			var imageHtml = '<img src="' + yndirectory.params['fb_small_loading_image_url'] + '" class="v_middle" />';

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
		yndStopWaiting: function() {
			this.html('');
		}
	});
})(window, undefined);


var yndirectory = {
	pt : []
    , cookieCompareItemName : 'yndirectory_compare_name'
    , params : false
	, setParams : function(params) {
		yndirectory.params = JSON.parse(params);
	}
	, alertMessage: function(message){
        tb_show(oTranslations['notice'], '', null, message);
	}
	, init: function()
	{
		if($('#yndirectory_pagename').length > 0 ){
			var yndirectory_pagename = $('#yndirectory_pagename').val();
			switch(yndirectory_pagename){
				case 'managerolesetting':
					yndirectory.initManageRoleSetting();
					break;
				case 'managepages':
					yndirectory.initManagepages();
					break;
				case 'businesstype':
					yndirectory.initBusinesstype();
					break;
				case 'add':
					yndirectory.initAdd();
					break;
				case 'edit':
					yndirectory.initEdit();
					break;
				case 'index':
					yndirectory.initIndex();
					break;
				case 'detail':
					 yndirectory.initDetail();
					break;
				case 'comparebusiness':
					yndirectory.initComparebusiness();
					break;
			}
		}

		yndirectory.autoCheckedCompareCheckbox();
	}
	, showAnnouncement  : function(ele, announcement_id){
		tb_show(oTranslations['directory.announcement_detail'], $.ajaxBox('directory.showAnnouncement', 'height=200&width=600&announcement_id=' + announcement_id));
	}
	, featureInBox  : function(ele, iBusinessId){
		tb_show(oTranslations['directory.business'], $.ajaxBox('directory.featureInBox', 'height=300&width=420&iBusinessId=' + iBusinessId));
	}
	, clickClaimBusinessButton  : function(ele){
		var $ele = $(ele);
		$('#yndirectory_loading').show();
        $Core.ajax('directory.clickClaimBusinessButton',
        {
            type: 'POST',
            params:
            {
                business_id: $ele.data('detailclaimingbuttonbusinessid')
            },
            success: function(sOutput)
            {
            	$('#yndirectory_loading').hide();
            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
                	yndirectory.alertMessage(oOutput.message);
                	$ele.hide();
                	window.location.reload();
                } else {
                	yndirectory.alertMessage(oOutput.message);
					window.location.href = window.location.href;
                }
            }
        });
	}
	, initComparebusiness  : function(){
		$("#yndirectory_comparebusiness_detail_category").change(function(){
			var sCompareLink = $(this).data('comparelink');
			var option = $(this).find('option:selected');
			var selected = this.value;
			var comparedetailtotalitem = $(option).data('comparedetailtotalitem');
			if(comparedetailtotalitem > 1){
				sCompareLink += 'category_' + selected + '/';
				window.location.href = sCompareLink;
				return true;
			} else {
				yndirectory.alertMessage(oTranslations['directory.please_select_more_than_one_entry_for_the_comparison']);
				return false;
			}
		});
	}
	, initCompareItemBlock: function(){
		$('div.business-item').each(function (index) {
			var item = $(this).find('.business-item-compare input:checkbox');
			$(item).off('change').on('change', function () {
				if ($(this).is(":checked")) {
					$(this).closest('.business-item').addClass('has-check');
				} else {
					$(this).closest('.business-item').removeClass('has-check');
				}
			});
		});

		var name = yndirectory.cookieCompareItemName;
		var data = getCookie(name);
		if(null != data && '' != data){
			$.each(data.split(','), function(key, value) {
                $('div.business-item .business-item-compare input:checkbox[data-compareitembusinessid=' + value + ']').prop('checked', true).trigger('change');
			});

	        $Core.ajax('directory.initCompareItemBlock',
	        {
	            type: 'POST',
	            params:
	            {
	                listOfBusinessIdToCompare: data
	            },
	            success: function(sOutput)
	            {
	            	var oOutput = $.parseJSON(sOutput);
	                if(oOutput.status == 'SUCCESS')
	                {
	                	var aCategory = oOutput.aCategory;
	                	var idx = 0;
	                	var idx2 = 0;
	                	var sHtml = '';
	                	var sHtml_tabs_menu = '';
	                	var sHtml_tabs_container = '';
                		for(x in oOutput.aCategory){
                			var aCategory = oOutput.aCategory[x];
                			var list_business = aCategory.list_business;
                			var id_tabcontent = 'yndirectory_compare_tabcontent_' + aCategory.data.category_id;
                			sHtml_tabs_menu += '<li id="yndirectory_compare_tab_menu_item_' + aCategory.data.category_id + '" data-counting="' + list_business.length + '">';
	                			sHtml_tabs_menu += '<a href="#' + id_tabcontent + '" rel="' + id_tabcontent + '">' + aCategory.data.title;
	                				sHtml_tabs_menu += ' <span id="yndirectory_compare_tab_menu_counting_' + aCategory.data.category_id + '">(' + list_business.length + ')';
	                				sHtml_tabs_menu += '</span>';
	                			sHtml_tabs_menu += '</a>';
                				sHtml_tabs_menu += ' <span onclick="yndirectory.removeItemOutCompareDashboardWithCategoryId(' + aCategory.data.category_id + ');"><i class="ico ico-close-circle"></i>';
                				sHtml_tabs_menu += '</span>';
                			sHtml_tabs_menu += '</li>';

            				sHtml_tabs_container += '<div id="' + id_tabcontent + '">';
            					//sHtml_tabs_container += '<div>';
                					sHtml_tabs_container += '<ul id="yndirectory_compare_tabs_container_list_' + aCategory.data.category_id + '">';
                			for(idx2 = 0; idx2 < list_business.length; idx2 ++){
		                				sHtml_tabs_container += '<li id="yndirectory_compare_tabs_container_item_' + list_business[idx2].business_id + '">';
			                				sHtml_tabs_container += '<div class="yndirectory-compare-tabs-container-image">';
				                				sHtml_tabs_container += '<a href="' + list_business[idx2].item_link + '">';
					                				sHtml_tabs_container += '<img src="' + list_business[idx2].logo_path + '" />';
				                				sHtml_tabs_container += '</a>';
			                				sHtml_tabs_container += '</div>';
			                				sHtml_tabs_container += '<span class="yndirectory-compare-tabs-container-item-close" onclick="yndirectory.removeItemOutCompareDashboardWithBusinessId(' + list_business[idx2].business_id + ');"><i class="ico ico-close"></i>';
			                				sHtml_tabs_container += '</span>';
			                				sHtml_tabs_container += '<span class="yndirectory-compare-tabs-container-item-title">';
				                				sHtml_tabs_container += '<a href="' + list_business[idx2].item_link + '">' + list_business[idx2].name;
				                				sHtml_tabs_container += '</a>';
			                				sHtml_tabs_container += '</span>';

			                				sHtml_tabs_container += '<div style="display: none;">';
			                					sHtml_tabs_container += '<input type="checkbox" ';
			                					sHtml_tabs_container += ' data-compareitembusinessid="' + list_business[idx2].business_id + '"';
			                					// sHtml_tabs_container += ' data-compareitemname="' + list_business[idx2].item_link + '"';
			                					sHtml_tabs_container += ' data-compareitemlink="' + list_business[idx2].item_link + '"';
			                					sHtml_tabs_container += ' data-compareitemlogopath="' + list_business[idx2].logo_path + '"';
			                					sHtml_tabs_container += ' onclick="yndirectory.clickCompareCheckbox(this);" ';
			                					sHtml_tabs_container += ' class="yndirectory-compare-checkbox">';
			                				sHtml_tabs_container += '</div>';

		                				sHtml_tabs_container += '</li>';
                			}
                					sHtml_tabs_container += '</ul>';
            					//sHtml_tabs_container += '</div>';
            				sHtml_tabs_container += '</div>';
                		}

                		sHtml += '<div id="yndirectory_compare_dashboard_content">';
	                		sHtml += '<div id="yndirectory_compare_dashboard_tabs"></div>';
		                	sHtml += '<div id="yndirectory_compare_dashboard">';
			                	sHtml += '<div id="yndirectory_compare_header">';
				                	sHtml += '<div id="yndirectory_compare_button_hide" onclick="yndirectory.minimizeCompareDashboard(true);"><i class="ico ico-angle-down"></i>';
				                	sHtml += '</div>';
			                	sHtml += '</div>';
			                	sHtml += '<div id="yndirectory_compare_tabs">';
			                		sHtml += '<ul id="yndirectory_compare_tabs_menu">';
			                			sHtml += sHtml_tabs_menu;
			                			sHtml += '<li class="yndirectory_compare_tab_action_btn"><div id="yndirectory_compare_button_compare" class="btn btn-xs btn-primary" onclick="yndirectory.redirectCompareDetail(this);" data-comparelink="' + oOutput.sCompareLink + '">' + oTranslations['directory.compare'];
				                		sHtml += '</div></li>';
			                		sHtml += '</ul>';
			                		sHtml += '<div id="yndirectory_compare_tabs_container">';
			                			sHtml += sHtml_tabs_container;
			                		sHtml += '</div>';
			                	sHtml += '</div>';
		                	sHtml += '</div>';
		                	sHtml += '<div id="yndirectory_compare_dashboard_hidden" style="display: none;">';
		                	sHtml += '</div>';
	                	sHtml += '</div>';
	                	sHtml += '<div id="yndirectory_compare_dashboard_min" data-toggle="tooltip" data-placement="left" title="'+ oTranslations['directory.compare'] + '" style="display: none;">';
	                		sHtml += '<span class="btn btn-sm btn-primary btn-round" onclick="yndirectory.maximizeCompareDashboard(true);"><i class="ico ico-merge-file-o"></i>';
	                		sHtml += '</span>';
	                	sHtml += '</div>';

	                	// set inner html
	                	$('#yndirectory_business_compareitem').html(sHtml);

	                	// bind event
	                	// $( "#yndirectory_compare_tabs" ).tabs();
						$('#yndirectory_compare_tabs_menu').each(function(){
							// For each set of tabs, we want to keep track of
							// which tab is active and it's associated content
							var $active, $content, $links = $(this).find('a');

							// If the location.hash matches one of the links, use that as the active tab.
							// If no match is found, use the first link as the initial active tab.
							$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
							$active.addClass('yndirectory-compare-tab-menu-active');

							$content = $($active[0].hash);

							// Hide the remaining content
							$links.not($active).each(function () {
								$(this.hash).hide();
							});

							// Bind the click event handler
							$(this).on('click', 'a', function(e){
								// Make the old tab inactive.
								$active.removeClass('yndirectory-compare-tab-menu-active');
								$content.hide();

								// Update the variables with the new link and content
								$active = $(this);
								$content = $(this.hash);

								// Make the tab active.
								$active.addClass('yndirectory-compare-tab-menu-active');
								$content.show();

								// Prevent the anchor's default click action
								e.preventDefault();
							});
						});

						$('#yndirectory_compare_dashboard').css('width', $('#content_holder').width() );
						$('#yndirectory_compare_dashboard_min').css('width', $('#content_holder').width() );
						$('#yndirectory_compare_dashboard_tabs').css('height', $('#yndirectory_compare_tabs_menu').height()+2 );
						$('#yndirectory_compare_dashboard_content').css('display', 'block' );

						// check status history of compare
						var yndirectory_comparebox_show_name = 'yndirectory_comparebox_show';
						var yndirectory_comparebox_show_data = getCookie(yndirectory_comparebox_show_name);
						switch(yndirectory_comparebox_show_data){
							case 'minimize':
								yndirectory.minimizeCompareDashboard(false);
								break;
							case 'maximize':
								yndirectory.maximizeCompareDashboard(false);
								break;
							default:
								yndirectory.maximizeCompareDashboard(false);
								break;
						}

	                } else {
	                	yndirectory.alertMessage(oOutput.message);
	                }
	            }
	        });
		}
	}
	, autoCheckedCompareCheckbox: function(){
		var name = yndirectory.cookieCompareItemName;
		var data = getCookie(name);
		var aData = [];
		var idx = 0;
		var idx2 = 0;
		if(null === data || typeof(data) == 'undefined'){
			data = '';
		} else {
			aData = data.split(",");
		}

		var $body = $('body');
		for(idx = 0; idx < aData.length; idx ++){
			// check brother checkbox
			var $ele = $body.find('[data-compareitembusinessid="' + aData[idx] + '"]');
			if($ele.length > 0){
				for(idx2 = 0; idx2 < $ele.length; idx2 ++){
					$ele[idx2].checked = true;

					// change title of compare button on detail page
					$parentEle = $($ele[idx2]).closest('ul.yndirectory-detailcheckinlist');
					if($parentEle.length > 0){
						$menucomparebutton = $parentEle.find('#yndirectory_detailcheckinlist_comparebutton');
						if($menucomparebutton.length > 0){
							$menucomparebutton.html('<i class="fa fa-files-o"></i> ' + oTranslations['directory.remove_from_compare']);
						}
					}

				}
			}
		}
	}
	, transferownerBusiness: function(){
		if($("#js_custom_search_friend_placement").length > 0){
			if($("#js_custom_search_friend_placement input[name='owner[]']").length > 0){
				var iBusinessId = $('#owner_business_id').val();
				var iUserId = 0;
				$("#js_custom_search_friend_placement input[name='owner[]']").each(function(){
					iUserId = this.value;
				});

				$('#yndirectory_loading').show();
				$.ajaxCall('directory.transferownerBusiness', 'iBusinessId=' + iBusinessId + '&iUserId=' + iUserId);
			}
		}
	}
	, click_detailcheckinlist_checkinhere: function(ele, business_id){
		$.ajaxCall('directory.checkinhere', 'iBusinessId=' + business_id);
	}
	, click_detailcheckinlist_promotebusiness: function(ele, business_id){
		tb_show(oTranslations['directory.promote_business'], $.ajaxBox('directory.getPromoteBusinessBox', 'height=300&width=380&iBusinessId=' + business_id));
	}
	, click_detailcheckinlist_transferowner: function(ele, business_id){
		tb_show(oTranslations['directory.transfer_owner'], $.ajaxBox('directory.openTransferownerBusiness', 'height=300&width=530&frontend=1&iBusinessId=' + business_id));
	}
	, click_yndirectory_detailcheckinlist_comparebutton: function(ele, business_id){
		var eleCheckbox = $(ele).closest('li').find('.yndirectory-compare-checkbox')[0];
		$menucomparebutton =  $(ele).closest('li').find('#yndirectory_detailcheckinlist_comparebutton');

		if(eleCheckbox.checked){
			yndirectory.removeItemOutCompareDashboardWithBusinessId(business_id);
			$menucomparebutton.html('<i class="fa fa-files-o"></i> ' + oTranslations['directory.add_to_compare']);
		} else {
			eleCheckbox.checked = true;
			yndirectory.clickCompareCheckbox(eleCheckbox);
			$menucomparebutton.html('<i class="fa fa-files-o"></i> ' + oTranslations['directory.remove_from_compare']);
		}

	}
	, printBusinessDetail: function(ele, business_id){
		window.print();
	}
	, downloadBusinessDetail: function(ele, business_id){
		$('#yndirectory_detail_hidden').find('a.no_ajax_link')[0].click();
	}
	, canManageBusiness : function(){
		if($('#yndirectory_can_manage_businesss').val() == 1){
			return true;
		}
			return false;
	}
	, canTransferBusiness : function(){
		if($('#yndirectory_can_transfer_businesss').val() == 1){
			return true;
		}
			return false;
	}, isClaimingDraft : function(){
		if($('#yndirectory_is_claiming_draft').val() == 1){
			return true;
		}
		return false;
	}
	, canCloseBusiness : function(){
		if($('#yndirectory_can_close_business').val() == 1){
			return true;
		}
		return false;
	}
	, canOpenBusiness : function(){
		if($('#yndirectory_can_open_business').val() == 1){
			return true;
		}
		return false;
	}
	, canDeleteBusiness : function(){
		if($('#yndirectory_can_delete_business').val() == 1){
			return true;
		}
		return false;
	}
	, canPublishBusiness : function(){
		if($('#yndirectory_can_publish_business').val() == 1){
			return true;
		}
		return false;
	}
	 ,displayLikeButtonTheme2 : function(){
		if($('#yndirectory_displayLikeButtonTheme2').val() == 2){
			return true;
		}
			return false;
	 }
	 ,isLiked : function(){
		if($('#yndirectory_business_isliked').val() == 1){
			return true;
		}
			return false;
	 }
	 ,click_like_page : function(iBusinessId){
		iBusinessId = $('#yndirectory_business_item_id').val();
	 	$.ajaxCall('directory.addLike', 'type_id=directory&item_id='+iBusinessId);
	 }
	 ,click_unlike_page : function(iBusinessId){
		iBusinessId = $('#yndirectory_business_item_id').val();
	 	$.ajaxCall('directory.deleteLike', 'type_id=directory&item_id='+iBusinessId);

	 },
    confirmDeleteBusiness: function(iBusinessId,iDetail){
        $Core.jsConfirm({message: oTranslations['directory.are_you_sure_you_want_to_delete_this_business']}, function () {
            $.ajaxCall('directory.deleteBusiness', 'iBusinessId=' + iBusinessId +'&iDetail=' + iDetail);
        }, function () {

        });
        return false;
    },
	closeBusiness : function(iBusinessId){
		$.ajaxCall('directory.closeBusiness', 'iBusinessId=' + iBusinessId);
	},
	openBusiness : function(iBusinessId){
		$.ajaxCall('directory.openBusiness', 'iBusinessId=' + iBusinessId);
	},
	deleteBusiness: function(iBusinessId,iDetail){
		$.ajaxCall('directory.deleteBusiness', 'iBusinessId=' + iBusinessId +'&iDetail=' + iDetail);
	},
	deleteManyBusiness: function(){
		aSetBusiness = [];
		aSetBusinessText = "";
		$('.moderate_link_active').each(function(index){
			aSetBusiness.push($(this).attr('businessid'));
		});
		aSetBusinessText = aSetBusiness.join(",");
		$.ajaxCall('directory.deleteManyBusiness', "aSetBusiness=" + aSetBusinessText+"");
	},
    confirmDeleteMemberOfBusiness: function (iUserId, iBusinessId) {
        $Core.jsConfirm({message: oTranslations['directory.are_you_sure_you_want_to_delete_this_member_of_business']}, function () {
            yndirectory.deleteMemberOfBusiness(iUserId, iBusinessId);
        }, function () {

        });
        return false;
    },
	deleteMemberOfBusiness: function(iUserId,iBusinessId){
		$.ajaxCall('directory.deleteUserMemberRole', 'height=300&width=530&user_id='+iUserId+'&business_id=' + iBusinessId );
	}
	, initDetail: function(){
		yndirectory.loadAjaxMapDetail($('#yndirectory_detail_business_id').val());
	}
	,loadAjaxMapDetail : function(iBusinessId){
		$Core.ajax('directory.loadAjaxMapDetail',
        {
            type: 'POST',
            params:
            {
                iBusinessId: iBusinessId
            },
            success: function(sOutput)
            {
            	datas = [];
				contents = [];

            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
            		yndirectory.sCorePath = oOutput.sCorePath;
                	var aData = oOutput.data;
            		$.each(aData, function(key, value){
					    item_data = [];
					    item_data['latitude'] = value['location_latitude'];
					    item_data['longitude'] = value['location_longitude'];
					    item_data['location'] = value['location_title'];
						datas.push(item_data);
						contents.push(value['location_address']);
					});
					yndirectory.showMapsWithData('yndirectory_detail_mapview', datas, contents);

                }
            }
        });

	}
	,loadAjaxMapStaticImage : function(iBusinessId){
		$Core.ajax('directory.loadAjaxMapDetail',
        {
            type: 'POST',
            params:
            {
                iBusinessId: iBusinessId
            },
            success: function(sOutput)
            {
            	datas = [];
				contents = [];

            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
            		yndirectory.sCorePath = oOutput.sCorePath;
                	var aData = oOutput.data;
            		$.each(aData, function(key, value){
					    item_data = [];
					    item_data['latitude'] = value['location_latitude'];
					    item_data['longitude'] = value['location_longitude'];
					    item_data['location'] = value['location_title'];
						datas.push(item_data);
						contents.push(value['location_title']);
					});
					yndirectory.showMapsStaticImage('js_block_border_directory_detailcover #yndirectory_cover_maps', datas, contents);

                }
            }
        });

	}
	, clickCompareCheckbox: function(ele){
		var businessid = $(ele).data('compareitembusinessid');
		var name = yndirectory.cookieCompareItemName;
		var data = getCookie(name);
		var aData = [];
		var idx = 0;
		var idx2 = 0;
		if(null === data){
			data = '';
		} else {
			aData = data.split(",");
		}

		var $body = $('body');
		var $ele = $body.find('[data-compareitembusinessid="' + businessid + '"]');
		if(ele.checked){
			// check --> add
			data += ',' + businessid
			data = yndirectoryhelper.trim(data, ',');
			setCookie(name, data, 1);
			// check brother checkbox
			if($ele.length > 0){
				for(idx2 = 0; idx2 < $ele.length; idx2 ++){
					$ele[idx2].checked = true;
				}
			}

			// add into compare dashboard
			yndirectory.addItemIntoCompareDashboard(ele);
		} else {
			// uncheck --> remove
			var isExist = false;
			for(idx = 0; idx < aData.length; idx ++){
				if(businessid == aData[idx]){
					aData.splice(idx, 1);
					break;
				}
			}
			deleteCookie(name);
			setCookie(name, aData.join(), 1);
			// un-check brother checkbox
			if($ele.length > 0){
				for(idx2 = 0; idx2 < $ele.length; idx2 ++){
					$ele[idx2].checked = false;
				}
			}

			// remove out compare dashboard
			yndirectory.removeItemOutCompareDashboard(ele);
		}
	}
	, addItemIntoCompareDashboard: function(checkboxObj){
    	var $checkboxObj = $(checkboxObj);
		if($('#yndirectory_compare_dashboard').length > 0){
			// add item
	        $Core.ajax('directory.compareGetInfoBusiness',
	        {
	            type: 'POST',
	            params:
	            {
	                business_id: $checkboxObj.data('compareitembusinessid')
	            },
	            success: function(sOutput)
	            {
	            	var oOutput = $.parseJSON(sOutput);
	                if(oOutput.status == 'SUCCESS')
	                {
	                	var aCategory = oOutput.aCategory;
	                	var $category_tab = $('#yndirectory_compare_tab_menu_item_' + aCategory.category_id);
	                	if($category_tab.length == 0){
	                		// add new category tab
                			var id_tabcontent = 'yndirectory_compare_tabcontent_' + aCategory.category_id;
	                		var sHtml_tabs_menu = '';
                			sHtml_tabs_menu += '<li id="yndirectory_compare_tab_menu_item_' + aCategory.category_id + '" data-counting="1">';
	                			sHtml_tabs_menu += '<a href="#' + id_tabcontent + '" rel="' + id_tabcontent + '">' + aCategory.title;
	                				sHtml_tabs_menu += ' <span id="yndirectory_compare_tab_menu_counting_' + aCategory.category_id + '">(1)';
	                				sHtml_tabs_menu += '</span>';
	                			sHtml_tabs_menu += '</a>';
                				sHtml_tabs_menu += ' <span onclick="yndirectory.removeItemOutCompareDashboardWithCategoryId(' + aCategory.category_id + ');"><i class="ico ico-close-circle"></i>';
                				sHtml_tabs_menu += '</span>';
                			sHtml_tabs_menu += '</li>';
                			$("#yndirectory_compare_tabs_menu").append(sHtml_tabs_menu);

                			// add new content tab
                			var sHtml_tabs_container = '';
            				sHtml_tabs_container += '<div id="' + id_tabcontent + '">';
            					sHtml_tabs_container += '<ul id="yndirectory_compare_tabs_container_list_' + aCategory.category_id + '">';
            					sHtml_tabs_container += '</ul>';
            				sHtml_tabs_container += '</div>';
                			$("#yndirectory_compare_tabs_container").append(sHtml_tabs_container);

                			// refresh tabs
							$('#yndirectory_compare_tab_menu_item_' + aCategory.category_id).on('click', 'a', function(e){
								// Make the old tab inactive.
								var $active = $('#yndirectory_compare_tabs_menu').find('a.yndirectory-compare-tab-menu-active');
								$active.removeClass('yndirectory-compare-tab-menu-active');
								if($active.length > 0){
									$content = $($active[0].hash);
									$content.hide();
								}

								// Update the variables with the new link and content
								$active = $(this);
								$content = $(this.hash);

								// Make the tab active.
								$active.addClass('yndirectory-compare-tab-menu-active');
								$content.show();

								// Prevent the anchor's default click action
								e.preventDefault();
							});
	                	} else {
		                	// update counting of compare category tab
		                	var counting = $category_tab.data('counting');
		                	counting = parseInt(counting) + 1;
		                	$category_tab.data('counting', counting);
		                	$('#yndirectory_compare_tab_menu_counting_' + aCategory.category_id).html('(' + counting + ')');
	                	}

	                	// add into list
	                	var sHtml_tabs_container = '';
        				sHtml_tabs_container += '<li id="yndirectory_compare_tabs_container_item_' + $checkboxObj.data('compareitembusinessid') + '">';
            				sHtml_tabs_container += '<div class="yndirectory-compare-tabs-container-image">';
                				sHtml_tabs_container += '<a href="' + $checkboxObj.data('compareitemlink') + '">';
	                				sHtml_tabs_container += '<img src="' + $checkboxObj.data('compareitemlogopath') + '" />';
                				sHtml_tabs_container += '</a>';
            				sHtml_tabs_container += '</div>';
            				sHtml_tabs_container += '<span class="yndirectory-compare-tabs-container-item-close" onclick="yndirectory.removeItemOutCompareDashboardWithBusinessId(' + $checkboxObj.data('compareitembusinessid') + ');"><i class="ico ico-close"></i>';
            				sHtml_tabs_container += '</span>';
            				sHtml_tabs_container += '<span class="yndirectory-compare-tabs-container-item-title">';
                				sHtml_tabs_container += '<a href="' + $checkboxObj.data('compareitemlink') + '">' + $checkboxObj.data('compareitemname');
                				sHtml_tabs_container += '</a>';
            				sHtml_tabs_container += '</span>';
        				sHtml_tabs_container += '</li>';
        				$('#yndirectory_compare_tabs_container_list_' + aCategory.category_id).append(sHtml_tabs_container);

        				// active this category tab
        				$( '#yndirectory_compare_tab_menu_item_' + aCategory.category_id + ' a' ).trigger( "click" );

        				$('#yndirectory_compare_dashboard_tabs').css('height', $('#yndirectory_compare_tabs_menu').height()+2 );
	                } else {
	                	yndirectory.alertMessage(oOutput.message);
	                }
	            }
	        });
		} else {
			// init compare dashboard
			yndirectory.initCompareItemBlock();
		}
	}
	, removeItemOutCompareDashboard: function(checkboxObj){
    	var $checkboxObj = $(checkboxObj);
        $Core.ajax('directory.compareGetInfoBusiness',
        {
            type: 'POST',
            params:
            {
                business_id: $checkboxObj.data('compareitembusinessid')
            },
            success: function(sOutput)
            {
            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
                	var aCategory = oOutput.aCategory;
                	var $category_tab = $('#yndirectory_compare_tab_menu_item_' + aCategory.category_id);
                	if($category_tab.length > 0){
	                	// update counting of compare category tab
	                	var counting = $category_tab.data('counting');
	                	counting = parseInt(counting) - 1;
	                	$category_tab.data('counting', counting);
	                	$('#yndirectory_compare_tab_menu_counting_' + aCategory.category_id).html('(' + counting + ')');

	                	// remove out list
        				$('#yndirectory_compare_tabs_container_item_' + $checkboxObj.data('compareitembusinessid')).remove();
        				// remove "li" on compare page
        				$('#yndirectory_compare_page_item_' + $checkboxObj.data('compareitembusinessid')).remove();
        				// update "option" on comare page
    					$option = $('#yndirectory_comparebusiness_detail_option_' + aCategory.category_id);
        				if($option.length > 0){
        					var val = parseInt($option.data('comparedetailtotalitem'), 10) ;
        					var html = $option.html();
        					val = val - 1;
        					html = html.replace(/\(\d+\)/, '(' + val + ')');
        					$option.html(html);
        					$option.data('comparedetailtotalitem', val);
        				}

        				// remove data list if empty
        				if($('#yndirectory_compare_tabs_container_list_' + aCategory.category_id).find('li').length == 0){
        					// remove data list
        					$('#yndirectory_compare_tabcontent_' + aCategory.category_id).remove();
        					// remove category tab
        					$('#yndirectory_compare_tab_menu_item_' + aCategory.category_id).remove();
        					// refresh
        					// $("#yndirectory_compare_tabs").tabs('refresh');
        					// $('#yndirectory_compare_tabs').tabs({ selected: 0 });
	        				if($('#yndirectory_compare_tabs_menu').find('li').length > 0){
		        				$( $('#yndirectory_compare_tabs_menu').find('li')[0]).find('a').trigger( "click" );
        					}
        				} else {
        					$('#yndirectory_compare_tab_menu_item_' + aCategory.category_id + ' a').trigger( "click" );
        				}
        				if($('#yndirectory_compare_tabs_menu').find('li').length == 1){
        					// remove compare box
        					$('#yndirectory_compare_dashboard').remove();
        					$('#yndirectory_compare_dashboard_min').remove();
        				}
                	}
                } else {
                	yndirectory.alertMessage(oOutput.message);
                }
            }
        });
	}
	, removeItemOutCompareDashboardWithBusinessId: function(business_id){
		var $body = $('body');
        var $ele = $body.find('[data-compareitembusinessid="' + business_id + '"]');

		if($ele.length > 0){
			$ele[0].checked = false;
            $($ele[0]).closest('.business-item').removeClass('has-check');
			yndirectory.clickCompareCheckbox($ele[0]);
		}
	}
	, removeItemOutCompareDashboardWithCategoryId: function(category_id){
		$ele = $('#yndirectory_compare_tabcontent_' + category_id);
		if($ele.length > 0){
			$ele.find('span.yndirectory-compare-tabs-container-item-close').trigger('click');
		}
	}
	, removeItemOutCompareDashboardOnComparePage: function(business_id){
		// TO DO: remove on compare page

		// remove from cookie
		yndirectory.removeItemOutCompareDashboardWithBusinessId(business_id);
	}
	, minimizeCompareDashboard: function(is_click){
		var name = 'yndirectory_comparebox_show';
        if(is_click) {
            var comparebox_show = getCookie(name);
            if(comparebox_show == 'minimize') {
                yndirectory.maximizeCompareDashboard(false);
                return false;
            }
        }
		setCookie(name, 'minimize', 1);
		$('#yndirectory_compare_dashboard_content').hide();
		$('#yndirectory_compare_dashboard_min').show();
	}
	, maximizeCompareDashboard: function(is_click){
		var name = 'yndirectory_comparebox_show';
		if(is_click) {
            var comparebox_show = getCookie(name);
            if(comparebox_show == 'maximize') {
            	yndirectory.minimizeCompareDashboard(false);
            	return false;
			}
		}
		setCookie(name, 'maximize', 1);
		$('#yndirectory_compare_dashboard_min').show();
		$('#yndirectory_compare_dashboard_content').show();

	}
	, redirectCompareDetail: function(ele){
		var sCompareLink = $(ele).data('comparelink');
		$ulMenu = $('#yndirectory_compare_tabs_menu');
		var category_id = $ulMenu.find('.yndirectory-compare-tab-menu-active').attr('href');
		category_id = category_id.replace("#yndirectory_compare_tabcontent_", "");
		$ulItemList = $('#yndirectory_compare_tabs_container_list_' + category_id);
		var idList = '';
		var count = 0;
		$ulItemList.find('li').each(function() {
			var id = this.id;
			id = id.replace("yndirectory_compare_tabs_container_item_", "");
			idList += id +',';
			count ++;
		});
		if(count > 1){
			idList = yndirectoryhelper.trim(idList, ',');
			sCompareLink += 'category_' + category_id + '/';
			window.location.href = sCompareLink;
			return true;
		} else {
			yndirectory.alertMessage(oTranslations['directory.please_select_more_than_one_entry_for_the_comparison']);
			return false;
		}
	}
	, initBusinessDetailContactUs: function(){
		// cancel link
		$('#yndirectory_business_detail_module_contactus #yndirectory_contactus_cancel').click(function(event) {
			window.location.href = window.location.href;

			return false;
		});
	}
	, initValidator: function(element){
		jQuery.validator.messages.required  = oTranslations['directory.this_field_is_required'];
		jQuery.validator.messages.url       = oTranslations['directory.please_enter_a_valid_url_for_example_http_example_com'];
		jQuery.validator.messages.accept    = oTranslations['directory.please_enter_a_value_with_a_valid_extension'] ;
		jQuery.validator.messages.minlength = oTranslations['directory.please_enter_at_least_0_characters'] ;
		jQuery.validator.messages.min       = oTranslations['directory.please_enter_a_value_greater_than_or_equal_to_0'] ;
		jQuery.validator.messages.number    = oTranslations['directory.please_enter_a_valid_number'] ;

		jQuery.validator.messages.maxlength = oTranslations['directory.please_enter_no_more_than_0_characters'] ;
		$.data(element[0], 'validator', null);
		element.validate({
			errorPlacement: function (error, element) {
				if(element.is(":radio") || element.is(":checkbox")) {
					// error.appendTo(element.parent());
					error.appendTo($(element).closest('div.form-group'));
				} else {
					error.appendTo(element.parent());
				}
			},
			errorClass: 'yndirectory-error',
			errorElement: 'span',
			debug: false
		});
	}
	, initManagepages: function()
	{

	}
	, initManageRoleSetting: function()
	{
				// change type
		$("#yndirectory_manage_role_id").change(function(){
			var selected = $("#yndirectory_manage_role_settings #yndirectory_manage_role_id").val();
			if (selected.length > 0) {
				$("#yndirectory_manage_role_settings .yndirectory-role-block").hide();
				$("#yndirectory_manage_role_settings #yndirectory_role_block_"+selected).show();
			}
		});

	}
	,initBusinesstype: function()
	{
		// bind click to redirect "add" page
		$('#yndirectory_businesstype .yndirectory-createabusiness').click(function()
		{
			var selected = $("#yndirectory_businesstype #yndirectory_type input[type='radio']:checked");
			if (selected.length > 0) {
				var selectedVal = selected.val();
				if ($(this).data('module') !== '' && $(this).data('item') !== '') {
					window.location.href = $(this).data('url') + 'type_' + selectedVal + '/package_' + $(this).data('packageid') + '/module_' + $(this).data('module') + '/item_' + $(this).data('item');

				}
				else{
					window.location.href = $(this).data('url') + 'type_' + selectedVal + '/package_' + $(this).data('packageid') + '/';
				}
			}
		});

		// change type
		$("#yndirectory_businesstype input[name=type]:radio").change(function(){
			var selected = $("#yndirectory_businesstype #yndirectory_type input[type='radio']:checked");
			if (selected.length > 0) {
			    var selectedVal = selected.val();
			    switch(selectedVal){
			    	case 'business':
			    		$('#yndirectory_businesstype #yndirectory_okforclaiming').hide();
			    		$('#yndirectory_businesstype #yndirectory_package').show();
			    		break;
			    	case 'claiming':
			    		$('#yndirectory_businesstype #yndirectory_okforclaiming').show();
			    		$('#yndirectory_businesstype #yndirectory_package').hide();
			    		break;
			    }
			}
		});

		// when click ok with claiming type
		$('#yndirectory_businesstype #yndirectory_okforclaiming').click(function(){
			var selected = $("#yndirectory_businesstype #yndirectory_type input[type='radio']:checked");
			if (selected.length > 0) {
			    var selectedVal = selected.val();
				window.location.href = $(this).data('url') + 'type_' + selectedVal + '/';
			}
		});
	}
	, initEdit: function()
	{
		// back button
		$('#yndirectory_edit #yndirectory_back').click(function(){
			window.location.href = $(this).data('url');
		});


		// auto select theme
		var selected = $("#yndirectory_edit #yndirectory_theme input[type='radio']:checked");
		if (selected.length == 0) {
			selected = $("#yndirectory_edit #yndirectory_theme input[type='radio']");
			if (selected.length > 0) {
				jQuery(selected[0]).attr('checked', 'checked');
			}
		}

		// search location by google api
	 	var input = ($("#yndirectory_edit #yndirectory_locationlist #yndirectory_location_1")[0]);
	 	if (window.google){
	 		// do nothing
	 	} else {
			return false;
		}
	 	var autocomplete = new google.maps.places.Autocomplete(input);
	  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }

		    var $parent = $(input).closest('.yndirectory-location');
		    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
		    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
		    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
		    $parent.find('.yndirectory-error').last().remove();
		    $parent.find('.yndirectory-error').removeClass('yndirectory-error');
	    });

	  	// when change main category
		$('.js_mp_category_list').change(function()
		{
			var $this = $(this);
			var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
			// var iCatId = document.getElementById('js_mp_id_0').value;
			iCatId = $this.val();
			if(!iCatId) {
				iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
			}

			// $.ajaxCall('advancedmarketplace.frontend_loadCustomFields', 'catid='+iCatId+'&lid='+$("#ilistingid").val());
			$parent = $this.closest('.category-wrapper');
			$parent.find('.js_mp_category_list').each(function()
			{
				if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
				{
					$parent.find('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

					this.value = '';
				}
			});

			$parent.find('#js_mp_holder_' + $(this).val()).show();
			$parentToCheckChangeCustomField = $this.closest('.js_mp_parent_holder');
			if($parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory').length > 0){
				var radiobutton = $parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory')[0];
				if(radiobutton.checked == true){
					var selected = $parentToCheckChangeCustomField.find('select option:selected').val();
					if(selected.length > 0){
						// change custom field by main category
						yndirectory.changeCustomFieldByMainCategory(selected);
					} else {
                        $('#yndirectory_customfield_category').html('');
					}
				}
			}
		});

		// when change main category
		$('#yndirectory_categorylist .yndirectory-categorylist-maincategory').change(function(){
			$parent = $(this).closest('.js_mp_parent_holder');
			if($parent.length > 0){
				var selected = $parent.find('select option:selected').val();
				if(selected.length > 0){
					// change custom field by main category
					yndirectory.changeCustomFieldByMainCategory(selected);
				}
			}
		});

		// update number of feature fee following numbers of days
		$('#yndirectory_feature_number_days').on('keyup', yndirectory.onChangeFeatureFeeTotal);

		// validate form
		yndirectory.initValidator($('#yndirectory_edit_directory_form'));
		jQuery.validator.addMethod('checkLocation', function() {
			var result = false;
			$('#yndirectory_edit_directory_form #yndirectory_locationlist').find('[data-inputid="address"]').each(function(){
				if(this.value.length > 0){
					result = true;
				}
			});

			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkPhone', function() {
			var result = false;
			$('#yndirectory_edit_directory_form #yndirectory_phonelist').find('input').each(function(){
				if(this.value.length > 0){
					result = true;
				}
			});

			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCategory', function() {
			var result = false;
			if($('#yndirectory_edit_directory_form #yndirectory_categorylist .category-wrapper:first #js_mp_id_0').val() != ''){
				result = true;
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldText', function(value, element, params) {
			var result = false;
			if(element.value.length > 0){
				result = true;
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldTextarea', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldSelect', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldMultiselect', function(value, element, params) {
			var result = false;
			var select = $(element).val();
			if(undefined != select && null != select && select.length > 0){
				result = true;
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldCheckbox', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:checkbox').is(':checked')){
				result = true;
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldRadio', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:radio').is(':checked')){
				result = true;
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		$('#yndirectory_edit_directory_form #name').rules('add', {
			required: true
		});
		$('#yndirectory_edit_directory_form #short_description').rules('add', {
			required: true
		});

		$('#yndirectory_edit_directory_form #yndirectory_phonelist .phone-wrapper:first input[name="val[phone][]"]').rules('add', {
			checkPhone: true
		});
		$('#yndirectory_edit_directory_form #yndirectory_email').rules('add', {
			required: true
		});
		$('#yndirectory_edit_directory_form #yndirectory_categorylist .category-wrapper:first #js_mp_id_0').rules('add', {
			checkCategory: true
		});

		// preview button
		$('#yndirectory_edit #yndirectory_preview').click(function(){
			yndirectory.showPreivewNewBusiness();
			return false;
		});

	}
	, initAdd: function()
	{
		if($('#yndirectory_edit_directory_form').length == 0){
			return false;
		}

		// back button
		$('#yndirectory_add #yndirectory_back').click(function(){
			window.location.href = $(this).data('url');
		});

		// auto select theme
		var selected = $("#yndirectory_add #yndirectory_theme input[type='radio']:checked");
		if (selected.length == 0) {
			selected = $("#yndirectory_add #yndirectory_theme input[type='radio']");
			if (selected.length > 0) {
				jQuery(selected[0]).attr('checked', 'checked');
			}
		}

		// search location by google api
		if($("#yndirectory_add #yndirectory_locationlist #yndirectory_location_99999").length > 0){
		 	var input = ($("#yndirectory_add #yndirectory_locationlist #yndirectory_location_99999")[0]);
		 	// var input = (document.getElementById('yndirectory_location_99999'));
		 	if (window.google){
		 		// do nothing
			 	var autocomplete = new google.maps.places.Autocomplete(input);
			  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
			    	var place = autocomplete.getPlace();
				    if (!place.geometry) {
				     	return;
				    }

				    var $parent = $(input).closest('.yndirectory-location');
				    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
				    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
				    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
				    $parent.find('.yndirectory-error').last().remove();
				    $parent.find('.yndirectory-error').removeClass('yndirectory-error');
			    });
			}
		} else {
			$("#yndirectory_add #yndirectory_locationlist").find('[data-inputid="fulladdress"]').each(function(){

				var input = this;
			 	if (window.google){
			 		// do nothing
				 	var autocomplete = new google.maps.places.Autocomplete(input);
				  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
				    	var place = autocomplete.getPlace();
					    if (!place.geometry) {
					     	return;
					    }

					    var $parent = $(input).closest('.yndirectory-location');
					    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
					    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
					    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
					    $parent.find('.yndirectory-error').last().remove();
					    $parent.find('.yndirectory-error').removeClass('yndirectory-error');
				    });
				}
			});
		}

	  	// when change main category
		$('.js_mp_category_list').change(function()
		{
			var $this = $(this);
			var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
			// var iCatId = document.getElementById('js_mp_id_0').value;
			iCatId = $this.val();
			if(!iCatId) {
				iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
			}

			// $.ajaxCall('advancedmarketplace.frontend_loadCustomFields', 'catid='+iCatId+'&lid='+$("#ilistingid").val());
			$parent = $this.closest('.category-wrapper');
			$parent.find('.js_mp_category_list').each(function()
			{
				if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
				{
					$parent.find('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

					this.value = '';
				}
			});

			$parent.find('#js_mp_holder_' + $(this).val()).show();
			$parentToCheckChangeCustomField = $this.closest('.js_mp_parent_holder');
			if($parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory').length > 0){
				var radiobutton = $parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory')[0];
				if(radiobutton.checked == true){
					var selected = $parentToCheckChangeCustomField.find('select option:selected').val();
					if(selected.length > 0){
						// change custom field by main category
						yndirectory.changeCustomFieldByMainCategory(selected);
					}
				}
			}
		});

		// when change main category
		$('#yndirectory_categorylist .yndirectory-categorylist-maincategory').change(function(){
			$parent = $(this).closest('.js_mp_parent_holder');
			if($parent.length > 0){
				var selected = $parent.find('select option:selected').val();
				if(selected.length > 0){
					// change custom field by main category
					yndirectory.changeCustomFieldByMainCategory(selected);
				}
			}
		});
		// check for first item
		if($('#yndirectory_categorylist .yndirectory-categorylist-maincategory').length > 0){
			$('#yndirectory_categorylist .yndirectory-categorylist-maincategory')[0].checked = true;
		}

		// update number of feature fee following numbers of days
		$('#yndirectory_feature_number_days').on('keyup', yndirectory.onChangeFeatureFeeTotal);

		// validate form
		yndirectory.initValidator($('#yndirectory_edit_directory_form'));
		jQuery.validator.addMethod('checkLocation', function() {
			var result = false;
			$('#yndirectory_edit_directory_form #yndirectory_locationlist').find('[data-inputid="address"]').each(function(){
				if(this.value.length > 0){
					result = true;
				}
				else{
					$('#yndirectory_submit_buttons').show();
				}
			});

			return result;
		}, oTranslations['directory.address_is_required']);
		jQuery.validator.addMethod('checkPhone', function() {
			var result = false;
			$('#yndirectory_edit_directory_form #yndirectory_phonelist').find('input').each(function(){
				if(this.value.length > 0){
					result = true;
				}
				else{
					$('#yndirectory_submit_buttons').show();
				}
			});

			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCategory', function() {
			var result = false;
			if($('#yndirectory_edit_directory_form #yndirectory_categorylist .category-wrapper:first #js_mp_id_0').val() != ''){
				result = true;
			}
			else{
				$('#yndirectory_submit_buttons').show();
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldText', function(value, element, params) {
			var result = false;
			if(element.value.length > 0){
				result = true;
			}
			else{
				$('#yndirectory_submit_buttons').show();
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldTextarea', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			else{
				$('#yndirectory_submit_buttons').show();
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldSelect', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			else{
				$('#yndirectory_submit_buttons').show();
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldMultiselect', function(value, element, params) {
			var result = false;
			var select = $(element).val();
			if(undefined != select && null != select && select.length > 0){
				result = true;
			}
			else{
				$('#yndirectory_submit_buttons').show();
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldCheckbox', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:checkbox').is(':checked')){
				result = true;
			}
			else{
				$('#yndirectory_submit_buttons').show();
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldRadio', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:radio').is(':checked')){
				result = true;
			}
			else{
				$('#yndirectory_submit_buttons').show();
			}
			return result;
		}, oTranslations['directory.this_field_is_required']);
		$('#yndirectory_edit_directory_form #name').rules('add', {
			required: true
		});
		$('#yndirectory_edit_directory_form #short_description').rules('add', {
			required: true
		});

		$('#yndirectory_edit_directory_form #yndirectory_phonelist .phone-wrapper:first input[name="val[phone][]"]').rules('add', {
			checkPhone: true
		});
		$('#yndirectory_edit_directory_form #yndirectory_email').rules('add', {
			required: true
		});
		$('#yndirectory_edit_directory_form #yndirectory_categorylist .category-wrapper:first #js_mp_id_0').rules('add', {
			checkCategory: true
		});

		// preview button
		$('#yndirectory_add #yndirectory_preview').click(function(){
			yndirectory.showPreivewNewBusiness();
			return false;
		});

	}
	, addAjaxForCreateNewItemInDashboard: function(business_id, type) {
		$.ajaxCall('directory.setBusinessSessionInDashboard', 'business_id=' + business_id + '&type=' + type, 'GET');
		return false;
	}
	, addAjaxForCreateNewItem: function(business_id, type) {
		$('#yndirectory_add_new_item').click(function() {
			$.ajaxCall('directory.setBusinessSession', 'business_id=' + business_id + '&type=' + type, 'GET');
			return false;
		});
	}
	/*================== INDEX PAGE - start =====================*/
	, initIndex: function(){
		yndirectory.changeViewHomePage();
		yndirectory.addCategoryJsEventListener();
		$('.js_pager_view_more_link').show();
	}
	, searchBlockData: {isClick: false}
	, initSearchBlock: function(){
		// prevent enter for submitting form
		$("#yndirectory_advsearch #keyword").keyup(function(event){
		    if(event.keyCode == 13){
		        $("#yndirectory_searchblock_submit").click();
		    }
		});
		$("#yndirectory_searchblock_submit").click(function(e) {
				yndirectory.searchBlockData.isClick = true;
				$("#yndirectory_advsearch").submit();
		});
		$("#yndirectory_advsearch").submit(function(e) {

			if(yndirectory.searchBlockData.isClick){
				return true;
			}else{
				yndirectory.searchBlockData.isClick = false;
			}
				return false;
		});
		// search block - location - auto suggest
		// search location by google api
		 	var input = ($("#yndirectory_advsearch #yndirectory_searchblock_location")[0]);
		 	if (window.google){
		 		// do nothing
		 	} else {
				return false;
			}
			if(!/undefined/i.test(typeof google.maps.places)){
			 	var autocomplete = new google.maps.places.Autocomplete(input);
			  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
			    	var place = autocomplete.getPlace();
				    if (!place.geometry) {
				     	return;
				    }

				    var $parent = $(input).closest('.yndirectory-form-location');
				    $parent.find('[data-inputid="location_address"]').val($parent.find('#yndirectory_searchblock_location').val());

				    $parent.find('[data-inputid="location_address_lat"]').val(place.geometry.location.lat());
				    $parent.find('[data-inputid="location_address_lng"]').val(place.geometry.location.lng());

			    });
			}
	}
	, subscribeBlockData: {isClick: false}
	, initSubscribeBlock: function(){
		// prevent enter for submitting form
		$("#yndirectory_subscribe").submit(function(e) {

				return false;
		});
		// subscribe block - location - auto suggest
		// search location by google api

	 	var input = ($("#yndirectory_subscribe #yndirectory_subscribeblock_location")[0]);
	 	if (window.google){
	 		// do nothing
	 	} else {
			return false;
		}
		if(!/undefined/i.test(typeof google.maps.places)){
		 	var autocomplete = new google.maps.places.Autocomplete(input);
		  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		    	var place = autocomplete.getPlace();
			    if (!place.geometry) {
			     	return;
			    }

			    var $parent = $(input).closest('.yndirectory-location');
			    $parent.find('[data-inputid="subscribe_location_address"]').val($parent.find('#yndirectory_subscribeblock_location').val());

			    $parent.find('[data-inputid="subscribe_location_address_lat"]').val(place.geometry.location.lat());
			    $parent.find('[data-inputid="subscribe_location_address_lng"]').val(place.geometry.location.lng());

		    });
		}

        $('.category_checkbox').on('change', function() {
            var selected_categories = $('.category_checkbox:checked');
            if(selected_categories.length == 1) {
                $('.subscribe-categories__text').text(selected_categories.parent().find('span').text());
            } else if(selected_categories.length > 1) {
                $('.subscribe-categories__text').text(selected_categories.length + ' ' +oTranslations['directory.categories_selected']);
            }
            if ($('.category_checkbox:checked').length) {
                $('.subscribe-categories span' ).addClass('active');
            } else {
                $('.subscribe-categories span' ).removeClass('active');
                $('.subscribe-categories__text').text(oTranslations['directory.select_category']);
            }
        });
	}
	, changeViewHomePage: function(){
		if ($('#completecreatetour').length != 0) {
			$('#yndirectory_listview_menu').addClass('view-menu-active');
			$('#yndirectory_listview').css('display', 'block');
			return false;
		}

		if($('#yndirectory_menu_viewtype').length > 0){
			if($('#yndirectory_has_init_modeview').val() == 0)
			{
				$('#yndirectory_has_init_modeview').val('1');
			}
			else
			{
				return false;
			}
			var iTimeout = 100;
			switch($('#yndirectory_menu_viewtype').val()){
				case 'listview':
					setTimeout(function(){ $( '#yndirectory_listview_menu' ).trigger( "click" ); $('#yndirectory_menu_viewtype_addcookie_triggerclick').val(1); }, iTimeout);
					break;
				case 'gridview':
					setTimeout(function(){ $( '#yndirectory_gridview_menu' ).trigger( "click" ); $('#yndirectory_menu_viewtype_addcookie_triggerclick').val(1); }, iTimeout);
					break;
				case 'pinboardview':
					setTimeout(function(){ $( '#yndirectory_pinboardview_menu' ).trigger( "click" ); $('#yndirectory_menu_viewtype_addcookie_triggerclick').val(1); }, iTimeout);
					break;
				case 'mapview':
					setTimeout(function(){ $( '#yndirectory_mapview_menu' ).trigger( "click" ); $('#yndirectory_menu_viewtype_addcookie_triggerclick').val(1); }, iTimeout);
					break;
			}
		}

	}
	, sCorePath : ''
	, showMapView: function(){

		sCondition = $('#yndirectory_index #yndirectory_condition').val();
		var yndirectory_menu_current_index_page = $('#yndirectory_menu_current_index_page').val();
		var yndirectory_menu_display_page = $('#yndirectory_menu_display_page').val();

        $Core.ajax('directory.loadAjaxMapView',
        {
            type: 'POST',
            params:
            {
                sCondition: sCondition
                , yndirectory_menu_current_index_page: yndirectory_menu_current_index_page
                , yndirectory_menu_display_page: yndirectory_menu_display_page
            },
            success: function(sOutput)
            {
            	datas = [];
				contents = [];

            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
            		yndirectory.sCorePath = oOutput.sCorePath;
                	var aData = oOutput.data;
            		$.each(aData, function(key, value){
					    item_data = [];
					    item_data['latitude'] = value[0]['latitude'];
					    item_data['longitude'] = value[0]['longitude'];
					    item_data['location'] = value[0]['location'];
					    item_data['location_address'] = value[0]['location_address'];
						datas.push(item_data);
						contents.push(value);
					});
					yndirectory.showMapsWithData('yndirectory_mapview', datas, contents);

                }
            }
        });

	}
	,addCategoryJsEventListener: function() {

			$('#yndirectoryp_advsearch #yndirectory_add').remove();
			$('#yndirectoryp_advsearch #yndirectory_delete').remove();
			$('#yndirectoryp_advsearch #yndirectory_maincategory').remove();

			$('.js_mp_category_list').change(function()
			{
				var iParentId = parseInt(this.id.replace('js_mp_id_', ''));

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


			$('.hover_action').each(function()
			{
				$(this).parents('.js_outer_video_div:first').css('width', this.width + 'px');
			});
		}
	/*================== INDEX PAGE - end =====================*/

	, showPreivewNewBusiness: function(){
        $Core.ajax('directory.showPreivewNewBusiness',
        {
            type: 'POST',
            params:
            {
                action: 'showPreivewNewBusiness'
                , sText: (Editor.getContent())
            },
            success: function(sOutput)
            {
                var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
                	var sDescription = oOutput.sText;
					$form = $('#yndirectory_edit_directory_form');
					var sHtml = '';
					sHtml += '<div class="yndirectory-popup-content">';
						sHtml += '<h1 class="preview-box-header">';
							sHtml += $form.find('#name').val();
						sHtml += '</h1>';
						sHtml += '<div class="yndirectory-detail-overview">';

							// short description
							if($form.find('#short_description').val().length > 0){
								sHtml += '<div class="yndirectory-detail-overview-item wrap-longtext">';
									sHtml += $form.find('#short_description').val();
								sHtml += '</div>';
							}

							// business sizes
							sHtml += '<div class="yndirectory-detail-overview-item">';
								sHtml += '<h5><span><i class="ico ico-user2-two-o"></i>' + oTranslations['directory.business_sizes'] + '<span>' + $form.find('#yndirectory_businesssize option:selected').text() + '</span></span></h5>';
							sHtml += '</div>';

							// operating_hours
							var sTimezone = '';
							var sOperatingHours = '';
							sTimezone += $form.find('#time_zone option:selected').text();
							$form.find('#yndirectory_visitinghourlist .visiting_hours-wrapper').each(function(){
								sOperatingHours += '<div class="yndirectory-detail-hour-item"><div class="item-day">' + $(this).find('select[name="val[visiting_hours_dayofweek_id][]"] option:selected').text() + '</div>';
								sOperatingHours += '<div class="item-hour">' + $(this).find('select[name="val[visiting_hours_hour_starttime][]"] option:selected').text() + ' - ' + $(this).find('select[name="val[visiting_hours_hour_endtime][]"] option:selected').text() + '</div>';
								sOperatingHours += '</div>';
							});
							sHtml += '<div class="yndirectory-detail-overview-item">';
								sHtml += '<h5 class="yndirectory-line"><span><i class="ico ico-clock-o"></i>' + oTranslations['directory.operating_hours'] + ' </span></h5>';
								sHtml += '<div class="yndirectory-overview-item-content"><div class="yndirectory-detail-timezone"><span class="time-title">' + oTranslations['directory.timezone'] + ' :</span> ' + sTimezone + ' </div>';
								sHtml += '<div class="yndirectory-detail-hour-list">';
								sHtml += sOperatingHours;
							sHtml += '</div></div></div>';
							// founders
							if($form.find('#yndirectory_founder').val().length > 0){
								sHtml += '<div class="yndirectory-detail-overview-item">';
									sHtml += '<h5 class="yndirectory-line"><span><i class="ico ico-user2-edit-o"></i>' + oTranslations['directory.founders'] + ' </span></h5>';
									sHtml += '<div class="yndirectory-overview-item-content">' + $form.find('#yndirectory_founder').val() + '</div>';
								sHtml += '</div>';
							}
							// contact_information
							var sPhone = '';
							var sWebsite = '';
							var sEmail = '';
							var sFax = '';
							$form.find('#yndirectory_phonelist .phone-wrapper').each(function(){
								var $ele = $(this).find('input[name="val[phone][]"]');
								if($ele.val().length > 0){
									sPhone += '<div>' + $ele.val() + '</div>';
								}
							});
							$form.find('#yndirectory_websitelist .web_address-wrapper').each(function(){
								var $ele = $(this).find('input[name="val[web_address][]"]');
								if($ele.val().length > 0){
									sWebsite += '<div>' + $ele.val() + '</div>';
								}
							});
							$form.find('#yndirectory_faxlist .fax-wrapper').each(function(){
								var $ele = $(this).find('input[name="val[fax][]"]');
								if($ele.val().length > 0){
									sFax += '<div>' + $ele.val() + '</div>';
								}
							});
							if($form.find('#yndirectory_email').val().length > 0){
								sEmail += '<div>' + $form.find('#yndirectory_email').val() + '</div>';
							}
							if(sPhone.length > 0 || sWebsite.length > 0 || sFax.length > 0 || sEmail.length > 0){
								sHtml += '<div class="yndirectory-detail-overview-item">';
									sHtml += '<h5 class="yndirectory-line"><span><i class="ico ico-envelope-o"></i>' + oTranslations['directory.contact_information'] + ' </span></h5>';
									sHtml += '<div class="yndirectory-overview-item-content">';
										if(sPhone.length > 0){
											sHtml += '<div class="yndirectory-detail-overview-contact-item"><div class="item-title">' + oTranslations['directory.phone'] + '</div><div class="item-info">' + sPhone + '</div></div>';
										}
										if(sFax.length > 0){
											sHtml += '<div class="yndirectory-detail-overview-contact-item"><div class="item-title">' + oTranslations['directory.fax'] + '</div><div class="item-info">' + sFax + '</div></div>';
										}
										if(sEmail.length > 0){
											sHtml += '<div class="yndirectory-detail-overview-contact-item"><div class="item-title">' + oTranslations['directory.email'] + '</div><div class="item-info">' + sEmail + '</div></div>';
										}
										if(sWebsite.length > 0){
											sHtml += '<div class="yndirectory-detail-overview-contact-item"><div class="item-title">' + oTranslations['directory.website'] + '</div><div class="item-info">' + sWebsite + '</div></div>';
										}
									sHtml += '</div>';
								sHtml += '</div>';
							}
							// locations
							var locationLatLong = [];
							var locationContent = [];
							$form.find('#yndirectory_locationlist .yndirectory-location').each(function(){
								var lat = $(this).find('input[name="val[location_address_lat][]"]').val();
								var lng = $(this).find('input[name="val[location_address_lng][]"]').val();
								if(lat > 0 && lng > 0){
									locationLatLong.push({latitude: lat, longitude: lng});
									locationContent.push('');
								}
							});
							if(locationLatLong.length > 0){
								sHtml += '<div class="yndirectory-detail-overview-item">';
									sHtml += '<h5 class="yndirectory-line"><span><i class="ico ico-map-o"></i>' + oTranslations['directory.locations'] + ' </span></h5>';
									sHtml += '<div class="yndirectory-overview-item-content">' + '<div id="yndirectory_location_map_canvas" style="height: 200px;"></div>' + '</div>';
								sHtml += '</div>';
							}
							//addition info
							if($form.find('#yndirectory_customfield_title').val().length > 0 || $form.find('#yndirectory_customfield_content').val().length > 0){
								sHtml += '<div class="yndirectory-detail-overview-item">';
								sHtml += '<h5 class="yndirectory-line"><span><i class="ico ico-file-text-o"></i>' + oTranslations['directory.additional_infomation'] + ' </span></h5>';
								sHtml += '<div class="yndirectory-overview-item-content"><div class="yndirectory-detail-overview-additional-item"><div>' + $form.find('#yndirectory_customfield_title').val() + ':</div><div>'+ $form.find('#yndirectory_customfield_content').val() + '</div></div></div>';
								sHtml += '</div>';
							}
							// description
							if(sDescription.length > 0){
								sHtml += '<div class="yndirectory-detail-overview-item">';
									sHtml += '<h5 class="yndirectory-line"><span><i class="fa fa-align-justify"></i>' + oTranslations['directory.description'] + ' </span></h5>';
									sHtml += '<div class="yndirectory-overview-item-content"><div class="yndirectory-description item_view_content">' + sDescription + '</div></div>';
								sHtml += '</div>';
							}

							// category
							var sCategories = '';
							$form.find('#yndirectory_categorylist .category-wrapper').each(function(){
								var $eleParent = $(this).find('#js_mp_holder_0 option:selected');
								if($eleParent.val() != ''){
									sCategories += $eleParent.text();
								}

								if($(this).find('#js_mp_id_3').is(":visible")){
									var $eleChild = $(this).find('#js_mp_holder_3 option:selected');
									if($eleChild.val() != ''){
										sCategories += ' » ' + $eleChild.text();
									}
								}
								sCategories += ' | ';
							});

							sCategories = yndirectoryhelper.trim(sCategories, ' | ');
							if(sCategories.length > 0){
								sHtml += '<div class="ync-item-info-group"><div class="ync-item-info"><div class="ync-category">';
								sHtml += '<span class="ync-item-label">' + oTranslations['directory.category'] + ':<span class="ync-item-content"> ' + sCategories + '</span></span>';
								sHtml += '</div></div></div>';
							}

						sHtml += '</div>';
					sHtml += '</div>';

					// Open directly via API
					$.magnificPopup.open({
					  items: {
					    // src: '<div class="white-popup-block-previewnewbusiness" style="width: 980px;">' + sHtml + '</div>', // can be a HTML string, jQuery object, or CSS selector
					    src: '<div class="white-popup-block-previewnewbusiness" >' + sHtml + '</div>', // can be a HTML string, jQuery object, or CSS selector
					    type: 'inline'
					  }
					});

					// render maps
					if(locationLatLong.length > 0){
						yndirectory.showMapsWithData('yndirectory_location_map_canvas', locationLatLong, locationContent);
					}
                } else
                {
                }
            }
        });
	}
	, showMapsWithData: function(id, datas, contents){
		if($('#' + id).length > 0 && datas.length > 0){
			var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
			var neighborhoods = [];
			var markers = [];
			var iterator = 0;
		    for(i=0 ; i< datas.length ; i++)
		    {
		    	neighborhoods.push(new google.maps.LatLng(datas[i]['latitude'], datas[i]['longitude']));
		    }

			function showMapsWithData_initialize() {
				var mapOptions = {
			    	zoom: 15,
			    	center: center
		  	  	};

			  	map = new google.maps.Map(document.getElementById(id),mapOptions);
		  		var bounds = new google.maps.LatLngBounds();


		      	for (var i = 0; i < neighborhoods.length; i++) {
		      		showMapsWithData_addMarker(i);
		      		if(neighborhoods.length > 1){

		  				bounds.extend(neighborhoods[i]);
		  			}

		  		}

		  		if(neighborhoods.length > 1){
		  			map.fitBounds(bounds);
				}
			}
			function showMapsWithData_addMarker(i) {
		  		marker = new google.maps.Marker({
			    	position: neighborhoods[iterator],
			    	map: map,
			    	draggable: false,
			    	animation: google.maps.Animation.DROP,
			    	icon: datas[i]['icon']
		  		})
		  		markers.push(marker);
		  		iterator++;
		  		infowindow = new google.maps.InfoWindow({});
		  		google.maps.event.addListener(marker, 'mouseover', function() {
		    		infowindow.close();
		    		infowindow.setContent(yndirectory.showExtraInfo(contents[i]));
		    		infowindow.open(map,markers[i]);
		  		});
			}

			showMapsWithData_initialize();
		}
	}
	, showMapsStaticImage: function(id, datas, contents){
		if($('#' + id).length > 0 && datas.length > 0){
			var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
			var neighborhoods = [];
			var markers = [];
			var iterator = 0;
			for (i = 0; i < datas.length; i++) {
				if(datas[i]['latitude'] != '' && datas[i]['longitude'] != '') {
					neighborhoods.push(new google.maps.LatLng(datas[i]['latitude'], datas[i]['longitude']));
				}
			}
			function showMapsWithData_initialize() {
				var mapOptions = {
			    	zoom: 10,
			    	center: center
		  	  	};


		  		var bounds = new google.maps.LatLngBounds();

		  		marker_static = "";
		      	for (var i = 0; i < neighborhoods.length; i++) {
		  			bounds.extend(neighborhoods[i]);
		  			marker_static += "&markers=color:red%7Clabel:S%7C"+datas[i]['latitude']+","+datas[i]['longitude']+"";
		  		}
		  		var apiKey = $('#yndirectory-api-key').val();
				static_image = 'https://maps.googleapis.com/maps/api/staticmap?zoom=12&scale=2&size=400x200&maptype=roadmap' + marker_static +'&key=' + apiKey;
				if($("#yndirectory_is_print_page").val() == 0) {
					$('#' + id).css("background-image", "url('" + static_image + "')");
					$('#' + id).css("background-repeat", "no-repeat");
				}
				else{

					$('#'+id).append("<img src='"+ static_image+"'/>");
				}
			}

			showMapsWithData_initialize();
		}
	}
	,showExtraInfo : function(info){


		sHtml = '';

		if($.isArray(info)){

			if(info.length > 1){
				sHtml += '<div class="business-item-map-header" style="background-color: #f4f4f4; color: #5f74a6">';
					sHtml += '<span style="background-color: #40474e; color: #fff; padding: 0 5px; display: inline-block; margin-right: 5px;">' + info.length + '</span>' + oTranslations['directory.businesses'];
				sHtml += '</div>';
				sHtml += '<div class="business-item-map-main">';
			}

			$.each(info, function(key, aBus){
				sHtml += '<div class="business-item-map" style="width: 300px; height: 64px; padding: 8px 5px; border-bottom: 1px solid #ebebeb; font-size: 12px; overflow: hidden; box-sizing: content-box; -webkit-box-sizing: content-box; -moz-box-sizing: content-box;">';
					sHtml += '<div class="business-item-map-image" style="position: relative; margin-right: 10px; float: left;">';
						sHtml += '<a href="'+aBus['url_detail']+'"><img width=64 height=64 src="'+aBus['url_image']+'"></a>';
						if(aBus['featured']){
							sHtml += '<div class="business-item-map-featured" style="background-color: #39b2ea; color: #fff; text-transform: uppercase; position: absolute; top: 0; left: -4px; display: block; height: 18px; line-height: 18px; padding: 0 6px 0 10px; font-weight: bold; font-size: 10px; -webkit-box-shadow: inset 4px 0 0 rgba(0, 0, 0, 0.3), 2px 2px 0 rgba(0, 0, 0, 0.2); -moz-box-shadow: inset 4px 0 0 rgba(0, 0, 0, 0.3), 2px 2px 0 rgba(0, 0, 0, 0.2); box-shadow: inset 4px 0 0 rgba(0, 0, 0, 0.3), 2px 2px 0 rgba(0, 0, 0, 0.2);">' + oTranslations['directory.featured'] + '</div>';
						}
					sHtml += '</div>';
					sHtml += '<div class="business-item-map-title" style="color: #3b5998; font-size: 14px; font-weight: bold; margin-bottom: 10px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><a href="'+aBus['url_detail']+'">' + aBus['title'] + '</a></div>';
					sHtml += '<div class="business-item-map-location" style="margin-bottom: 5px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa fa-map-marker"></i> ' + aBus['location'] + '</div>';
					sHtml += '<div class="business-item-map-info" style="display: inline-block;">';
						sHtml += '<span>';
						for (i = 0; i < 5; i++) {
							if (i < aBus['rating']) {
		    					sHtml += '<img src="' + yndirectory.sCorePath + 'module/directory/static/image/star-on.png" />';
							} else {
								sHtml += '<img src="' + yndirectory.sCorePath + 'module/directory/static/image/star-off.png" />';
							}
						}
						sHtml += '</span>';
						sHtml += '<span style="vertical-align: top; padding-left: 5px;">(' + aBus['reviews'] + ')</span>';
					sHtml += '</div">';

					sHtml += '<div style="display: inline-block; padding-left: 10px;"><a href="https://maps.google.com/maps?daddr='+aBus['location_address']+'" target="_blank"><img src="' + oParams.sJsHome + 'module/directory/static/image/icon-getdirection.png" /> '+oTranslations['directory.get_directions']+'</a></div></div>';

				sHtml += '</div>';
			});

			if(info.length > 1){
				sHtml += '</div>';
			}

		}
		else {
			sHtml = info;
			sHtml += '<div style="display: inline-block; padding-left: 10px;"><a href="https://maps.google.com/maps?daddr='+info+'" target="_blank"><img src="' + oParams.sJsHome + 'module/directory/static/image/icon-getdirection.png" /> '+oTranslations['directory.get_directions']+'</a></div></div>';
		}

		return sHtml;
	}
	, onChangeFeatureFeeTotal: function(){
		if('' == $('#yndirectory_feature_number_days').val() || (isNaN(parseInt($('#yndirectory_feature_number_days').val())))){
			$('#yndirectory_feature_number_days').val('');
			$('#yndirectory_feature_fee_total').val('');
		} else {
			$('#yndirectory_feature_number_days').val(parseInt($('#yndirectory_feature_number_days').val()));
			$('#yndirectory_feature_fee_total').val(parseInt($('#yndirectory_feature_number_days').val()) * parseInt($('#yndirectory_defaultfeaturefee').val()));
		}
	}
	, viewMap: function(ele){
		if($('#yndirectory_pagename').length > 0 ){
			var yndirectory_pagename = $('#yndirectory_pagename').val();
			switch(yndirectory_pagename){
				case 'add':
					var item = $(ele).closest('.yndirectory-location').data('item');
					var obj = $('#yndirectory_add #yndirectory_locationlist').find('[data-item="' + item + '"]');
			     	var latitude = obj.find('[data-inputid="lat"]').val();
			     	var longitude = obj.find('[data-inputid="lng"]').val();
			     	var address = obj.find('[data-inputid="address"]').val();
			     	if (latitude == '' || longitude == '' || address == '')
			     	{
						// Open directly via API
						$.magnificPopup.open({
						  items: {
						    src: '<div class="white-popup-block" style="width: 300px;">' + oTranslations['directory.please_enter_location'] + '</div>', // can be a HTML string, jQuery object, or CSS selector
						    type: 'inline'
						  }
						});
			     		return false;
			        }
			     	else
			     	{
						// tb_show('GoogleMap', $.ajaxBox('directory.gmap', 'height=300&width=485&item=' + item));
						// Open directly via API
						$.magnificPopup.open({
						  items: {
						    src: '<div class="white-popup-block-without-width" >' + '<div id="yndirectory_viewmap_' + item + '" style="height: 450px;"></div>' + '</div>', // can be a HTML string, jQuery object, or CSS selector
						    type: 'inline'
						  }
						});


						yndirectory.showMapsWithData('yndirectory_viewmap_' + item, [{latitude: latitude, longitude: longitude}], [address]);
			        }
					break;
				case 'edit':
					var item = $(ele).closest('.yndirectory-location').data('item');
					var obj = $('#yndirectory_edit #yndirectory_locationlist').find('[data-item="' + item + '"]');
			     	var latitude = obj.find('[data-inputid="lat"]').val();
			     	var longitude = obj.find('[data-inputid="lng"]').val();
			     	var address = obj.find('[data-inputid="address"]').val();
			     	if (latitude == '' || longitude == '' || address == '')
			     	{
						// Open directly via API
						$.magnificPopup.open({
						  items: {
						    src: '<div class="white-popup-block" style="width: 300px;">' + oTranslations['directory.please_enter_location'] + '</div>', // can be a HTML string, jQuery object, or CSS selector
						    type: 'inline'
						  }
						});
			     		return false;
			        }
			     	else
			     	{
						// tb_show('GoogleMap', $.ajaxBox('directory.gmap', 'height=300&width=485&item=' + item));
						// Open directly via API
						$.magnificPopup.open({
						  items: {
						    src: '<div class="white-popup-block-without-width" >' + '<div id="yndirectory_viewmap_' + item + '" style="height: 450px;"></div>' + '</div>', // can be a HTML string, jQuery object, or CSS selector
						    type: 'inline'
						  }
						});

						yndirectory.showMapsWithData('yndirectory_viewmap_' + item, [{latitude: latitude, longitude: longitude}], [address]);
			        }
					break;
			}
		}
	}
	, viewMapSuccess: function(item){
		if($('#yndirectory_pagename').length > 0 ){
			var yndirectory_pagename = $('#yndirectory_pagename').val();
			switch(yndirectory_pagename){
				case 'add':
					var obj = $('#yndirectory_add #yndirectory_locationlist').find('[data-item="' + item + '"]');
					if(obj.length > 0){
				     	var latitude = obj.find('[data-inputid="lat"]').val();
				     	var longitude = obj.find('[data-inputid="lng"]').val();
				     	var address = obj.find('[data-inputid="address"]').val();
				     	if (latitude == '' || longitude == '' || address == '')
				     	{
				     		yndirectory.getCurrentPosition();
				        }
				     	else
				     	{
				     		yndirectory.showMapByLatLong(address, latitude, longitude);
				        }
					}
					break;
			}
		}
	}
	, changeCustomFieldByMainCategory: function(iMainCategoryId){
		iBusinessId = $('#yndirectory_businessid').val();

        $Core.ajax('directory.changeCustomFieldByMainCategory',
        {
            type: 'POST',
            params:
            {
                action: 'changeCustomFieldByMainCategory'
                ,iMainCategoryId: iMainCategoryId
                ,iBusinessId : iBusinessId
            },
            success: function(sOutput)
            {
                var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
                	$('#yndirectory_customfield_category').html(oOutput.content);
                	// add validate each custom field
					$('#yndirectory_customfield_category').find('[data-isrequired="1"]').each(function(){
						var type = $(this).data('type');
						switch(type){
							case 'text':
								$(this).rules('add', {
									checkCustomFieldText: true
								});
								break;
							case 'textarea':
								$(this).rules('add', {
									checkCustomFieldTextarea: true
								});
								break;
							case 'select':
								$(this).rules('add', {
									checkCustomFieldSelect: true
								});
								break;
							case 'multiselect':
								$(this).rules('add', {
									checkCustomFieldMultiselect: true
								});
								break;
							case 'radio':
								$(this).rules('add', {
									checkCustomFieldRadio: true
								});
								break;
							case 'checkbox':
								$(this).rules('add', {
									checkCustomFieldCheckbox: true
								});
								break;
						}
					});

                } else
                {
                }
            }
        });
	}
	, addValidateForCustomField: function(){
    	// add validate each custom field
		$('#yndirectory_customfield_category').find('[data-isrequired="1"]').each(function(){
			var type = $(this).data('type');
			switch(type){
				case 'text':
					$(this).rules('add', {
						checkCustomFieldText: true
					});
					break;
				case 'textarea':
					$(this).rules('add', {
						checkCustomFieldTextarea: true
					});
					break;
				case 'select':
					$(this).rules('add', {
						checkCustomFieldSelect: true
					});
					break;
				case 'multiselect':
					$(this).rules('add', {
						checkCustomFieldMultiselect: true
					});
					break;
				case 'radio':
					$(this).rules('add', {
						checkCustomFieldRadio: true
					});
					break;
				case 'checkbox':
					$(this).rules('add', {
						checkCustomFieldCheckbox: true
					});
					break;
			}
		});
	}
	, appendPredefined: function(ele,classname){
		var now = +new Date();
		switch(classname){
			case 'location':
				var count = $('#yndirectory_add #yndirectory_locationlist .yndirectory-location').length + 1;

				var oCloned = $('#yndirectory_add #yndirectory_locationlist .yndirectory-location:first').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_location_99999').attr('id', 'yndirectory_location_' + now);
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    oCloned.find('span.yndirectory-error').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_locationlist').append('<div data-item="' + now + '" class="yndirectory-location">' + firstAnswer + '</div>');

				// search location by google api
			 	var input = ($("#yndirectory_add #yndirectory_locationlist #yndirectory_location_" + now)[0]);
			 	if (window.google){
			 		// do nothing
			 	} else {
					return false;
				}
			 	var autocomplete = new google.maps.places.Autocomplete(input);
			  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
			    	var place = autocomplete.getPlace();
				    if (!place.geometry) {
				     	return;
				    }

				    var $parent = $(input).closest('.yndirectory-location');
				    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
				    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
				    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
				    $parent.find('.yndirectory-error').last().remove();
				    $parent.find('.yndirectory-error').removeClass('yndirectory-error');
			    });
				break;
			case 'phone':
				var oCloned = $(ele).closest('.phone-wrapper').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    oCloned.find('span.yndirectory-error').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_phonelist').append('<div class="phone-wrapper">' + firstAnswer + '</div>');
				break;
			case 'fax':
				var oCloned = $(ele).closest('.fax-wrapper').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_faxlist').append('<div class="fax-wrapper">' + firstAnswer + '</div>');
				break;
			case 'web_address':
				var oCloned = $(ele).closest('.web_address-wrapper').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_websitelist').append('<div class="web_address-wrapper">' + firstAnswer + '</div>');
				break;
			case 'visiting_hours':
				var oCloned = $(ele).closest('.visiting_hours-wrapper').clone();
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_visitinghourlist').append('<div class="visiting_hours-wrapper">' + firstAnswer + '</div>');
				break;
			case 'customfield_user':
				var oCloned = $(ele).closest('.yndirectory-customfield-user').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('input').attr('maxlength', '255');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_customfield_user').append('<div class="yndirectory-customfield-user">' + firstAnswer + '</div>');
				break;
			case 'receivers':
				var oCloned = $(ele).closest('.yndirectory-receivers').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_receivers').append('<div class="yndirectory-receivers">' + firstAnswer + '</div>');
				break;
			case 'category':
		    	var iCnt = 0;
			    $('#yndirectory_categorylist .yndirectory-categorylist-maincategory').each(function()
			    {
			    	this.value = iCnt;
				    // increase
				    iCnt ++;
			    });
			    if(iCnt == 3){
			    	yndirectory.alertMessage(oTranslations['directory.you_can_add_only_maximum_3_categories']);
			    	break;
			    }

				var oCloned = $(ele).closest('.js_category_section_content').length ? $(ele).closest('.js_category_section_content').clone() : $(ele).closest('.js_mp_parent_holder').clone();
				oCloned.find('.yndirectory-categorylist-maincategory').attr('value', iCnt).attr('checked', false);
				oCloned.find('.js_mp_parent_holder').each(function(){
					if (parseInt(this.id.replace('js_mp_holder_', '')) > 0)
					{
						$(this).hide();
					}
				});
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();

			    let wrapper = $('<div data-item="' + now + '" class="category-wrapper"></div>');
			    oCloned.appendTo(wrapper.get(0));
			    wrapper.appendTo($(ele).closest('#yndirectory_categorylist').get(0));

			  	// when change main category
			  	$('#yndirectory_categorylist').find('[data-item="' + now + '"]').find('.js_mp_category_list').change(function()
				{
					var $this = $(this);
					var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
					iCatId = $this.val();
					if(!iCatId) {
						iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
					}

					// $.ajaxCall('advancedmarketplace.frontend_loadCustomFields', 'catid='+iCatId+'&lid='+$("#ilistingid").val());
					$parent = $this.closest('.category-wrapper');
					$parent.find('.js_mp_category_list').each(function()
					{
						if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
						{
							$parent.find('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

							this.value = '';
						}
					});

					$parent.find('#js_mp_holder_' + $(this).val()).show();
					$parentToCheckChangeCustomField = $this.closest('.js_mp_parent_holder');
					if($parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory').length > 0){
						var radiobutton = $parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory')[0];
						if(radiobutton.checked == true){
							var selected = $parentToCheckChangeCustomField.find('select option:selected').val();
							if(selected.length > 0){
								// change custom field by main category
								yndirectory.changeCustomFieldByMainCategory(selected);
							}
						}
					}
				});

				$('#yndirectory_categorylist').find('[data-item="' + now + '"]').find('.yndirectory-categorylist-maincategory').change(function(){
					$parent = $(this).closest('.js_mp_parent_holder');
					if($parent.length > 0){
						var selected = $parent.find('select option:selected').val();
						if(selected.length > 0){
							// change custom field by main category
							yndirectory.changeCustomFieldByMainCategory(selected);
						}
					}
				});

				break;
		}
	}
	, appendPredefinedForEdit: function(ele,classname){
		var now = +new Date();
		switch(classname){
			case 'location':
				var count = $('#yndirectory_edit #yndirectory_locationlist .yndirectory-location').length + 1;

				var oCloned = $('#yndirectory_edit #yndirectory_locationlist .yndirectory-location:first').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_location_1').attr('id', 'yndirectory_location_' + now);
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    oCloned.find('span.yndirectory-error').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_locationlist').append('<div data-item="' + now + '" class="yndirectory-location">' + firstAnswer + '</div>');

				// search location by google api
			 	var input = ($("#yndirectory_edit #yndirectory_locationlist #yndirectory_location_" + now)[0]);
			 	if (window.google){
			 		// do nothing
			 	} else {
					return false;
				}
			 	var autocomplete = new google.maps.places.Autocomplete(input);
			  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
			    	var place = autocomplete.getPlace();
				    if (!place.geometry) {
				     	return;
				    }

				    var $parent = $(input).closest('.yndirectory-location');
				    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
				    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
				    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
				    $parent.find('.yndirectory-error').last().remove();
				    $parent.find('.yndirectory-error').removeClass('yndirectory-error');
			    });
				break;
			case 'phone':
				var oCloned = $(ele).closest('.phone-wrapper').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    oCloned.find('span.yndirectory-error').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_phonelist').append('<div class="phone-wrapper">' + firstAnswer + '</div>');
				break;
			case 'fax':
				var oCloned = $(ele).closest('.fax-wrapper').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_faxlist').append('<div class="fax-wrapper">' + firstAnswer + '</div>');
				break;
			case 'web_address':
				var oCloned = $(ele).closest('.web_address-wrapper').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_websitelist').append('<div class="web_address-wrapper">' + firstAnswer + '</div>');
				break;
			case 'visiting_hours':
				var oCloned = $(ele).closest('.visiting_hours-wrapper').clone();
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_visitinghourlist').append('<div class="visiting_hours-wrapper">' + firstAnswer + '</div>');
				break;
			case 'customfield_user':
				var oCloned = $(ele).closest('.yndirectory-customfield-user').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_customfield_user').append('<div class="yndirectory-customfield-user">' + firstAnswer + '</div>');
				break;
			case 'category':
		    	var iCnt = 0;
			    $('#yndirectory_categorylist .yndirectory-categorylist-maincategory').each(function()
			    {
			    	this.value = iCnt;
				    // increase
				    iCnt ++;
			    });
			    if(iCnt == 3){
			    	yndirectory.alertMessage(oTranslations['directory.you_can_add_only_maximum_3_categories']);
			    	break;
			    }

				var oCloned = $(ele).closest('.category-wrapper').clone();
				oCloned.find('.yndirectory-categorylist-maincategory').attr('value', iCnt);
				oCloned.find('.js_mp_parent_holder').each(function(){
					if (parseInt(this.id.replace('js_mp_holder_', '')) > 0)
					{
						$(this).hide();
					}
				});
			    oCloned.find('#yndirectory_delete').show();
			    oCloned.find('#yndirectory_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#yndirectory_categorylist').append('<div data-item="' + now + '" class="category-wrapper">' + firstAnswer + '</div>');

			  	// when change main category
			  	$('#yndirectory_categorylist').find('[data-item="' + now + '"]').find('.js_mp_category_list').change(function()
				{
					var $this = $(this);
					var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
					// var iCatId = document.getElementById('js_mp_id_0').value;
					iCatId = $this.val();
					if(!iCatId) {
						iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
					}

					// $.ajaxCall('advancedmarketplace.frontend_loadCustomFields', 'catid='+iCatId+'&lid='+$("#ilistingid").val());
					$parent = $this.closest('.category-wrapper');
					$parent.find('.js_mp_category_list').each(function()
					{
						if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
						{
							$parent.find('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

							this.value = '';
						}
					});

					$parent.find('#js_mp_holder_' + $(this).val()).show();
					$parentToCheckChangeCustomField = $this.closest('.js_mp_parent_holder');
					if($parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory').length > 0){
						var radiobutton = $parentToCheckChangeCustomField.find('.yndirectory-categorylist-maincategory')[0];
						if(radiobutton.checked == true){
							var selected = $parentToCheckChangeCustomField.find('select option:selected').val();
							if(selected.length > 0){
								// change custom field by main category
								yndirectory.changeCustomFieldByMainCategory(selected);
							}
						}
					}
				});

				$('#yndirectory_categorylist').find('[data-item="' + now + '"]').find('.yndirectory-categorylist-maincategory').change(function(){
					$parent = $(this).closest('.js_mp_parent_holder');
					if($parent.length > 0){
						var selected = $parent.find('select option:selected').val();
						if(selected.length > 0){
							// change custom field by main category
							yndirectory.changeCustomFieldByMainCategory(selected);
						}
					}
				});

				break;
		}
	}
	, removePredefined: function(ele,classname){
		switch(classname){
			case 'location':
		       	$(ele).closest('.yndirectory-location').remove();
				break;
			case 'phone':
		       	$(ele).closest('.phone-wrapper').remove();
				break;
			case 'fax':
		       	$(ele).closest('.fax-wrapper').remove();
				break;
			case 'web_address':
		       	$(ele).closest('.web_address-wrapper').remove();
				break;
			case 'visiting_hours':
		       	$(ele).closest('.visiting_hours-wrapper').remove();
				break;
			case 'customfield_user':
		       	$(ele).closest('.yndirectory-customfield-user').remove();
				break;
			case 'receivers':
		       	$(ele).closest('.yndirectory-receivers').remove();
				break;
			case 'category':
				($(ele).closest('.js_category_section_content').length ? $(ele).closest('.js_category_section_content').remove() : $(ele).closest('.js_mp_parent_holder').remove());
		    	var iCnt = 0;
			    $('#yndirectory_categorylist .yndirectory-categorylist-maincategory').each(function()
			    {
			    	this.value = iCnt;
				    // increase
				    iCnt ++;
			    });
				break;
		}
	}
	, getCurrentPosition: function(){
		var result = null;
  		if (navigator.geolocation)
    	{
    		navigator.geolocation.getCurrentPosition(function(position){
    			if (position.coords.latitude)
    			{
    				result = {latitude: position.coords.latitude, longitude: position.coords.longitude};
            	}
    			else
    			{
    				result = {latitude: -33.8688, longitude: 151.2195};
        		}

        	});
    	}
  		else
		{
			result = {latitude: -33.8688, longitude: 151.2195};
  			// showMapByLatLong('', -33.8688, 151.2195);
		}

		return result;
	}
	, getCurrentPositionForBlock: function(type){
		var result = null;

  		if (navigator.geolocation)
    	{
    		navigator.geolocation.getCurrentPosition(function(position){

    			if (position.coords.latitude)
    			{

    				result = {latitude: position.coords.latitude, longitude: position.coords.longitude};

    				var latLng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
					geocoder = new google.maps.Geocoder();
					geocoder.geocode({
					        latLng: latLng
					        },
					        function(responses)
					        {
					           if (responses && responses.length > 0)
					           {
					           		if(type == 'search'){

									    $("#yndirectory_advsearch #yndirectory_searchblock_location").val(responses[0].formatted_address);
									    $("#yndirectory_advsearch input[data-inputid='location_address']").val(responses[0].formatted_address);
									 	$("#yndirectory_advsearch input[data-inputid='location_address_lat']").val(position.coords.latitude);
									    $("#yndirectory_advsearch input[data-inputid='location_address_lng']").val(position.coords.longitude);

									}
									else
									if(type == 'subscribe'){

    									$("#yndirectory_subscribe #yndirectory_subscribeblock_location").val(responses[0].formatted_address);
									    $("#yndirectory_subscribe input[data-inputid='subscribe_location_address']").val(responses[0].formatted_address);
									 	$("#yndirectory_subscribe input[data-inputid='subscribe_location_address_lat']").val(position.coords.latitude);
									    $("#yndirectory_subscribe input[data-inputid='subscribe_location_address_lng']").val(position.coords.longitude);

									}
					           }

					        }
					);
            	}
    			else
    			{

    				result = {latitude: -33.8688, longitude: 151.2195};
        		}

        	});
    	}
  		else
		{

			result = {latitude: -33.8688, longitude: 151.2195};
  			// showMapByLatLong('', -33.8688, 151.2195);
		}
		return result;
	}

   	, deleteRoleMember : function(iRoleId, iBusinessId){
		$.ajaxCall('directory.deleteRoleMember', 'role_id='+iRoleId + '&business_id=' + iBusinessId);
	}
	, deleteAnnouncement : function(iAnnouncementId){
		$.ajaxCall('directory.deleteAnnouncement', 'announcement_id='+iAnnouncementId);
	}
	, showMapByLatLong: function(address, latitude, longtitude){
          var pyrmont = new google.maps.LatLng(latitude, longtitude);

          var map = new google.maps.Map(document.getElementById('yndirectory_map'), {
              center: pyrmont,
              zoom: 15
            });

          var request = {
            location: pyrmont,
            radius: '500',
            query: address
          };

          var service = new google.maps.places.PlacesService(map);
          var poop = service.textSearch(request, showMapByLatLong_callback);

        function showMapByLatLong_callback(results, status) {
          if (status == google.maps.places.PlacesServiceStatus.OK) {

            var obj = eval(results);
            var place_id = obj[0].place_id;
            showMapByLatLong_showDetail(place_id);
            var reviews = google.maps.places.PlaceResult();
          }
        }
        function showMapByLatLong_showDetail(place_id){
          var request = {
            placeId: place_id
          };

          var infowindow = new google.maps.InfoWindow();
          var service = new google.maps.places.PlacesService(map);

          service.getDetails(request, function(place, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
              var marker = new google.maps.Marker({
                map: map,
                position: place.geometry.location
              });
              google.maps.event.addListener(marker, 'click', function() {
                infowindow.setContent(place.name);
                infowindow.open(map, this);
              });
            }
          });
        }
	}
	, refreshMap: function(){}
	, showAddress: function(){}
};

$Behavior.readyYnDirectory = function() {

	yndirectory.init();
	$(document).tooltip({
       selector: '[data-toggle="tooltip"]'
   });
};


// ====================================================================================

(function(window, undefined, $) {
	$.fn.extend({
		ajaxForm: function(options) {
			return this.each( // in case we want to init multiple elements
				function() {
					var $this;
					$this = $(this);
					var settings = $.extend({
						'ajax_action' : $this.data('ajax-action'),
						'result_div_id' : $this.data('result-div-id'),
						'custom_event' : $this.data('custom-event'),
						'is_validate' : $this.data('is-validate') === true,
						'is_prevent_submit' : $this.data('is-prevent-submit') === true,
					}, options);
					$this.data('ajaxForm', new ajaxForm(this, settings));
				}
			);
		},
	});

	$.fn.isBind = function(type)
	{
		var events = jQuery._data(this[0], "events");
		var data = events[type];
		if (data === undefined || data.length === 0) {
			return false;
		}
		return true;
	};

	var ajaxForm = ( function() {

		var _this, _ele ;

		var $paging = $('<input>').attr({
			type: 'hidden',
			name: 'val[page]',
			value: '1'
		});

		var customList =  {};

		ajaxForm = function(ele, settings) { // constructor
			_this = this;
			_ele = ele;
			_this.settings = settings;


			$(_ele).append($paging); // for paging
			$(document).bind(_this.settings['custom_event'], _this.handleTableDataChanged);
			if (!$(document).isBind('changepage')) {
				$(document).bind('changepage', _this.changePage); // to bind paging action
			}
			if (!$(document).isBind('changeCustom')) {
				$(document).bind('changeCustom', _this.changeCustom); // to bind paging action
			}
			$(_ele).on('change', function(e) {
					if(!$(e.target).hasClass('ynsaNoAjax')) {
						if(_this.settings.is_validate) {
							if(!$(_ele).valid() ) { // assume that we use valid as checking function
								return false;
							}
						}
						$paging.val(1);
						_this.submitAjaxForm();
					}
			});

			$(_ele).on('submit', function() {
				if(_this.settings.is_prevent_submit) {
					return false;
				}
			});
		};
		ajaxForm.prototype.changePage = function(evt, page) {
			$paging.val(page);
			_this.submitAjaxForm();
		}

		ajaxForm.prototype.changeCustom = function(evt, name, val) {
			if(typeof customList[name] !== 'undefined') {
				var $custom = customList[name];
			} else {

				var $custom = $('<input>').attr({
					type: 'hidden',
					name:  '',
					value: ''
				});

				customList[name] = $custom;
				$(_ele).append($custom);
			}

			$custom.val(val);
			$custom.attr('name', name);

			_this.submitAjaxForm();
		}

		ajaxForm.prototype.handleTableDataChanged = function(evt, html) {
			$('#' + _this.settings['result_div_id']).html(html);
		}


		ajaxForm.prototype.submitAjaxForm = function() {
			$(_ele).ajaxCall(_this.settings['ajax_action'], 'custom_event=' + _this.settings['custom_event']);
			$('#' + _this.settings['result_div_id']).yndWaiting('prepend');

			return false;

		};
		return ajaxForm;
	})();
	window.yndirectory.ajaxForm = ajaxForm;
}(window, undefined, jQuery));

$Ready(function(){
	$("#yndirectory-featured").owlCarousel({
        nav: true,
        loop: false,
        items: 1,
        autoplayTimeout: 3000,
    	autoplay: true,
        lazyContent: true,
        loop: true,
        navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>'],
	    responsive:{
	        0:{
	        	smartSpeed: 250,
	        	autoplayTimeout: 2500,
	        	dots: false
	        },
	        480:{
	       		dots: true
	        },
	        767:{
	        	smartSpeed: 800,
	        	autoplayTimeout: 3000
	        }
	    }
    });

    if($('.yndirectory-block-homepage-js').length) {
        ync_mode_view.init('yndirectory-block-homepage-js');
    };

    if($('.yndirectory-listing-js').length) {
        ync_mode_view.init('yndirectory-listing-js');
    };
})
