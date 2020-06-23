<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<!--  {*<form method="post" action="{url link='admincp.feedback.settings'}">
    <div class="table_header">
       {_p var='global_settings'}
    </div>
     <div class="table form-group">
        <div class="table_left">
            {required}{_p var='who_can_create_feedback'}
        </div>
        <div class="table_right">
            <span style="padding-bottom: 5px;display:block;">{_p var='allow_disallow_visitors_non_logged_in_users_from_posting_feedbacks'}</span>
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="val[is_allowed]" value="1" {if $is_allowed eq 1 } {value type='radio' id='is_active' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_allowed]" value="0" {if $is_allowed eq 0 } {value type='radio' id='is_active' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>   
      <div class="table form-group">
        <div class="table_left">
            {required}{_p var='email_notification'}
        </div>
        <div class="table_right">
            <span style="padding-bottom: 5px;display:block;">{_p var='send_email_alert_to_admin_for_every_new_feedback_creation'}</span>
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="val[is_email]" value="1" {if $is_email eq 1 } {value type='radio' id='is_active' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_email]" value="0" {if $is_email eq 0 } {value type='radio' id='is_active' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="table form-group">
    	<div class="table_left">
    		
    	</div>
    </div>
    <div class="table_clear">
	 <input type="submit" value="{_p var='save_changes'}" class="button" name="save_global_settings"/>
    </div>
</form>
*} -->

<form method="post" action="{url link='admincp.feedback.settings'}">
    <div class="table_header">
       {_p var='global_settings'}
    </div>
    <div class="table form-group">
    </div>
    <div class="table form-group">
          <div class="table_left">
              {required}{_p var='send_mail_to_none_user_when_someone_comment'}
          </div>
          <div class="table_right">
              <div class="item_is_active_holder">
                  <span class="js_item_active item_is_active"><input type="radio" name="is_send_mail_to_none_user" value="1" {if $is_send_mail_to_none_user eq 1 } {value type='radio' id='is_send_mail_to_none_user' default='1' selected='true'}{/if} /> {phrase var='admincp.yes'}</span>
                  <span class="js_item_active item_is_not_active"><input type="radio" name="is_send_mail_to_none_user" value="0" {if $is_send_mail_to_none_user eq 0 } {value type='radio' id='is_send_mail_to_none_user' default='1' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
              </div>
          </div>
          <div class="clear"></div>
     </div>
      <div class="table_clear">
        <input type="submit" name="save_settings" value="{_p var='save_changes'}" class="button" />
    </div>
</form>
