<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_fundraising_block_email_conditions" class="js_fundraising_block page_section_menu_holder" style="display:none;">
<form method="post" action="{url link='current'}" id="ynfr_edit_email_conditions_form" onsubmit="" enctype="multipart/form-data">
    <div>
        <h3>{phrase var='thanks_donor'}</h3>
        <div class="table form-group">
            <div class="table_left">
                {phrase var='subject'}:
            </div>
            <div class="table_right label_hover">
                <input class="form-control" type="text" name="val[email_subject]" value="{$aForms.email_subject}" id="email_subject" size="60"/>
            </div>
        </div>
        <div class="table form-group">
            <div class="table_left">
                {phrase var='message'}:
            </div>
            <div class="table_right label_hover">
                {editor id="email_message" value=$aForms.email_message}
            </div>
        </div>
        {if !empty($aForms.target_email)}
        <div class="table form-group-follow">
            <div class="table_left">
                {phrase var='send_fundraising_letter_online'}
            </div>
            <div class="table_right">
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_send_online]" value="0" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='is_send_online' default='0' selected=true}/> {phrase var='no'}</span>
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_send_online]" value="1" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='is_send_online' default='1'}/> {phrase var='yes'}</span>
                </div>
            </div>
        </div>
        {/if}
		{module name='fundraising.keyword-placeholder'}

        <h3>{required}{phrase var='term_condition'}</h3>
        <div class="table_right">
            <textarea class="form-control" cols="40" rows="8" name="val[term_condition]">{$aForms.term_condition}</textarea>
        </div>

        <div class="table_clear">
            <button type="submit" name="val[submit_email_conditions]" value="{phrase var='save'}" class="btn btn-sm btn-primary">{phrase var='save'}</button>
            {if $bIsEdit && $aForms.is_draft == 1}
            <button type="submit" name="val[publish_email_conditions]" value="{phrase var='publish'}" class="btn btn-sm btn-default">{phrase var='publish'}</button>
            {/if}
        </div>
    </div>
</form>
</div>
