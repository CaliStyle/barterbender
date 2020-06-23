/**
 * Filfer date input box when input the date from the date-picker 
 * @return false
 */
$Behavior.filterDatePicker = function() {
	// Generate date picker for "From Date" field element
	$("#js_from_date_filter").datepicker({
		dateFormat : 'mm/dd/yy',
		onSelect : function(dateText, inst) {
			var $dateFrom = $("#js_from_date_filter").datepicker("getDate");
			
			if ($dateFrom) {
				$dateFrom.setHours(0);
				$dateFrom.setMilliseconds(0);
				$dateFrom.setMinutes(0);
				$dateFrom.setSeconds(0);
			}
			return false;
		}
	});


	$("#js_from_date_filter_anchor").click(function() {
		$("#js_from_date_filter").focus();
		return false;
	});

}


