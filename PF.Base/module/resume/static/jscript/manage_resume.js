/**
 * Delete resume when admin click on delete button
 * @param int resume_id is the resume id need to be deleted
 * @return false
 */
function deleteResume(resume_id) {
    $Core.jsConfirm({message: oTranslations['resume.are_you_sure_you_want_to_delete_this_resume']}, function(){
		$.ajaxCall('resume.deleteResume', 'id=' + resume_id);
	});
	return false;
}

function approveResume(resume_id) {
	$.ajaxCall('resume.approveResume', 'id=' + resume_id);
	return false;
}

function denyResume(resume_id) {
	$.ajaxCall('resume.denyResume', 'id=' + resume_id);
	return false;
}
/**
 * Check/Uncheck all recording row when press on checkbox in the table head
 * @return boolean check is the status of the checking
 */
function checkAllResume() {
	var checked = document.getElementById('resume_list_check_all').checked;
	$('.resume_row_checkbox').each(function(index,element){
		element.checked = checked;
		var sIdName = '#resume_' + element.value;
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
	$('.resume_row_checkbox').each(function(index, element) {
		var sIdName = '#resume_' + element.value;
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