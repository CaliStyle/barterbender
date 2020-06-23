<div id="js_contest_block_email_conditions" class="js_contest_block page_section_menu_holder" style="display:none;">
	<form method="post" action="{url link='current'}" id="ynfr_edit_email_conditions_form" onsubmit="" enctype="multipart/form-data">
    <div>
        <h3>{phrase var='contest.thanks_participant'}</h3>
        <div class="table form-group">
            <div class="table_left">
                {phrase var='contest.subject'}:
            </div>
            <div class="table_right label_hover">
                <input type="text" class="form-control" name="val[subject]" value="{$aForms.subject}" id="subject" size="60" />
            </div>
        </div>
        <div class="table form-group">
            <div class="table_left">
                {phrase var='contest.message'}:
            </div>
            <div class="table_right label_hover">
                {editor id="message" value=$aForms.message}
            </div>
        </div>
        <div class="table_clear">
            <ul class="table_clear_button">
                <li> <input type='submit' class="button btn btn-primary btn-sm" name='val[save_email_condition]' value='{if $aForms.contest_status == $aContestStatus.draft.id} {phrase var='contest.save_as_draft'} {else} {phrase var='contest.save'} {/if}'> </li>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
</form>
</div>