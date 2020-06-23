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

{if $boolean_id==0}
<form method="post" enctype="multipart/form-data" action="{url link='admincp.gettingstarted.editmanagecategory'}id_{$scheduled_mail.scheduledmail_id}">
    <input type="hidden" name="val[scheduledmail_id]" value="{$scheduled_mail.scheduledmail_id}"/>
<div class="table_header">
    Edit Category
</div>


<div class="table">
    <div class="table_left">
       {phrase var='gettingstarted.scheduled_category'}
    </div>
    <div class="table_right">

      {$scheduled_mail.scheduledmail_name}

    </div>
    <div class="clear"></div>
</div>

<div class="table">
    <div class="table_left">
       {phrase var='gettingstarted.description'}
    </div>
    <div class="table_right">
        <textarea type="text" name="val[description]" cols="30" rows="5" >{$scheduled_mail.description}</textarea>
    </div>
    <div class="clear"></div>
</div>

<div class="table_clear">
    <input type="submit" id="submit_editscheduledmail" name="submit_editscheduledmail" value="{phrase var='core.submit'}" class="button" />
</div>
</form>
 {/if}