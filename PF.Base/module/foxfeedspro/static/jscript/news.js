var _ah = {
	ii : [],
	cf : function() {
	},
	push : function(a, b, c) {
		_ah.ii.push( {
			id : a,
			is_active : b,
			bIsAdminpanel : c
		});
	},
	pop : function() {
		var o = _ah.ii.pop();
		if (o != null)
			getdata(o.id, o.is_active, o.bIsAdminpanel);
	}
};
var _bh = {
	ii : [],
	cf : function() {
	},
	push : function(a, b) {
		_bh.ii.push( {
			id : a,
			is_active : b
		});
	},
	pop : function() {
		var o = _bh.ii.pop();
		if (o != null)
			updatestatus(o.id, o.is_active);
	}
};
var _gh = {
	ii : [],
	cf : function() {
	},
	push : function(a, b) {
		_gh.ii.push( {
			id : a,
			is_active : b
		});
	},
	pop : function() {
		var o = _gh.ii.pop();
		if (o != null)
			updatestatusCat(o.id, o.is_active);
	}
};
var _ch = {
	ii : [],
	cf : function() {
	},
	push : function(a, b) {
		_ch.ii.push( {
			id : a,
			is_approved : b
		});
	},
	pop : function() {
		var o = _ch.ii.pop();
		if (o != null)
			updateapproval(o.id, o.is_approved);
	}
};
var is_feed_s = false;
function approvalBySelect() {
	// alert('go here');
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var select = false;
	var arr = "";
	for ( var i = count - 1; i >= 0; i--) {
		if (check[i].checked == true) {
			var is_active = document.getElementById('is_selected_active_'
					+ check[i].value).value;
			_ch.push(check[i].value, is_active);
			arr += "," + check[i].value;
			select = true;
		}
	}
	if (select == true) {
		document.getElementById('arr_selected').value = arr;
	} else {
		document.getElementById('arr_selected').value = "";
		if (is_feed_s) {
			alert($Core.foxfeedspro_message["foxfeedspro.there_is_no_selected_feeds_to_approve"]);
		} else {
			alert($Core.foxfeedspro_message["foxfeedspro.there_is_no_selected_news_to_approve"]);
		}
		return false;
	}
}

