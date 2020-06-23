var foxfeedsprostack ={
		// stack array
		storage : [],
		// push a feed to stack array
		push: function(input1, input2, input3)
		{
			foxfeedsprostack.storage.push({
				id: input1,
				value: input2,
				style: input3	
			});
		},
		// Pop a feed from stack array and get it's data
		pop: function()
		{
			var info = foxfeedsprostack.storage.pop();
			if(info)
			{
				switch(info.style)
				{
					case 'getFeedData':
						foxfeedspro.getData(info.id,info.value,'selected');
						break;
					case 'updateFeedStatus':
						foxfeedspro.updateFeedStatus(info.id,info.value,'selected');
						break;
					case 'approveNews':
						foxfeedspro.updateApprovalNews(info.id,info.value);
						break;	
					case 'approveFeed':
						foxfeedspro.updateApprovalFeeds(info.id,info.value);
						break;
				}
			}
		}
};

var foxfeedspro = {
	iGetDataSelected : 0,
	/**
	 * Check/Uncheck all checkboxes
	 */
	checkAll: function() {
		var checked = document.getElementById('feed_list_check_all').checked;
		$('.foxfeedspro_row_checkbox').each(function(index, element){
			element.checked = checked;
			var sIdName = '#foxfeedspro_item_' + element.value;
			if (element.checked == true) {
				$(sIdName).css({
					'backgroundColor' : '#FFFF88'
				});
			}
			else {
				if(element.getAttribute('position') % 2 == 0){
					$(sIdName).css({
						'backgroundColor' : '#F0F0F0'
					});
				}
				else{
					$(sIdName).css({
						'backgroundColor' : '#F9F9F9'
					});
				}
			}
		});
		foxfeedspro.setButtonStatus(checked);
		return checked;
	},

	/**
	 * Disable/enable "Delete Selected","Get Data" button
	 * @param boolean status is the status for buttons (true = enabled| false = disabled) 
	 * @return none
	 */
	setButtonStatus: function(status) {
		if (status) {
			$('#get_data').removeClass('disabled');
			$('#get_data').attr('disabled', false);
			$('#inactive_active').removeClass('disabled');
			$('#inactive_active').attr('disabled', false);
			$('#delete_selected').removeClass('disabled');
			$('#delete_selected').attr('disabled', false);
			$('#approve_selected').removeClass('disabled');
			$('#approve_selected').attr('disabled', false);
			$('#decline_selected').removeClass('disabled');
			$('#decline_selected').attr('disabled', false);
		}
		else {
			$('#get_data').addClass('disabled');
			$('#get_data').attr('disabled', true);
			$('#inactive_active').addClass('disabled');
			$('#inactive_active').attr('disabled', true);
			$('#delete_selected').addClass('disabled');
			$('#delete_selected').attr('disabled', true);
			$('#approve_selected').addClass('disabled');
			$('#approve_selected').attr('disabled', true);
			$('#decline_selected').addClass('disabled');
			$('#decline_selected').attr('disabled', true);
		}
	},

	/**
	 * Update layout when checking on a check box on signer page
	 * @return boolean status
	 */
	checkDisableStatus: function () {
		var status = false;
		$('.foxfeedspro_row_checkbox').each(function(index, element) {
			var sIdName = '#foxfeedspro_item_' + element.value;
			if (element.checked == true) 
			{
				status = true;
				$(sIdName).css({
					'backgroundColor' : '#FFFF88'
				});
			}
			else 
			{
				if(element.value % 2 == 0)
				{
					$(sIdName).css({
						'backgroundColor' : '#F0f0f0'
					});
				}
				else
				{
					$(sIdName).css({
						'backgroundColor' : '#F9F9F9'
					});
				}
			}
		});
		
		// Uncheck "Check All" checkbox if no checkbox is selected
		if(!status)
		{
			document.getElementById('feed_list_check_all').checked = false;
		}
		// Update Button Status
		foxfeedspro.setButtonStatus(status);
		return status;
	},

	/**
	 * Get feed data through feed Id
	 * @param <int> iFeedId
	 * @param <int> iIsActive
	 * @param <int> iIsAdminPanel
	 */
	getData: function (iFeedId, iIsAdminPanel, sMode, sPage) {
		$('#feed_getdata_' + iFeedId).html('Updating...');
		if(typeof sPage != 'undefined'){
			$.ajaxCall('foxfeedspro.getData', 'iFeedId=' + iFeedId + '&iIsAdminPanel=' + iIsAdminPanel+'&sMode=' + sMode + '&sPage=' + sPage);
		}
		else
			$.ajaxCall('foxfeedspro.getData', 'iFeedId=' + iFeedId + '&iIsAdminPanel=' + iIsAdminPanel+'&sMode=' + sMode+ '&sPage=0');
	},
	
	/**
	 * Enable get data button after finish get selected feed data
	 */
	getDataReport: function (sMode)
	{
		if(foxfeedspro.iGetDataSelected > 0)
		{
			foxfeedspro.iGetDataSelected--;
		}
		
		if(foxfeedspro.iGetDataSelected == 0 && sMode == 'selected')
	    {
			foxfeedspro.setButtonStatus(true);
		}
	},

	/**
	 * Get selected feed data
	 * @param <int> iIsAdminPanel is the mode that show we are getting data from back-end or not 
	 */
	getDataBySelected: function (iIsAdminPanel) {
		
		// Disable "Get Data" Button
		foxfeedspro.setButtonStatus(false);
		
		var feeds = document.getElementsByClassName('foxfeedspro_row_checkbox');
		var total = feeds.length;
		var isApproved = 0;
				
		for ( var i = total-1; i >= 0 ; i--) 
		{
			// Check selected feed and get Data if it was approved
			if (feeds[i].checked) 
			{					
				isApproved = feeds[i].getAttribute('approved');
				
				if (isApproved == 1) 
				{	
					foxfeedsprostack.push(feeds[i].value, iIsAdminPanel, 'getFeedData');
					foxfeedspro.iGetDataSelected++;
				}
			}
		}

		// Pop up to get data of a feed
			foxfeedsprostack.pop();
	},
	
	/**
	 * Set Active/Inactive a Rss Provider 
	 * @param <int> iFeedId - the related feed Id
	 * @param <int> iIsActive - the status of the feed
	 */
	updateFeedStatus: function(iFeedId, iIsActive, sMode) {
		// Get related feed
		var feed = document.getElementById("feed_" + iFeedId);
		
		// Set attribute status on the checkbox again
		if(iIsActive)
		{
			feed.setAttribute('status', 0);
		}
		else
		{
			feed.setAttribute('status', 1);
		}
		
		// Process 
		$('#feed_update_status_' + iFeedId).html('Updating...');		
		$.ajaxCall('foxfeedspro.updateFeedStatus', 'feed_id=' + iFeedId + '&is_active=' + iIsActive + '&mode=' + sMode);
	},
	
	updateStatusBySelected: function()
	{
		// Disable Buttons
		foxfeedspro.setButtonStatus(false);
		
		var feeds = document.getElementsByClassName('foxfeedspro_row_checkbox');
		var total = feeds.length;
		var isActive = 0;
				
		for ( var i = total-1; i >= 0 ; i--) 
		{
			// Check selected feed and get Data if it was approved
			if (feeds[i].checked) 
			{					
				// Get status of current feed
				isActive = feeds[i].getAttribute('status');
				// Update status before process
				if(isActive)
				{
					feeds[i].setAttribute('status', 0);
				}
				else
				{
					feeds[i].setAttribute('status', 1);
				}
				
				// Push data into storage stack
				foxfeedsprostack.push(feeds[i].value, isActive, 'updateFeedStatus');
				foxfeedspro.iGetDataSelected++;
			}
		}

		// Pop up to update status of a feed
		foxfeedsprostack.pop();
	},
	
	/**
	 *  Update news feature status 
 	 * @param <int> iNewsId is the Id of selected news
 	 * @param <int> iIsFeatured is the feature status of the news
	 */
	updateFeatured: function(iNewsId, iIsFeatured)
	{
		$('#item_update_featured_' + iNewsId).html('Updating...');
		$.ajaxCall('foxfeedspro.updateFeatured', 'iNewsId=' + iNewsId + '&iIsFeatured=' + iIsFeatured);
	},
	
	/**
	 * 
	 * @param {Object} iNewsId
	 * @param {Object} iIsApproved
	 */
	updateApprovalNews: function(iNewsId, iIsApproved)
	{
		$('#item_update_approval_' + iNewsId).html('Updating...');
		$.ajaxCall('foxfeedspro.updateApprovalNews', 'iNewsId=' + iNewsId + '&iIsApproved=' + iIsApproved);
	},
	
	/**
	 * 
 	 * @param {Object} iIsApproved
	 */
	approveNewsBySelected: function(iIsApproved)
	{
		// Disable Buttons
		foxfeedspro.setButtonStatus(false);
		var feeds = document.getElementsByClassName('foxfeedspro_row_checkbox');
		var total = feeds.length;
		var isActive = 0;
		
		for ( var i = total-1; i >= 0 ; i--) 
		{
			// Check selected feed and get Data if it was approved
			if (feeds[i].checked) 
			{				
				// Push data into storage stack
				foxfeedsprostack.push(feeds[i].value, iIsApproved, 'approveNews');
				foxfeedspro.iGetDataSelected++;
			}
		}

		// Pop up to update status of a feed
		foxfeedsprostack.pop();
	},
	
	/**
	 * 
	 * @param {Object} iNewsId
	 * @param {Object} iIsApproved
	 */
	updateApprovalFeeds: function(iFeedId, iIsApproved)
	{
		$('#item_update_approval_' + iFeedId).html('Updating...');
		$.ajaxCall('foxfeedspro.updateApprovalFeeds', 'iFeedId=' + iFeedId + '&iIsApproved=' + iIsApproved);
	},
	
	/**
	 * 
 	 * @param {Object} iIsApproved
	 */
	approveFeedBySelected: function(iIsApproved)
	{
		// Disable Buttons
		foxfeedspro.setButtonStatus(false);
		var feeds = document.getElementsByClassName('foxfeedspro_row_checkbox');
		var total = feeds.length;
		var isActive = 0;
		
		for ( var i = total-1; i >= 0 ; i--) 
		{
			// Check selected feed and get Data if it was approved
			if (feeds[i].checked) 
			{				
				// Push data into storage stack
				foxfeedsprostack.push(feeds[i].value, iIsApproved, 'approveFeed');
				foxfeedspro.iGetDataSelected++;
			}
		}

		// Pop up to update status of a feed
		foxfeedsprostack.pop();
	},
};
