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
	<form id="get_contact_email" name="get_contact_email" onsubmit="" enctype="" class="global_form yncontact_manual_form" action="{url link='contactimporter.typingmanual'}" method="post" autocomplete="off">
		<input type="hidden" name="typingmanual" value="typingmanual"/>
		<div class="form-elements toggle_container">
			<div class="form-group">
				<div class="table_left">
					<label for="email_box" class="required" >{phrase var='invite.to'}</label>
				</div>
				<div class="table_right">
					<textarea class="form-control" cols="40" rows="3" id="emails" name="typing_emails" onkeydown="$Core.resizeTextarea($(this));" onkeyup="$Core.resizeTextarea($(this));"></textarea>
					<div class="extra_info">
						{phrase var='invite.separate_multiple_emails_with_a_comma'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<button class="btn btn-sm btn-primary" name="import" id="import" type="submit" onclick="" value="{_p var='import_contact'}">{_p var='import_contact'}</button>
			</div>
		</div>
	</form>
</div>