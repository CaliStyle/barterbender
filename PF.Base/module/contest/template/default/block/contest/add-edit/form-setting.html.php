<div id="js_contest_block_settings" class="js_contest_block page_section_menu_holder" style="display:none;">
    <form method="post" class="yncontest_add_edit_form" action="{url link='current'}" id="ynfr_edit_settings_form" onsubmit="" enctype="multipart/form-data">
    	<div class="table form-group">
            <div class="table_left">
                {phrase var='contest.number_of_winning_entries'}
            </div>
            <div class="table_right label_hover">
                <input type="text" name="val[num_winning_entry]" value="{$aForms.number_winning_entry_max}" id="num_winning_entry" size="60" class='contest_add required yn_positive_number form-control'/>
            </div>
            <div class="extra_info">
    			{phrase var='contest.must_be_greater_or_equal_0'} ({phrase var='contest.set_0_for_unlimited_entries'})
    		</div>
             <label>
            <input type="checkbox" {if isset($aForms.is_auto_approve) && $aForms.is_auto_approve} checked="true" {/if} name="val[automatic_approve]" /> {phrase var='contest.set_entries_automatically_approved'}
        </label>

        </div>


        <div class="table_clear">
            <ul class="table_clear_button">
                <li> <input type='submit' class="button btn btn-primary btn-sm" name='val[save_settings]' value='{if $aForms.contest_status == $aContestStatus.draft.id} {phrase var='contest.save_as_draft'} {else} {phrase var='contest.save'} {/if}'/> </li>
            </ul>
            <div class="clear"></div>
        </div>
    </form>

</div>