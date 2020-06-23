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
<form method="post" enctype="multipart/form-data" action="{url link='admincp.gettingstarted.addscheduledmail'}" style="margin-top:30px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            {phrase var='gettingstarted.add_a_new_schedule_mail'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">
                   {required}{phrase var='gettingstarted.time'}
                </label>
                <input type="text" name="val[time]" value="{$aScheduledMail.time}" class="input form-control" />
                (m: {phrase var='gettingstarted.month'}, w: {phrase var='gettingstarted.week'}, d: {phrase var='gettingstarted.day'}, h: {phrase var='gettingstarted.hour'}, {phrase var='gettingstarted.default_hour'})
            </div>
            <div class="form-group">
                <label>
                   {phrase var='gettingstarted.scheduled_category'}
                </label>
                <select id="val[scheduledmail_id]" name="val[scheduledmail_id]" onchange="loadCategory(this.value)" class="form-control">
                    {foreach from=$scheduled_category item=cats}
                    <option value="{$cats.scheduledmail_id}" {if $aScheduledMail.scheduledmail_id==$cats.scheduledmail_id}selected{/if}>{$cats.scheduledmail_name}</option>
                    {/foreach}

                </select>
                <span id="loading"></span>
            </div>
            <div class="form-group">
                <label>
                   {phrase var='gettingstarted.description'}
                </label>
                <div style="padding-bottom: 10px;" id="div_settings_category">
                    {$aScheduledMail.description}
                </div>
            </div>
            <div class="form-group">
                <label>
                    {required}{phrase var='gettingstarted.subtitle'}
                </label>
                <input type="text" name="val[name]" value="{$aScheduledMail.name}" class="input form-control" />
                ([full_name]: {phrase var='gettingstarted.fullname'}, [user_name] : {phrase var='gettingstarted.user_name'})
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="val[unsubscribe]" {if $aScheduledMail.active==1}checked="true" value="1" {/if}  />
                    {phrase var='gettingstarted.allow_users_to_unsubscribe_this_email'}
                </label>

            </div>
            <div class="form-group">
                <label>
                   {phrase var='gettingstarted.message'}
                </label>
                {editor id='message'}
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="val[active]" {if $aScheduledMail.active==1}checked="true" value="1" {/if}  />
                   {phrase var='gettingstarted.active'}
                </label>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" id="submit_addscheduledmail" name="submit_addscheduledmail" value="{phrase var='core.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>


<script type="text/javascript">
    {literal}
        function loadCategory(value)
        {
           $('#loading').html('Loading data ...');
           $('#div_settings_category').html('');
           $('#div_settings_category').ajaxCall('gettingstarted.loadCategory','category='+value);
        }
        {/literal}
 </script>