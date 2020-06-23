;

var ynauction = {
	pt : []
    , params : false
    , cookieCompareItemName : 'ynauction_compare_name'
	, setParams : function(params) {
		ynauction.params = JSON.parse(params);
	}
	, init: function()
	{
		if($('#ynauction_pagename').length > 0 ){
				var ynauction_pagename = $('#ynauction_pagename').val();
				switch(ynauction_pagename){
					case 'add':
						ynauction.initAdd();
						break;
					case 'edit':
						ynauction.initEdit();
						break;
					case 'compareauction':
						ynauction.initCompareAuction();
					break;
			}

		}
			ynauction.autoCheckedCompareCheckbox();

	}
	, sCorePath : ''
	, alertMessage: function(message, width){
		if(undefined == width || null == width){
			width = '300px';
		}
		$.magnificPopup.open({
		  items: {
		    src: '<div class="white-popup-block" style="width: ' + width + ';">' + message + '</div>',
		    type: 'inline'
		  }
		});
	}
    , advSearchDisplay: function(title_search)
    {
		var $form = $('#ynauction_adv_search');
		var $flag = $('#form_flag');

		if($flag.val() == 1)
		{
			$form.hide();
			$flag.val(0);
		}
		else
		{
			$form.show();
			$flag.val(1);
		}

		return false;
    }
    , confirmDeleteManyAuctions: function(){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_delete_auctions_that_you_selected'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button class="btn btn-sm btn-primary" onclick="ynauction.deleteManyAuctions();">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
            items: {
			    src: sHtml,
			    type: 'inline'
            }
        });
	}
	, confirmDeleteProductOnDetailPage: function(sLink){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
        		sHtml += '<span style="font-weight: bold; font-size: 16px;">';
        		sHtml += oTranslations['auction.confirm'];
        		sHtml += '</span>';
        		sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_delete_this_auction'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button class="btn btn-sm btn-primary" onclick="window.location.href = \'' + sLink + '\';">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
            items: {
			    src: sHtml,
			    type: 'inline'
            }
        });
	}
	, confirmDenyProductOnDetailPage: function(iProductId){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_deny_this_auction'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button class="btn btn-sm btn-primary" onclick="ynauction.denyProduct(' + iProductId + ');">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
            items: {
			    src: sHtml,
			    type: 'inline'
            }
        });
	}
	, denyProduct: function(iProductId){
		$('.mfp-close-btn-in .mfp-close').trigger('click');
		$.ajaxCall('auction.denyProduct', 'id=' + iProductId);
	}
	, confirmApproveProductOnDetailPage: function(iProductId){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_approve_this_auction'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button class="btn btn-sm btn-primary" onclick="ynauction.approveProduct(' + iProductId + ');">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
            items: {
			    src: sHtml,
			    type: 'inline'
            }
        });
	}
	, approveProduct: function(iProductId){
		$('.mfp-close-btn-in .mfp-close').trigger('click');
		$.ajaxCall('auction.approveProduct', 'id=' + iProductId);
	}
	, confirmPublishProductOnDetailPage: function(iProductId){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_publish_this_auction'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button  class="btn btn-sm btn-primary" onclick="ynauction.publishProduct(' + iProductId + ');">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
            items: {
			    src: sHtml,
			    type: 'inline'
            }
        });
	}
	, publishProduct: function(iProductId){
		$('.mfp-close-btn-in .mfp-close').trigger('click');
		$.ajaxCall('auction.publishProduct', 'id=' + iProductId);
	}
	, confirmCloseProductOnDetailPage: function(iProductId){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_close_this_auction'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button  class="btn btn-sm btn-primary" onclick="ynauction.closeProduct(' + iProductId + ');">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
            items: {
			    src: sHtml,
			    type: 'inline'
            }
        });
	}
	, closeProduct: function(iProductId){
		$('.mfp-close-btn-in .mfp-close').trigger('click');
		$.ajaxCall('auction.closeProduct', 'id=' + iProductId);
	}
    , deleteManyAuctions: function(){
		aSetAuctions = [];
		aSetBusinessText = "";
		$('.moderate_link_active').each(function(index){
			aSetAuctions.push($(this).attr('auctionid'));
		});
		aSetAuctionsText = aSetAuctions.join(",");
		$.ajaxCall('auction.deleteManyAuctions', "aSetAuctions=" + aSetAuctionsText + "");
	}
	, click_ynauction_detailauction_comparebutton: function(ele, product_id){
		var eleCheckbox = $(ele).closest('li').find('.ynauction-compare-checkbox')[0];
		$menucomparebutton =  $(ele).closest('li').find('#ynauction_detailcheckinlist_comparebutton');

		if(eleCheckbox.checked){
			ynauction.removeItemOutCompareDashboardWithAuctionId(product_id);
			$menucomparebutton.html('<i class="fa fa-files-o"></i> ' + oTranslations['auction.add_to_compare']);
		} else {
			eleCheckbox.checked = true;
			ynauction.clickCompareCheckbox(eleCheckbox);
			$menucomparebutton.html('<i class="fa fa-files-o"></i> ' + oTranslations['auction.remove_from_compare']);
		}

	}
	, clickCompareCheckbox: function(ele){

		var auctionid = $(ele).data('compareitemauctionid');
		var name = ynauction.cookieCompareItemName;
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
		var $ele = $body.find('[data-compareitemauctionid="' + auctionid + '"]');
		if(ele.checked){
			// check --> add
			data += ',' + auctionid
			data = ynauctionhelper.trim(data, ',');
			setCookie(name, data, 1);
			// check brother checkbox
			if($ele.length > 0){
				for(idx2 = 0; idx2 < $ele.length; idx2 ++){
					$ele[idx2].checked = true;
				}
			}

			// add into compare dashboard
			ynauction.addItemIntoCompareDashboard(ele);
		} else {
			// uncheck --> remove
			var isExist = false;
			for(idx = 0; idx < aData.length; idx ++){
				if(auctionid == aData[idx]){
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
			ynauction.removeItemOutCompareDashboard(ele);
		}

	}
	, addItemIntoCompareDashboard: function(checkboxObj){
    	var $checkboxObj = $(checkboxObj);
		if($('#ynauction_compare_dashboard').length > 0){
			// add item
	        $Core.ajax('auction.compareGetInfoAuction',
	        {
	            type: 'POST',
	            params:
	            {
	                product_id: $checkboxObj.data('compareitemauctionid')
	            },
	            success: function(sOutput)
	            {
	            	var oOutput = $.parseJSON(sOutput);
	                if(oOutput.status == 'SUCCESS')
	                {
	                	var aCategory = oOutput.aCategory;
	                	var $category_tab = $('#ynauction_compare_tab_menu_item_' + aCategory.category_id);
	                	if($category_tab.length == 0){
	                		// add new category tab
                			var id_tabcontent = 'ynauction_compare_tabcontent_' + aCategory.category_id;
	                		var sHtml_tabs_menu = '';
                			sHtml_tabs_menu += '<li id="ynauction_compare_tab_menu_item_' + aCategory.category_id + '" data-counting="1">';
	                			sHtml_tabs_menu += '<a href="#' + id_tabcontent + '" rel="' + id_tabcontent + '">' + aCategory.title;
	                				sHtml_tabs_menu += ' <span id="ynauction_compare_tab_menu_counting_' + aCategory.category_id + '">(1)';
	                				sHtml_tabs_menu += '</span>';
	                			sHtml_tabs_menu += '</a>';
                				sHtml_tabs_menu += ' <span onclick="ynauction.removeItemOutCompareDashboardWithCategoryId(' + aCategory.category_id + ');"><i class="fa fa-times"></i>';
                				sHtml_tabs_menu += '</span>';
                			sHtml_tabs_menu += '</li>';
                			$("#ynauction_compare_tabs_menu").append(sHtml_tabs_menu);

                			// add new content tab
                			var sHtml_tabs_container = '';
            				sHtml_tabs_container += '<div id="' + id_tabcontent + '">';
            					sHtml_tabs_container += '<ul id="ynauction_compare_tabs_container_list_' + aCategory.category_id + '">';
            					sHtml_tabs_container += '</ul>';
            				sHtml_tabs_container += '</div>';
                			$("#ynauction_compare_tabs_container").append(sHtml_tabs_container);

                			// refresh tabs
        					// $("#ynauction_compare_tabs").tabs('refresh');
							$('#ynauction_compare_tab_menu_item_' + aCategory.category_id).on('click', 'a', function(e){
								// Make the old tab inactive.
								var $active = $('#ynauction_compare_tabs_menu').find('a.ynauction-compare-tab-menu-active');
								$active.removeClass('ynauction-compare-tab-menu-active');
								if($active.length > 0){
									$content = $($active[0].hash);
									$content.hide();
								}

								// Update the variables with the new link and content
								$active = $(this);
								$content = $(this.hash);

								// Make the tab active.
								$active.addClass('ynauction-compare-tab-menu-active');
								$content.show();

								// Prevent the anchor's default click action
								e.preventDefault();
							});
	                	} else {
		                	// update counting of compare category tab
		                	var counting = $category_tab.data('counting');
		                	counting = parseInt(counting) + 1;
		                	$category_tab.data('counting', counting);
		                	$('#ynauction_compare_tab_menu_counting_' + aCategory.category_id).html('(' + counting + ')');
	                	}

	                	// add into list
	                	var sHtml_tabs_container = '';
        				sHtml_tabs_container += '<li id="ynauction_compare_tabs_container_item_' + $checkboxObj.data('compareitemauctionid') + '">';
            				sHtml_tabs_container += '<div class="ynauction-compare-tabs-container-image">';
                				sHtml_tabs_container += '<a href="' + $checkboxObj.data('compareitemlink') + '">';
	                				sHtml_tabs_container += '<img src="' + $checkboxObj.data('compareitemlogopath') + '" />';
                				sHtml_tabs_container += '</a>';
            				sHtml_tabs_container += '</div>';
            				sHtml_tabs_container += '<span class="ynauction-compare-tabs-container-item-close" onclick="ynauction.removeItemOutCompareDashboardWithAuctionId(' + $checkboxObj.data('compareitemauctionid') + ');"><i class="fa fa-times"></i>';
            				sHtml_tabs_container += '</span>';
            				sHtml_tabs_container += '<span class="ynauction-compare-tabs-container-item-title">';
                				sHtml_tabs_container += '<a href="' + $checkboxObj.data('compareitemlink') + '">' + $checkboxObj.data('compareitemname');
                				sHtml_tabs_container += '</a>';
            				sHtml_tabs_container += '</span>';
        				sHtml_tabs_container += '</li>';
        				$('#ynauction_compare_tabs_container_list_' + aCategory.category_id).append(sHtml_tabs_container);

        				// active this category tab
        				$( '#ynauction_compare_tab_menu_item_' + aCategory.category_id + ' a' ).trigger( "click" );

        				$('#ynauction_compare_dashboard_tabs').css('height', $('#ynauction_compare_tabs_menu').height()+5 );
	                } else {
	                	ynauction.alertMessage(oOutput.message);
	                }
	            }
	        });
		} else {
			ynauction.initCompareItemBlock();
		}
	}
	, removeItemOutCompareDashboard: function(checkboxObj){
    	var $checkboxObj = $(checkboxObj);
        $Core.ajax('auction.compareGetInfoAuction',
        {
            type: 'POST',
            params:
            {
                product_id: $checkboxObj.data('compareitemauctionid')
            },
            success: function(sOutput)
            {
            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
                	var aCategory = oOutput.aCategory;
                	var $category_tab = $('#ynauction_compare_tab_menu_item_' + aCategory.category_id);
                	if($category_tab.length > 0){
	                	// update counting of compare category tab
	                	var counting = $category_tab.data('counting');
	                	counting = parseInt(counting) - 1;
	                	$category_tab.data('counting', counting);
	                	$('#ynauction_compare_tab_menu_counting_' + aCategory.category_id).html('(' + counting + ')');

	                	// remove out list
        				$('#ynauction_compare_tabs_container_item_' + $checkboxObj.data('compareitemauctionid')).remove();
        				// remove "li" on compare page
        				$('#ynauction_compare_page_item_' + $checkboxObj.data('compareitemauctionid')).remove();
        				// update "option" on comare page
    					$option = $('#ynauction_compareauction_detail_option_' + aCategory.category_id);
        				if($option.length > 0){
        					var val = parseInt($option.data('comparedetailtotalitem'), 10) ;
        					var html = $option.html();
        					val = val - 1;
        					html = html.replace(/\(\d+\)/, '(' + val + ')');
        					$option.html(html);
        					$option.data('comparedetailtotalitem', val);
        				}

        				// remove data list if empty
        				if($('#ynauction_compare_tabs_container_list_' + aCategory.category_id).find('li').length == 0){
        					// remove data list
        					$('#ynauction_compare_tabcontent_' + aCategory.category_id).remove();
        					// remove category tab
        					$('#ynauction_compare_tab_menu_item_' + aCategory.category_id).remove();
        					// refresh
        					// $("#ynauction_compare_tabs").tabs('refresh');
        					// $('#ynauction_compare_tabs').tabs({ selected: 0 });
	        				if($('#ynauction_compare_tabs_menu').find('li').length > 0){
		        				$( $('#ynauction_compare_tabs_menu').find('li')[0]).find('a').trigger( "click" );
        					}
        				} else {
        					$('#ynauction_compare_tab_menu_item_' + aCategory.category_id + ' a').trigger( "click" );
        				}
        				if($('#ynauction_compare_tabs_menu').find('li').length == 0){
        					// remove compare box
        					$('#ynauction_compare_dashboard').remove();
        					$('#ynauction_compare_dashboard_min').remove();
        				}

        				if($('#ynauction_detailcheckinlist_comparebutton').attr('auctionid') == $checkboxObj.data('compareitemauctionid')){
								$menucomparebutton =  $('body').find('#ynauction_detailcheckinlist_comparebutton');
								$menucomparebutton.html('<i class="fa fa-files-o"></i> ' + oTranslations['auction.add_to_compare']);
        				}
                	}
                } else {
                	ynauction.alertMessage(oOutput.message);
                }
            }
        });
	}
	, initCompareAuction  : function(){
		$("#ynauction_compareauction_detail_category").change(function(){
			var sCompareLink = $(this).data('comparelink');
			var option = $(this).find('option:selected');
			var selected = this.value;
			var comparedetailtotalitem = $(option).data('comparedetailtotalitem');
			if(comparedetailtotalitem > 1){
				sCompareLink += 'category_' + selected + '/';
				window.location.href = sCompareLink;
				return true;
			} else {
				ynauction.alertMessage(oTranslations['auction.please_select_more_than_one_entry_for_the_comparison']);
				return false;
			}
		});
	}
	,initCompareItemBlock: function(){
		var name = ynauction.cookieCompareItemName;
		var data = getCookie(name);
		if(null != data && '' != data){

	        $Core.ajax('auction.initCompareItemBlock',
	        {
	            type: 'POST',
	            params:
	            {
	                listOfAuctionIdToCompare: data
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
                			var list_auction = aCategory.list_auction;
                			var id_tabcontent = 'ynauction_compare_tabcontent_' + aCategory.data.category_id;
                			sHtml_tabs_menu += '<li id="ynauction_compare_tab_menu_item_' + aCategory.data.category_id + '" data-counting="' + list_auction.length + '">';
	                			sHtml_tabs_menu += '<a href="#' + id_tabcontent + '" rel="' + id_tabcontent + '">' + aCategory.data.title;
	                				sHtml_tabs_menu += ' <span id="ynauction_compare_tab_menu_counting_' + aCategory.data.category_id + '">(' + list_auction.length + ')';
	                				sHtml_tabs_menu += '</span>';
	                			sHtml_tabs_menu += '</a>';
                				sHtml_tabs_menu += ' <span onclick="ynauction.removeItemOutCompareDashboardWithCategoryId(' + aCategory.data.category_id + ');"><i class="fa fa-times"></i>';
                				sHtml_tabs_menu += '</span>';
                			sHtml_tabs_menu += '</li>';

            				sHtml_tabs_container += '<div id="' + id_tabcontent + '">';
                					sHtml_tabs_container += '<ul id="ynauction_compare_tabs_container_list_' + aCategory.data.category_id + '">';
                			for(idx2 = 0; idx2 < list_auction.length; idx2 ++){
		                				sHtml_tabs_container += '<li id="ynauction_compare_tabs_container_item_' + list_auction[idx2].product_id + '">';
			                				sHtml_tabs_container += '<div class="ynauction-compare-tabs-container-image">';
				                				sHtml_tabs_container += '<a href="' + list_auction[idx2].item_link + '">';
					                				sHtml_tabs_container += '<img src="' + list_auction[idx2].logo_path + '" />';
				                				sHtml_tabs_container += '</a>';
			                				sHtml_tabs_container += '</div>';
			                				sHtml_tabs_container += '<span class="ynauction-compare-tabs-container-item-close" onclick="ynauction.removeItemOutCompareDashboardWithAuctionId(' + list_auction[idx2].product_id + ');"><i class="fa fa-times"></i>';
			                				sHtml_tabs_container += '</span>';
			                				sHtml_tabs_container += '<span class="ynauction-compare-tabs-container-item-title">';
				                				sHtml_tabs_container += '<a href="' + list_auction[idx2].item_link + '">' + list_auction[idx2].name;
				                				sHtml_tabs_container += '</a>';
			                				sHtml_tabs_container += '</span>';

			                				sHtml_tabs_container += '<div style="display: none;">';
			                					sHtml_tabs_container += '<input type="checkbox" ';
			                					sHtml_tabs_container += ' data-compareitemauctionid="' + list_auction[idx2].product_id + '"';
			                					// sHtml_tabs_container += ' data-compareitemname="' + list_auction[idx2].item_link + '"';
			                					sHtml_tabs_container += ' data-compareitemlink="' + list_auction[idx2].item_link + '"';
			                					sHtml_tabs_container += ' data-compareitemlogopath="' + list_auction[idx2].logo_path + '"';
			                					sHtml_tabs_container += ' onclick="ynauction.clickCompareCheckbox(this);" ';
			                					sHtml_tabs_container += ' class="ynauction-compare-checkbox">';
			                				sHtml_tabs_container += '</div>';

		                				sHtml_tabs_container += '</li>';
                			}
                					sHtml_tabs_container += '</ul>';
            				sHtml_tabs_container += '</div>';
                		}

                		sHtml += '<div id="ynauction_compare_dashboard_content">';
	                		sHtml += '<div id="ynauction_compare_dashboard_tabs"></div>';
		                	sHtml += '<div id="ynauction_compare_dashboard">';
			                	sHtml += '<div id="ynauction_compare_header">';
				                	sHtml += '<div id="ynauction_compare_button_compare" class="btn btn-xs btn-primary" onclick="ynauction.redirectCompareDetail(this);" data-comparelink="' + oOutput.sCompareLink + '">' + oTranslations['auction.compare'];
				                	sHtml += '</div>';
				                	sHtml += '<div id="ynauction_compare_button_hide" onclick="ynauction.minimizeCompareDashboard();"><i class="fa fa-chevron-down"></i>';
				                	sHtml += '</div>';
			                	sHtml += '</div>';
			                	sHtml += '<div id="ynauction_compare_tabs">';
			                		sHtml += '<ul id="ynauction_compare_tabs_menu">';
			                			sHtml += sHtml_tabs_menu;
			                		sHtml += '</ul>';
			                		sHtml += '<div id="ynauction_compare_tabs_container">';
			                			sHtml += sHtml_tabs_container;
			                		sHtml += '</div>';
			                	sHtml += '</div>';
		                	sHtml += '</div>';
		                	sHtml += '<div id="ynauction_compare_dashboard_hidden" style="display: none;">';
		                	sHtml += '</div>';
	                	sHtml += '</div>';
	                	sHtml += '<div id="ynauction_compare_dashboard_min" style="display: none;">';
	                		sHtml += '<span class="btn btn-sm btn-primary" onclick="ynauction.maximizeCompareDashboard();">' + oTranslations['auction.compare'] + '<i class="fa fa-chevron-up"></i>';
	                		sHtml += '</span>';
	                	sHtml += '</div>';

	                	// set inner html
	                	$('#ynauction_auction_compareitem').html(sHtml);

	                	// bind event
	                	// $( "#ynauction_compare_tabs" ).tabs();
						$('#ynauction_compare_tabs_menu').each(function(){
							// For each set of tabs, we want to keep track of
							// which tab is active and it's associated content
							var $active, $content, $links = $(this).find('a');

							// If the location.hash matches one of the links, use that as the active tab.
							// If no match is found, use the first link as the initial active tab.
							$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
							$active.addClass('ynauction-compare-tab-menu-active');

							$content = $($active[0].hash);

							// Hide the remaining content
							$links.not($active).each(function () {
								$(this.hash).hide();
							});

							// Bind the click event handler
							$(this).on('click', 'a', function(e){
								// Make the old tab inactive.
								$active.removeClass('ynauction-compare-tab-menu-active');
								$content.hide();

								// Update the variables with the new link and content
								$active = $(this);
								$content = $(this.hash);

								// Make the tab active.
								$active.addClass('ynauction-compare-tab-menu-active');
								$content.show();

								// Prevent the anchor's default click action
								e.preventDefault();
							});
						});

						$('#ynauction_compare_dashboard').css('width', $('#content_holder').width() );
						//$('#ynauction_compare_dashboard_min').css('width', $('#content_holder').width() );
						$('#ynauction_compare_dashboard_tabs').css('height', $('#ynauction_compare_tabs_menu').height()+5 );

						// check status history of compare
						var ynauction_comparebox_show_name = 'ynauction_comparebox_show';
						var ynauction_comparebox_show_data = getCookie(ynauction_comparebox_show_name);
						switch(ynauction_comparebox_show_data){
							case 'minimize':
								ynauction.minimizeCompareDashboard();
								break;
							case 'maximize':
								ynauction.maximizeCompareDashboard();
								break;
							default:
								ynauction.maximizeCompareDashboard();
								break;
						}

	                } else {
	                	ynauction.alertMessage(oOutput.message);
	                }
	            }
	        });
		}
	}
	, redirectCompareDetail: function(ele){
		var sCompareLink = $(ele).data('comparelink');
		$ulMenu = $('#ynauction_compare_tabs_menu');
		var category_id = $ulMenu.find('.ynauction-compare-tab-menu-active').attr('href');
		category_id = category_id.replace("#ynauction_compare_tabcontent_", "");
		$ulItemList = $('#ynauction_compare_tabs_container_list_' + category_id);
		var idList = '';
		var count = 0;
		$ulItemList.find('li').each(function() {
			var id = this.id;
			id = id.replace("ynauction_compare_tabs_container_item_", "");
			idList += id +',';
			count ++;
		});
		if(count > 1){
			idList = ynauctionhelper.trim(idList, ',');
			sCompareLink += 'category_' + category_id + '/';
			window.location.href = sCompareLink;
			return true;
		} else {
			ynauction.alertMessage(oTranslations['auction.please_select_more_than_one_entry_for_the_comparison']);
			return false;
		}
	}

	, featureInBox  : function(ele, iProductId){
		// Implement later.
	}
	, minimizeCompareDashboard: function(){
		var name = 'ynauction_comparebox_show';
		setCookie(name, 'minimize', 1);
		$('#ynauction_compare_dashboard_content').hide();
		$('#ynauction_compare_dashboard_min').show();
	}
	, maximizeCompareDashboard: function(){
		var name = 'ynauction_comparebox_show';
		setCookie(name, 'maximize', 1);
		$('#ynauction_compare_dashboard_min').hide();
		$('#ynauction_compare_dashboard_content').show();
	}
	, removeItemOutCompareDashboardWithAuctionId: function(product_id){
		var $body = $('body');
		var $ele = $body.find('[data-compareitemauctionid="' + product_id + '"]');
		if($ele.length > 0){
			$ele[0].checked = false;
			ynauction.clickCompareCheckbox($ele[0]);
		}
	}
	, removeItemOutCompareDashboardWithCategoryId: function(category_id){
		$ele = $('#ynauction_compare_tabcontent_' + category_id);
		if($ele.length > 0){
			$ele.find('span.ynauction-compare-tabs-container-item-close').trigger('click');
		}
	}
	, removeItemOutCompareDashboardOnComparePage: function(product_id){
		// TO DO: remove on compare page

		// remove from cookie
		ynauction.removeItemOutCompareDashboardWithAuctionId(product_id);
	}
	, autoCheckedCompareCheckbox: function(){
		var name = ynauction.cookieCompareItemName;
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
		for(idx = 0; idx < aData.length; idx ++){
			// check brother checkbox
			var $ele = $body.find('[data-compareitemauctionid="' + aData[idx] + '"]');
			if($ele.length > 0){
				for(idx2 = 0; idx2 < $ele.length; idx2 ++){
					$ele[idx2].checked = true;

					// change title of compare button on detail page
					$parentEle = $($ele[idx2]).closest('ul.ynauction-detailcheckinlist');
					if($parentEle.length > 0){
						$menucomparebutton = $parentEle.find('#ynauction_detailcheckinlist_comparebutton');
						if($menucomparebutton.length > 0){
							$menucomparebutton.html('<i class="fa fa-files-o"></i> ' + oTranslations['auction.remove_from_compare']);
						}
					}

				}
			}
		}
	}
	, confirmDeleteAuction: function(iProductId){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_delete_this_auction'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button class="btn btn-sm btn-primary" onclick="ynauction.deleteAuction(' + iProductId + ');">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
			items: {
			    src: sHtml,
			    type: 'inline'
			}
		});
	}
	, confirmCloseAuction: function(iProductId){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_close_this_auction_notice_it_cannot_be_re_opened'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button class="btn btn-sm btn-primary" onclick="ynauction.closeAuction(' + iProductId + ');">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
			items: {
			    src: sHtml,
			    type: 'inline'
			}
		});
	}
	, deleteAuction: function(iProductId){
		$.ajaxCall('auction.deleteAuction', 'iProductId=' + iProductId);
	}
	, closeAuction: function(iProductId){
		$.ajaxCall('auction.close', 'iProductId=' + iProductId);
	}
    , changeViewHomePage: function(first){
		$('#ynauction_index #ynauction_menu .homepage-view-menu').click(function(){
			if ($('#ynauction_menu_viewtype_addcookie').val() == 1 && $('#ynauction_menu_viewtype_addcookie_triggerclick').val() == 1)
            {
				var name = 'ynauction_menu_viewtype';
				switch(this.id){
					case 'ynauction_listview_menu':
						setCookie(name, 'listview', 1);
						break;
					case 'ynauction_gridview_menu':
						setCookie(name, 'gridview', 1);
						break;
					case 'ynauction_pinboardview_menu':
						setCookie(name, 'pinboardview', 1);
						break;
				}
			}

			$('#ynauction_index #ynauction_menu .homepage-view-menu').removeClass('view-menu-active');
			$(this).addClass('view-menu-active');

			$('#ynauction_index .homepage-view').hide();
			$('#ynauction_index #ynauction_' + $(this).attr('value')).show();

			$('.js_pager_view_more_link').show();

			viewtype =$(this).attr('value');

			pager = $('#ynauction_block_homepage .pager li a');
			$('#ynauction_block_homepage .pager_ynauction  a').each(function(){
				text = $(this).attr('href').split('/viewtype_listview').join('');
				text1 = text.split('/viewtype_gridview').join('');
				text2 = text1.split('/viewtype_pinboardview').join('');

				if($(this).hasClass('not') == false){
					if(viewtype == 'listview' ||viewtype == 'gridview' ||viewtype == 'pinboardview' ){
						$(this).attr('href',text2+'/viewtype_'+viewtype);
					}
				}
			});

			if ( $(this).attr('value') == 'pinboardview' ) {
				(function ($){
                    var handler = $('#ynauction_pinboardview > div');

                    handler.wookmark({
                        // Prepare layout options.
                        autoResize: true, // This will auto-update the layout when the browser window is resized.
                        container: $('#ynauction_pinboardview'), // Optional, used for some extra CSS styling
                        offset: 10, // Optional, the distance between grid items
                        outerOffset: 0, // Optional, the distance to the containers border
                        itemWidth: 190 // Optional, the width of a grid item
                    });

			    })(jQuery);
			}
		});

		if($('#ynauction_menu_viewtype').length > 0 && first == 0){
			var iTimeout = 100;
			if($('.yntour_already_created_step').length != 0) return;
			switch($('#ynauction_menu_viewtype').val()){
				case 'listview':
					setTimeout(function(){ $( '#ynauction_listview_menu' ).trigger( "click" ); $('#ynauction_menu_viewtype_addcookie_triggerclick').val(1); }, iTimeout);
					break;
				case 'gridview':
					setTimeout(function(){ $( '#ynauction_gridview_menu' ).trigger( "click" ); $('#ynauction_menu_viewtype_addcookie_triggerclick').val(1); }, iTimeout);
					break;
				case 'pinboardview':
					setTimeout(function(){ $( '#ynauction_pinboardview_menu' ).trigger( "click" ); $('#ynauction_menu_viewtype_addcookie_triggerclick').val(1); }, iTimeout);
					break;
			}
		}

	}
    , initAdvancedSearch: function(){
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
    }
    , featureBox  : function(iProductId){
		tb_show('Auction', $.ajaxBox('auction.featureInBox', 'height=300&width=420&iProductId=' + iProductId));
	}
   , initAdd: function()
	{
		if($('#ynauction_add_auction_form').length == 0){
			return false;
		}

		$('.js_mp_category_list').change(function()
		{
			var $this = $(this);
			var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
			// var iCatId = document.getElementById('js_mp_id_0').value;
			iCatId = $this.val();
			if(!iCatId) {
				iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
			}

			$parent = $this.closest('.table_right');
			$parent.find('.js_mp_category_list').each(function()
			{
				if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
				{
					$parent.find('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

					this.value = '';
				}
			});

			$parent.find('#js_mp_holder_' + $(this).val()).show();
			var selected = $parent.find('#js_mp_id_0').val();
			if(selected.length >= 0){
				// change custom field by main category
				ynauction.changeCustomFieldByMainCategory(selected,null);
			}
		});

		$('#ynauction_feature_number_days').on('keyup', ynauction.onChangeFeatureFeeTotal);
		$('#ynauction_reserve_price').on('keyup', ynauction.onChangeReservePrice);

		// validate form
		ynauction.initValidator($('#ynauction_add_auction_form'));

		/*name*/
		$('#ynauction_add_auction_form #name').rules('add', {
			required: true
		});

		/*shipping*/
		$('#ynauction_add_auction_form #shipping').rules('add', {
			required: true
		});

		/*category*/
		$('#ynauction_add_auction_form #ynauction_categorylist .table_right:first #js_mp_id_0').rules('add', {
			required: true
		});

		if($('#ynauction_add_auction_form #ynauction_uom').length > 0){

			$('#ynauction_add_auction_form #ynauction_uom').rules('add', {
				required: true
			});

		}

		/*reserve price*/
		$('#ynauction_add_auction_form #ynauction_reserve_price').rules('add', {
			required: true,
			min:0.01
		});

		$('#ynauction_add_auction_form #ynauction_buynow_price').rules('add', {
			required: true,
			min:0.01
		});


		$('#ynauction_add_auction_form #ynauction_quantity').rules('add', {
			required: true,
			min: 1,
		});

		/*custom field*/
		jQuery.validator.addMethod('checkCustomFieldText', function(value, element, params) {
			var result = false;
			if(element.value.length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldTextarea', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldSelect', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldMultiselect', function(value, element, params) {
			var result = false;
			var select = $(element).val();
			if(undefined != select && null != select && select.length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldCheckbox', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:checkbox').is(':checked')){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldRadio', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:radio').is(':checked')){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);



	}
	,initEdit: function()
	{

		if($('#ynauction_edit_auction_form').length == 0){
			return false;
		}

		$('.js_mp_category_list').change(function()
		{
			var $this = $(this);
			var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
			// var iCatId = document.getElementById('js_mp_id_0').value;
			iCatId = $this.val();
			if(!iCatId) {
				iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
			}

			$parent = $this.closest('.table_right');
			$parent.find('.js_mp_category_list').each(function()
			{
				if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
				{
					$parent.find('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

					this.value = '';
				}
			});

			$parent.find('#js_mp_holder_' + $(this).val()).show();
			var selected = $parent.find('#js_mp_id_0').val();
			if(selected.length >= 0){
				ynauction.changeCustomFieldByMainCategory(selected,null);
			}
		});

		$('#ynauction_feature_number_days').on('keyup', ynauction.onChangeFeatureFeeTotal);

		// validate form
		ynauction.initValidator($('#ynauction_edit_auction_form'));

		/*name*/
		$('#ynauction_edit_auction_form #name').rules('add', {
			required: true
		});

		/*shipping*/
		$('#ynauction_edit_auction_form #shipping').rules('add', {
			required: true
		});

		/*category*/
		$('#ynauction_edit_auction_form #ynauction_categorylist .table_right:first #js_mp_id_0').rules('add', {
			required: true
		});

		if($('#ynauction_add_auction_form #ynauction_uom').length > 0){

			$('#ynauction_add_auction_form #ynauction_uom').rules('add', {
				required: true
			});

		}

		/*reserve price*/
		if($('#ynauction_edit_auction_form #ynauction_canEditAll').val()){
			$('#ynauction_edit_auction_form #ynauction_reserve_price').rules('add', {
				required: true,
				min:0.01
			});
			$('#ynauction_edit_auction_form #ynauction_buynow_price').rules('add', {
				required: true,
				min:0.01
			});

		}


		$('#ynauction_edit_auction_form #ynauction_quantity').rules('add', {
			required: true,
			min: 1,
		});


		/*custom field*/
		jQuery.validator.addMethod('checkCustomFieldText', function(value, element, params) {
			var result = false;
			if(element.value.length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldTextarea', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldSelect', function(value, element, params) {
			var result = false;
			if($(element).val().length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldMultiselect', function(value, element, params) {
			var result = false;
			var select = $(element).val();
			if(undefined != select && null != select && select.length > 0){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldCheckbox', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:checkbox').is(':checked')){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);
		jQuery.validator.addMethod('checkCustomFieldRadio', function(value, element, params) {
			var result = false;
			var name = element.name;
			if($('input[name="' + name + '"]:radio').is(':checked')){
				result = true;
			}
			return result;
		}, oTranslations['auction.this_field_is_required']);




	}
	,onChangeFeatureFeeTotal: function(){
		if('' == $('#ynauction_feature_number_days').val() || (isNaN(parseInt($('#ynauction_feature_number_days').val())))){
			$('#ynauction_feature_number_days').val('');
			$('#ynauction_feature_fee_total').val('');
		} else {
			$('#ynauction_feature_number_days').val(parseInt($('#ynauction_feature_number_days').val()));
			$('#ynauction_feature_fee_total').val(parseInt($('#ynauction_feature_number_days').val()) * parseInt($('#ynauction_defaultfeaturefee').val()));
			$('#ynauction_text_defaultpublishfee').text( parseInt($('#ynauction_defaultpublishfee').val()) + parseInt($('#ynauction_feature_number_days').val()) * parseInt($('#ynauction_defaultfeaturefee').val()));
		}

	}
	,onChangeReservePrice : function(){
		if('' == $('#ynauction_reserve_price').val() || (isNaN(parseInt($('#ynauction_reserve_price').val())))){
			$('#ynauction_reserve_price').val('');
			$('#ynauction_buynow_price').val('');
		} else {
			$('#ynauction_buynow_price').val(parseFloat($('#ynauction_reserve_price').val()*$('#ynauction_ratio_buyitnow_price').val() / 100));
		}
	}
	, initValidator: function(element){
		jQuery.validator.messages.required  = oTranslations['auction.this_field_is_required'];
		jQuery.validator.messages.min       = oTranslations['auction.please_edit_quantity_more_than_zero'] ;
		element.validate({
			errorPlacement: function (error, element) {
				if(element.is(":radio") || element.is(":checkbox")) {
					// error.appendTo(element.parent());
					error.appendTo($(element).closest('.table_right'));
				} else {
					error.appendTo(element.parent());
				}
			},
			errorClass: 'ynauction-error',
			errorElement: 'span',
			debug: false
		});
	}
	, appendPredefined: function(ele,classname){
		var now = +new Date();
		switch(classname){
			case 'customfield_user':
				var oCloned = $(ele).closest('.ynauction-customfield-user').clone();
			    oCloned.find('input').attr('value', '');
			    oCloned.find('#ynauction_delete').show();
			    oCloned.find('#ynauction_add').remove();
			    var oFirst = oCloned.clone();
			    var firstAnswer = oFirst.html();
			    $(ele).closest('#ynauction_customfield_user').append('<div class="ynauction-customfield-user">' + firstAnswer + '</div>');
			break;
		}
	}
	, removePredefined: function(ele,classname){
		switch(classname){
			case 'customfield_user':
		       	$(ele).closest('.ynauction-customfield-user').remove();
				break;
		}
	}
	, changeCustomFieldByMainCategory: function(iMainCategoryId,iProductId){
		if(null == iProductId){
			iProductId = $('#ynauction_auctionid').val();
		}

        $Core.ajax('auction.changeCustomFieldByMainCategory',
        {
            type: 'POST',
            params:
            {
                action: 'changeCustomFieldByMainCategory'
                ,iMainCategoryId : iMainCategoryId
                ,iProductId 	 : iProductId
            },
            success: function(sOutput)
            {
                var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
                	$('#ynauction_customfield_category').html(oOutput.content);
                	// add validate each custom field
					$('#ynauction_customfield_category').find('[data-isrequired="1"]').each(function(){
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
		$('#ynauction_customfield_category').find('[data-isrequired="1"]').each(function(){
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
	, addAjaxForCreateNewItem: function(product_id, type) {
		$('#ynauction_add_new_item').click(function() {
			$.ajaxCall('auction.setAuctionSession', 'product_id=' + product_id + '&type=' + type, 'GET');
			return false;
		});
	}, initCountdownTime: function()
	{
		var iUnixTimestamp = $('.countdown_holder').attr('unix_timestamp');
        var oUntil = new Date(iUnixTimestamp * 1000);
        if($('#defaultCountdown').length)
        	$('#defaultCountdown').countdown({until: oUntil});
	}, initChart: function()
	{
		$("<div id='ynauction_chart_tooltip'></div>").css({
            position: "absolute",
            display: "none",
            border: "1px solid #fdd",
            padding: "2px",
            "background-color": "#fee",
            opacity: 0.80
        }).appendTo("body");


        $("#placeholder").bind("plothover", function (event, pos, item) {

            if (item) {
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                array_data = item.series.data;
                index_data = item.dataIndex;
                label_item = array_data[index_data][2];
                $("#ynauction_chart_tooltip").html(label_item)
                    .css({top: item.pageY + 5, left: item.pageX + 5})
                    .fadeIn(200);
            } else {
                $("#ynauction_chart_tooltip").hide();
            }
        });
        var data = [];
        var iProductId = $('#ynauction_bidhistory #ynauction_product_id').val();
        var js_start__datepicker = $('#ynauction_bidhistory input[name="js_start__datepicker"]').val();
        var js_end__datepicker = $('#ynauction_bidhistory input[name="js_end__datepicker"]').val();

        $Core.ajax('auction.getChartData',
            {
                type: 'POST',
                params: {
                    iProductId: iProductId,
                    js_start__datepicker: js_start__datepicker,
                    js_end__datepicker: js_end__datepicker,
                },
                success: function (sOutput) {
                    var aChartData = $.parseJSON(sOutput);

                    var d = [];
                    var ticks = [];
                    var count = 0;

                    aReceive = aChartData.data;
                    title = aChartData.title;
                    index_buy_now = aChartData.idx_buynow;
                    for (var i in aReceive) {
                        d.push([count, aReceive[i][0], aReceive[i][1]]);
                        ticks.push([count, i]);
                        count = count + 1;
                    }
                    if (index_buy_now > 0) {
                        /*caculate ratio of buy it now point on chart*/
                        ratio = (d[index_buy_now][1] - d[index_buy_now - 1][1]) / (d[index_buy_now + 1][1] - d[index_buy_now - 1][1]) + index_buy_now - 1;

                        ticks[index_buy_now][0] = ratio;
                        d[index_buy_now][0] = ratio;


                        for (var j = index_buy_now + 1; j < count; j++) {
                            ticks[j][0] = j - 1;
                            d[j][0] = j - 1;
                        }
                        ;
                    }
                    var data = [{
                        data: d,
                        label: oTranslations['auction.' + title]
                    }];

                    function raw(plot, ctx) {
                        var dataPlot = plot.getData();
                        var axes = plot.getAxes();
                        var offset = plot.getPlotOffset();
                        for (var i = 0; i < dataPlot.length; i++) {
                            var series = dataPlot[i];
                            var d = (series.data[index_buy_now]);
                            var x = offset.left + axes.xaxis.p2c(d[0]);
                            var y = offset.top + axes.yaxis.p2c(d[1]);
                            var r = 5;
                            ctx.beginPath();
                            ctx.arc(x, y, r, 0, Math.PI * 2, true);
                            ctx.closePath();
                            ctx.fillStyle = "#ff0000";
                            ctx.fill();
                        }
                    };

                    var plot = $.plot("#placeholder", data, {

                        legend: {
                            labelFormatter: function (label, series) {
                                return label;
                            }
                        },
                        series: {
                            lines: {
                                show: true
                            },
                            points: {
                                show: true
                            }
                        },
                        grid: {
                            hoverable: true,
                            clickable: true
                        },
                        xaxis: {
                            show: true,
                            ticks: ticks
                        },
                        hooks: {
                            draw: [raw]
                        }

                    });
                }
            });

        $('#filter_chart').click(function () {

            var js_start__datepicker = $('#ynauction_bidhistory input[name="js_start__datepicker"]').val();
            var js_end__datepicker = $('#ynauction_bidhistory input[name="js_end__datepicker"]').val();

            $Core.ajax('auction.getChartData',
                {
                    type: 'POST',
                    params: {
                        iProductId: iProductId,
                        js_start__datepicker: js_start__datepicker,
                        js_end__datepicker: js_end__datepicker,
                    },
                    success: function (sOutput) {
                        var aChartData = $.parseJSON(sOutput);

                        var d = [];
                        var ticks = [];
                        var count = 0;

                        aReceive = aChartData.data;

                        title = aChartData.title;
                        index_buy_now = aChartData.idx_buynow;
                        for (var i in aReceive) {
                            d.push([count, aReceive[i][0], aReceive[i][1]]);
                            ticks.push([count, i]);
                            count = count + 1;
                        }
                        if (index_buy_now > 0) {
                            /*caculate ratio of buy it now point on chart*/
                            ratio = (d[index_buy_now][1] - d[index_buy_now - 1][1]) / (d[index_buy_now + 1][1] - d[index_buy_now - 1][1]) + index_buy_now - 1;

                            ticks[index_buy_now][0] = ratio;
                            d[index_buy_now][0] = ratio;


                            for (var j = index_buy_now + 1; j < count; j++) {
                                ticks[j][0] = j - 1;
                                d[j][0] = j - 1;
                            }
                            ;
                        }
                        var data = [{
                            data: d,
                            label: oTranslations['auction.' + title]
                        }];

                        function raw(plot, ctx) {
                            var dataPlot = plot.getData();
                            var axes = plot.getAxes();
                            var offset = plot.getPlotOffset();
                            for (var i = 0; i < dataPlot.length; i++) {
                                var series = dataPlot[i];
                                var d = (series.data[index_buy_now]);
                                var x = offset.left + axes.xaxis.p2c(d[0]);
                                var y = offset.top + axes.yaxis.p2c(d[1]);
                                var r = 5;
                                ctx.beginPath();
                                ctx.arc(x, y, r, 0, Math.PI * 2, true);
                                ctx.closePath();
                                ctx.fillStyle = "#ff0000";
                                ctx.fill();
                            }
                        };

                        var plot = $.plot("#placeholder", data, {

                            legend: {
                                labelFormatter: function (label, series) {
                                    return label;
                                }
                            },
                            series: {
                                lines: {
                                    show: true
                                },
                                points: {
                                    show: true
                                }
                            },
                            grid: {
                                hoverable: true,
                                clickable: true
                            },
                            xaxis: {
                                show: true,
                                ticks: ticks
                            },
                            hooks: {
                                draw: [raw]
                            }

                        });

                    }
                });

        });
	}, initDetailChart: function()
	{
		$("<div id='ynauction_chart_tooltip'></div>").css({
            position: "absolute",
            display: "none",
            border: "1px solid #fdd",
            padding: "2px",
			"z-index": "1",
            "background-color": "#fee",
            opacity: 0.80
        }).appendTo("body");


        $("#placeholder").bind("plothover", function (event, pos, item) {

            if (item) {
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                array_data = item.series.data;
                index_data = item.dataIndex;
                label_item = array_data[index_data][2];
                $("#ynauction_chart_tooltip").html(label_item)
                    .css({top: item.pageY+20, left: item.pageX-80})
                    .fadeIn(200);
            } else {
                $("#ynauction_chart_tooltip").hide();
            }
        });


        var data = [];

        var iProductId = $('.ynauction-detail-charforbidding #ynauction_product_id').val();

        var js_start__datepicker = $('.ynauction-detail-charforbidding input[name="js_start__datepicker"]').val();
        var js_end__datepicker = $('.ynauction-detail-charforbidding input[name="js_end__datepicker"]').val();

        $Core.ajax('auction.getChartData',
        {
            type: 'POST',
            params:
            {
                 iProductId: iProductId,
                 iFrontEnd : true,
                 js_start__datepicker: js_start__datepicker,
                 js_end__datepicker: js_end__datepicker,

            },
            success: function(sOutput)
            {
                var aChartData = $.parseJSON(sOutput);

                var d = [];
                var ticks = [];
                var count = 0;

                aReceive = aChartData.data;
                title = aChartData.title;
                index_buy_now = aChartData.idx_buynow;
                for(var i in aReceive)
                {
                    d.push([count,aReceive[i][0],aReceive[i][1]]);
                    ticks.push([count,i]);
                    count = count +1;
                }
              if(index_buy_now > 0){
                    /*caculate ratio of buy it now point on chart*/
                    ratio = (d[index_buy_now][1] - d[index_buy_now-1][1])/(d[index_buy_now+1][1] - d[index_buy_now-1][1]) + index_buy_now -1 ;

                    ticks[index_buy_now][0] = ratio;
                    d[index_buy_now][0] = ratio;


                    for (var j = index_buy_now + 1; j < count; j++) {
                        ticks[j][0] = j -1;
                        d[j][0] =  j -1;
                    };
                }
               var data = [{
                            data: d,
                            label: oTranslations['auction.'+title]
                        }];

             function raw(plot, ctx) {
                var dataPlot = plot.getData();
                var axes = plot.getAxes();
                var offset = plot.getPlotOffset();
                for (var i = 0; i < dataPlot.length; i++) {
                    var series = dataPlot[i];
                        var d = (series.data[index_buy_now]);
                        var x = offset.left + axes.xaxis.p2c(d[0]);
                        var y = offset.top + axes.yaxis.p2c(d[1]);
                        var r = 5;
                        ctx.beginPath();
                        ctx.arc(x,y,r,0,Math.PI*2,true);
                        ctx.closePath();
                        ctx.fillStyle = "#ff0000";
                        ctx.fill();
                }
              };

             var plot =   $.plot("#placeholder", data, {

                    legend: {
                        labelFormatter: function(label, series) {
                            return  label;
                        }
                    },
                    series: {
                        lines: {
                            show: true
                        },
                        points: {
                            show: true
                        }
                    },
                    grid: {
                        hoverable: true,
                        clickable: true
                    },
                    xaxis: {
                        show: true,
                        ticks: ticks
                    },
                    hooks: {
                        draw  : [raw]
                    }

                } );
            }
        });

	$('#filter_chart').click(function(){
		var js_start__datepicker = $('.ynauction-detail-charforbidding input[name="js_start__datepicker"]').val();
		var js_end__datepicker = $('.ynauction-detail-charforbidding input[name="js_end__datepicker"]').val();

		$Core.ajax('auction.getChartData',
			{
				type: 'POST',
				params:
					{
						iProductId: iProductId,
						js_start__datepicker: js_start__datepicker,
						js_end__datepicker: js_end__datepicker,
					},
				success: function(sOutput)
				{
					var aChartData = $.parseJSON(sOutput);
					var d = [];
					var ticks = [];
					var count = 0;
					aReceive = aChartData.data;
					title = aChartData.title;
					index_buy_now = aChartData.idx_buynow;
					for(var i in aReceive)
					{
						d.push([count,aReceive[i][0],aReceive[i][1]]);
						ticks.push([count,i]);
						count = count +1;
					}
					var data = [{
						data: d,
						label: oTranslations['auction.'+title]
					}];

					var plot =   $.plot("#placeholder", data, {

						legend: {
							labelFormatter: function(label, series) {
								return  label;
							}
						},
						series: {
							lines: {
								show: true
							},
							points: {
								show: true
							}
						},
						grid: {
							hoverable: true,
							clickable: true
						},
						xaxis: {
							show: true,
							ticks: ticks
						}

					} );

				}
			});
        });
    }
};

$Behavior.readyYnAuction = function() {
	ynauction.init();
};

;(function(window, undefined) {
	$.fn.extend({
		ynaWaiting: function(type) {
			var imageHtml = '<img src="' + ynauction.params['fb_small_loading_image_url'] + '" class="v_middle" />';

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
		ynaStopWaiting: function() {
			this.html('');
		}
	});
})(window, undefined);


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
			$(document).bind('changepage', _this.changePage); // to bind paging action
			$(document).bind('changeCustom', _this.changeCustom); // to bind paging action
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
			$('#' + _this.settings['result_div_id']).ynaWaiting('prepend');

			return false;

		};
		return ajaxForm;
	})();
	window.ynauction.ajaxForm = ajaxForm;
}(window, undefined, jQuery));

$Core.auction =
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
            $('#js_ynauction_form_upload_images').find('input[type="hidden"]').each(function () {
                formData.append($(this).prop('name'), $(this).val());
            });
        },

        dropzoneOnSuccess: function (ele, file, response) {
            $Core.auction.processResponse(ele, file, response);
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
                        $Core.dropzone.setFileError('auction', file, response.errors[i]);
                        return;
                    }
                }
            }
            return file.previewElement.classList.add('dz-success');
        }
    };

