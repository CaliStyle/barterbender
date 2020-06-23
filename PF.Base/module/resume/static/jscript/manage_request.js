/**
 * Filfer date input box when input the date from the date-picker 
 * @return false
 */
$Behavior.filterDatePicker = function() {
	// Generate date picker for "From Date" field element
	$("#js_from_date_filter").datepicker({
		dateFormat : 'mm/dd/yy',
		onSelect : function(dateText, inst) {
			var $dateTo = $("#js_to_date_filter").datepicker("getDate");
			var $dateFrom = $("#js_from_date_filter").datepicker("getDate");

			if ($dateTo) {
				$dateTo.setHours(0);
				$dateTo.setMilliseconds(0);
				$dateTo.setMinutes(0);
				$dateTo.setSeconds(0);
			}
			if ($dateFrom) {
				$dateFrom.setHours(0);
				$dateFrom.setMilliseconds(0);
				$dateFrom.setMinutes(0);
				$dateFrom.setSeconds(0);
			}
			if ($dateTo && $dateFrom && $dateTo < $dateFrom) {
				tmp = $("#js_to_date_filter").val();
				$("#js_to_date_filter").val($("#js_from_date_filter").val());
				$("#js_from_date_filter").val(tmp);
			}
			return false;
		}
	});

	// Generate date picker for "To Date" field element
	$("#js_to_date_filter").datepicker({
		dateFormat : 'mm/dd/yy',
		onSelect : function(dateText, inst) {
			var $dateTo = $("#js_to_date_filter").datepicker("getDate");
			var $dateFrom = $("#js_from_date_filter").datepicker("getDate");

			if ($dateTo) {
				$dateTo.setHours(0);
				$dateTo.setMilliseconds(0);
				$dateTo.setMinutes(0);
				$dateTo.setSeconds(0);
			}
			if ($dateFrom) {
				$dateFrom.setHours(0);
				$dateFrom.setMilliseconds(0);
				$dateFrom.setMinutes(0);
				$dateFrom.setSeconds(0);
			}
			if ($dateTo && $dateFrom && $dateTo < $dateFrom) {
				tmp = $("#js_to_date_filter").val();
				$("#js_to_date_filter").val($("#js_from_date_filter").val());
				$("#js_from_date_filter").val(tmp);
			}
			return false;
		}
	});

	$("#js_from_date_filter_anchor").click(function() {
		$("#js_from_date_filter").focus();
		return false;
	});

	$("#js_to_date_filter_anchor").click(function() {
		$("#js_to_date_filter").focus();
		return false;
	});
}

/**
 * Delete request when admin click on delete button
 * @param int request_id is the request id need to be deleted
 * @return false
 */
function deleteKaraokeRequest(request_id) {
    $Core.jsConfirm({message: oTranslations['are_you_sure']}, function(){
		$.ajaxCall('karaoke.deleteRequest', 'id=' + request_id);
	});
	return false;
}

/**
 * Check/Uncheck all request row when press on checkbox in the table head
 * @return boolean check is the status of the checking
 */
function checkAllRequest() {
	var checked = document.getElementById('karaoke_request_list_check_all').checked;
	$('.karaoke_request_row_checkbox').each(function(index,element){
		element.checked=checked;
		var sIdName = '#karaoke_request_' + element.value;
		if (element.checked == true) {
			$(sIdName).css({
				'backgroundColor' : '#FFFF88'
			});
		}
		else {
			if(element.value % 2 == 0){
				$(sIdName).css({
					'backgroundColor' : '#F0f0f0'
				});
			}
			else{
				$(sIdName).css({
					'backgroundColor' : '#F9F9F9'
				});
			}
		}
	});
	setDeleteSelectedButtonStatus(checked);
	return checked;
}

/**
 * Disable/enable "Delete Selected" button
 * @param boolean status is the status for button (true = enabled| false = disabled) 
 * @return none
 */
function setDeleteSelectedButtonStatus(status) {
	if (status) {
		$('.delete_selected').removeClass('disabled');
		$('.delete_selected').attr('disabled', false);
	}
	else {
		$('.delete_selected').addClass('disabled');
		$('.delete_selected').attr('disabled', true);
	}
}

/**
 * Update layout when checking on a check box on signer page
 * @return boolean status
 */
function checkDisableStatus() {
	var status = false;
	$('.karaoke_request_row_checkbox').each(function(index, element) {
		var sIdName = '#karaoke_request_' + element.value;
		if (element.checked == true) {
			status = true;
			$(sIdName).css({
				'backgroundColor' : '#FFFF88'
			});
		}
		else {
			if(element.value % 2 == 0){
				$(sIdName).css({
					'backgroundColor' : '#F0f0f0'
				});
			}
			else{
				$(sIdName).css({
					'backgroundColor' : '#F9F9F9'
				});
			}
		}
	});
	setDeleteSelectedButtonStatus(status);
	return status;
}