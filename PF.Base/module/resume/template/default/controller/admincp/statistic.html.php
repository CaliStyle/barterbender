
{literal}
<script type="text/javascript">
	function deleteAccount(account_id)
	{
		$.ajaxCall('resume.deleteAccount','account_id='+account_id);
	}
	function setApproveView(id, status) {
		$.ajaxCall('resume.setApproveView', 'id=' + id + '&status=' + status);
	}
	function setApproveWhoView(id, status) {
		$.ajaxCall('resume.setApproveWhoView', 'id=' + id + '&status=' + status);
	}
	function checkAllResume()
	{
		var checked = document.getElementById('resume_list_check_all').checked;
		$('.resume_view_checkbox').each(function(index,element){
			element.checked=checked;
			var sIdName = '#resume_view_' + element.value;
			
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

	function checkDisableStatus()
	{
		var status = false;
		$('.resume_view_checkbox').each(function(index,element){
		var sIdName = '#resume_view_' + element.value;
		
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
</script>
{/literal}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='statistics'}
        </div>
    </div>
    <!-- Search -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <td>
                    {_p var='who_s_viewed_me'} :
                </td>
                <td>
                &nbsp;{$aForms.whoview} {_p var='members'}
                </td>
            </tr>
            <tr>
                <td>
                    {_p var='view_all_resumes'} :
                </td>
                <td>
                &nbsp;{$aForms.view} {_p var='members'}
                </td>
            </tr>
        </table>
    </div>
</div>


