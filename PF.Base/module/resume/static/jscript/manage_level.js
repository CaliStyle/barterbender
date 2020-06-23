/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */

/**
 * Delete singer when admin click on delete button
 * @param int singer_id is the singer id need to be deleted
 * @return false
 */
function deleteResumeLevel(singer_id) {
    $Core.jsConfirm({message: oTranslations['are_you_sure']}, function(){
		$.ajaxCall('resume.deleteLevel', 'id=' + singer_id);
	});
	return false;
}

/**
 * Check/Uncheck all singer row when press on checkbox in the table head
 * @return boolean check is the status of the checking
 */
function checkAllLevel() {
	var checked = document.getElementById('resume_level_list_check_all').checked;
	$('.resume_level_row_checkbox').each(function(index,element){
		element.checked=checked;
		var sIdName = '#resume_level_' + element.value;
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
	$('.resume_level_row_checkbox').each(function(index, element) {
		var sIdName = '#resume_level_' + element.value;
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