function updateapproval(id, is_approved, is_featured) {
	$('#item_update_approval_' + id).html('Updating...');
	$.ajaxCall('foxfeedspro.updateApproval', 'item_id=' + id + '&is_approved='+ is_approved + '&is_featured=' + is_featured);
}
function updateapprovalfeed(id, is_approved) {
	$('#feed_update_approval_' + id).html('Updating...');
	$.ajaxCall('foxfeedspro.updateApprovalFeed', 'item_id=' + id + '&is_approved='
			+ is_approved);
}
function updatefeatured(id, is_featured, is_approved) {
	if(is_approved == '0'){
		alert('Can not feature unapproved news');
		return;
	}
	$('#item_update_featured_' + id).html('Updating...');
	$.ajaxCall('foxfeedspro.updateFeatured', 'item_id=' + id + '&is_featured='
			+ is_featured);
}
function updatestatus(id, is_active) {
	$('#feed_update_status_' + id).html('Updating...');
	$.ajaxCall('foxfeedspro.updateStatus', 'feed_id=' + id + '&is_active='
			+ is_active);

}
function updateNewsStatus(id, is_active) {
	$('#news_update_status_' + id).html('Updating...');
	$.ajaxCall('foxfeedspro.updateNewsStatus', 'item_id=' + id + '&is_active='
			+ is_active);

}
function updatestatusCat(id, is_active) {
	$('#feed_update_status_' + id).html('Updating...');
	$.ajaxCall('foxfeedspro.updateStatusCat', 'cat_id=' + id + '&is_active='
			+ is_active);

}
function getdata(id, is_active, isAdminPanel) {
	var bIsAdminPanel = false;
	if (typeof (isAdminPanel) != 'undefined') {
		bIsAdminPanel = isAdminPanel;	
	}
	$('#feed_getdata_' + id).html('Updating...');
	$.ajaxCall('foxfeedspro.getData', 'feed_id=' + id + '&is_active=' + is_active + '&bIsAdminPanel=' + bIsAdminPanel);
}
function getDataReport(){
	iGetDataSelected--;
	if(iGetDataSelected <= 0){
		$('#getdata_button').removeAttr('disabled');
		$('#getdata_button').removeClass('disabled');
	}
}
function selectAll() {
	var check = document.getElementsByName('is_selected');
	var is_select = document.getElementById('checkAll');
	var count = check.length;
	for ( var i = 0; i < count; i++) {
		check[i].checked = is_select.checked;
	}
}
var iGetDataSelected = 0;
function getDataBySelect(isAdminPanel) {
	// alert('go here');
	var bIsAdminPanel = false;
	if (typeof (isAdminPanel) != 'undefined') {
		bIsAdminPanel = isAdminPanel;	
	}
	$('#getdata_button').addClass('disabled');
	$('#getdata_button').attr('disabled','disabled');
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var select = false;
	for ( var i = count - 1; i >= 0; i--) {
		if (check[i].checked == true) {
			// var i = check[i].value.split(',');
			var is_active = document.getElementById('is_selected_active_'
					+ check[i].value).value;
			
			// alert(check[i].value+'000'+is_active);
			// alert(i);

			// getdata(check[i].value,is_active);
			var is_approved_hidden = document.getElementById('is_approved_'
					+ check[i].value).value;
			
			if (is_approved_hidden!="" && is_approved_hidden>0) {	
				_ah.push(check[i].value, is_active, bIsAdminPanel);
				select = true;
				iGetDataSelected++;
			}
		}
	}
	if (select == true) {
		_ah.pop();
	}

	else
		alert($Core.foxfeedspro_message["foxfeedspro.there_is_no_selected_feeds_to_get_data"]);
}
function updatestatusCatBySelect() {
	// alert('go here');
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var select = false;
	for ( var i = count - 1; i >= 0; i--) {
		if (check[i].checked == true) {
			var is_active = document.getElementById('is_selected_active_'
					+ check[i].value).value;
			_gh.push(check[i].value, is_active);
			select = true;
		}
	}
	if (select == true)
		_gh.pop();
	else
		alert($Core.foxfeedspro_message["foxfeedspro.there_is_no_selected_category_to_update_status"]);
}
function updatestatusBySelect() {
	// alert('go here');
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var select = false;
	for ( var i = count - 1; i >= 0; i--) {
		if (check[i].checked == true) {

			var is_active = document.getElementById('is_selected_active_'
					+ check[i].value).value;
			var is_approved_hidden = document.getElementById('is_approved_'
					+ check[i].value).value;
			
			if (is_approved_hidden!="" && is_approved_hidden>0) {		
				_bh.push(check[i].value, is_active);
				select = true;
			}
		}
	}
	if (select == true) {
		_bh.pop();
	} else
		alert($Core.foxfeedspro_message["foxfeedspro.there_is_no_selected_feeds_to_update_status"]);
}
var is_submit = true;
function getsubmit() {
	return is_submit;
}
var is_category = false;
var is_feed = false;
function setValue() {
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var arr = "";
	var nopermission = 0;
	var countcheck = 0;
	for ( var i = count - 1; i >= 0; i--) {
		if (check[i].checked == true) {
			arr += "," + check[i].value;
			countcheck += 1;
		}
	}

	if (countcheck == 1 && check[0].value == 100 && check[0].checked) {
		nopermission = 1;
	}

	document.getElementById('arr_selected').value = arr;
	if (arr.length > 0) {
		var conf = $Core.foxfeedspro_message["foxfeedspro.are_you_sure"];
		if (is_category == true) {
			if (nopermission == 0) {
				conf = $Core.foxfeedspro_message["foxfeedspro.are_you_sure_you_want_to_delete_this_action_will_delete_all_feeds"];
			} 
			else {
				conf = $Core.foxfeedspro_message["foxfeedspro.you_have_not_permisssion_to_delete_this_category"];
				alert(conf);
				is_submit = false;
				return false;
			}
			is_category = false;

		}
		
		if (confirm(conf)) {
			is_submit = true;
		} else {
			document.getElementById('arr_selected').value = "";
			is_submit = false;
			;
		}
		
	} 
	else {
		if(is_category) 
		{
			alert($Core.foxfeedspro_message["foxfeedspro.no_selected_categories_to_delete"]);
		}
		else 
		{
			if(is_feed)
			{
				alert($Core.foxfeedspro_message["foxfeedspro.no_selected_feeds_to_delete"]);
			}			
			
			else
				alert($Core.foxfeedspro_message["foxfeedspro.no_selected_news_to_delete"]);
			
		}
		
		document.getElementById('arr_selected').value = "";
		is_submit = false;
		return false;
	}
	if(is_submit == true)
	{
		if($('#sbf_news_itmes').length >0)
		{
			
			return false;
			$('#sbf_news_itmes').submit();
		}
		
	}
	

}
function setFeed() {
	// document.getElementById('arr_selected').value ="";
	var feed = document.getElementsByName('search[type]')[0];
	document.getElementById('feed_selected').value = feed.options[feed.selectedIndex].value;
}
function toggle() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if (ele.style.display == "block") {
		ele.style.display = "none";
		text.innerHTML = "More From Source";
	} else {
		ele.style.display = "block";
		text.innerHTML = "Hide From Source";
	}
}

function getNewsPopup($feed_id) {
	$.ajaxCall("foxfeedspro.viewNewsPopuUpByFeedId", "id=" + $feed_id);
}
