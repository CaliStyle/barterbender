{literal}
<script type="text/javascript">
    function submitViaEnter(myfield, e) {
        var keycode;
        if (window.event)
            keycode = window.event.keyCode;
        else if (e)
            keycode = e.which;
        else
            return true;
        if (keycode == 13) {
            myfield.form.submit();
            return false;
        } else
            return true;
    }
</script>
{/literal}
<div class="formpopup">
	<form id="get_contact_email" enctype="multipart/form-data" name="get_contact_email" class="global_form" action="{url link='contactimporter.csv'}" method="post">
		<div class="form-elements toggle_container">
			<div class="form-group">
				<div class="table_left">
					<label for="email_box" class="required" >{_p var='upload_file_csv'}</label>
				</div>
				<div class="table_right">
					<input class="form-control" type="file" class="text" name="csvfile"/>
				</div>
			</div>
			<div class="form-group">
				<button name="submit_button" type="submit" style="margin-left:5px;" class="btn btn-sm btn-primary" value="{_p var='read_contact'}">{_p var='read_contact'}</button>
			</div>
		</div>
		<input type="hidden" name="uploadcsv" value="uploadcsv"/>
	</form>
</div>