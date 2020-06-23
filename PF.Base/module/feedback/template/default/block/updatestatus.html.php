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
{literal}
<style type="text/css">
.table_left
{
    width: 100px !important;
    float: left !important;
}
</style>
{/literal}
<form method="post" ENCTYPE="multipart/form-data" id="js_form_statusfeedback" action="{url link='admincp.feedback.feedbacks'}" onsubmit="$(this).ajaxCall('feedback.updateStatusAdmin'); return requestformupdatestatus();">
    <input type="hidden" name="val[feedback_id]" value="{$aFeedBack.feedback_id}" />
     <input type="hidden" name="val[title]" value="{$aFeedBack.title}" />
     <input type="hidden" value="1" name="post_ajax_feedback" id="post_ajax_feedback"/>
<div class="table form-group">
    <div id="errorstatus"></div>
    <div class="table_left">
      {_p var='status'}{required}
    </div>
    <div class="table_right" style="margin-left: 100px;">
       <select id="status[status_id]" name="val[status_id]">
         <option label="" value="0"></option>
         {foreach from=$aStatus item=aStat}
             {if $aStat.status_id eq $aFeedBack.feedback_status_id}
                <option value="{$aStat.status_id}" selected>{$aStat.name}</option>
                {else}
                <option value="{$aStat.status_id}">{$aStat.name}</option>
             {/if}
         {/foreach}
       </select>
    </div>
    <div class="clear"></div>
</div>
 <div class="table form-group">
    <div class="table_left">
       {_p var='description'}{required}
    </div>
    <div class="table_right" style="margin-left: 100px;">
        <textarea type="text" name="val[description]" cols="30" rows="5" >{$aFeedBack.feedback_status}</textarea>
         <input type="hidden" name="updatestatus" value="{_p var='save_changes'}" class="button" />
    </div>
    <div class="clear"></div>
</div>
<div class="table_clear" style="text-align:center;margin-top: 20px;">
    <input type="submit" name="updatestatus" value="{_p var='save_changes'}" class="button" />
   
</div>
</form>

<script type="text/javascript">
    {literal}
    function requestformupdatestatus()
    {
        var post_ajax=$('#post_ajax_feedback').val();
        if(post_ajax==2)
            return true;
        return false;
    }
    {/literal}
</script>