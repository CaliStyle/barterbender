<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{literal}
<script type="text/css">
    .table_right input{
        width:200px;
    }
</script>
{/literal}
{literal}
<style type="text/css">
#public_message, #core_js_messages
{
	margin-top:30px;
}
</style>
{/literal}
{if $boolean_id==0}
<form method="post" enctype="multipart/form-data" action="{url link='admincp.gettingstarted.editscheduledmail'}id_{$scheduled_mail.scheduledmail_id}">
    <div class="panel panel-default">
        <input type="hidden" name="val[scheduledmail_id]" value="{$scheduled_mail.scheduledmail_id}"/>
        <div class="panel-heading">
            {phrase var='gettingstarted.edit_schedule_mail'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                   {required}{phrase var='gettingstarted.time'}
                </label>
                <input type="text" name="val[time]" value="{$scheduled_mail.time}" class="input form-control" />
                (m: {phrase var='gettingstarted.month'}, w: {phrase var='gettingstarted.week'}, d: {phrase var='gettingstarted.day'}, h: {phrase var='gettingstarted.hour'}, {phrase var='gettingstarted.default_hour'})
            </div>
            <div class="form-group">
                <label>
                   {phrase var='gettingstarted.scheduled_category'}
                </label>
                <select id="val[scheduledmail_category_id]" name="val[scheduledmail_category_id]" class="form-control">
                    {foreach from=$scheduled_category item=cats}
                    <option value="{$cats.scheduledmail_id}" {if $scheduled_mail.scheduledmail_category_id==$cats.scheduledmail_id}selected{/if}>{$cats.scheduledmail_name}</option>
                    {/foreach}
                </select>
                <div class="clear"></div>
            </div>

            <div class="form-group">
                <label>
                    {phrase var='gettingstarted.subtitle'}:
                </label>
                <input type="text" name="val[name]" value="{$scheduled_mail.name}" class="input form-control" />
                ([full_name]: {phrase var='gettingstarted.fullname'}, [user_name] : {phrase var='gettingstarted.user_name'})
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="val[unsubscribe_email]" value="1" {if $scheduled_mail.unsubscribe_email == 1}checked{/if}  />
                    {phrase var='gettingstarted.allow_users_to_unsubscribe_this_email'}
                </label>
            </div>
            <div class="form-group">
                <label>
                   {required}{phrase var='gettingstarted.message'}
                </label>
                {editor id='message'}
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" id="submit_editscheduledmail" name="submit_editscheduledmail" value="{phrase var='core.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>
 {/if}