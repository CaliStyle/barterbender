<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/28/17
 * Time: 16:21
 */
?>
<div class="panel panel-default">
    <form method="post" action="{url link='current'}"  onsubmit="return checkResponse(this);">
        <input type="hidden" name="val[request_id]" value="{$iRequestId}">
        <input type="hidden" name="val[status]" value="{$sStatus}">
        <div class="panel-body">
            <div class="form-group">
                <div class="error_message" style="display: none;">{_p var='response_message_is_required'}</div>
                <label for="">
                    {required}{_p var='response_message'}
                </label>
                <textarea name="val[response]" id="ynaf_response" cols="20" rows="5"></textarea>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='Submit'}" class="btn btn-primary" />
            <input type="button" value="{_p var='Cancel'}" class="btn btn-primary" onclick="if($('.js_box_content').length){l}js_box_remove(this);{r}else{l}window.location='{url link='admincp.yncaffiliate.manage-request'}'{r}" style="margin-top:5px;"/>
        </div>
    </form>
</div>
{literal}
<script type="text/javascript">
    function checkResponse(ele)
    {
        var data = $('#ynaf_response').val();
        if(data == "")
        {
            $(ele).find('.error_message').css('display','block');
            return false;

        }
       return true;
    }

</script>
{/literal}