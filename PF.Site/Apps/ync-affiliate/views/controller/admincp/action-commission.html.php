<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/28/17
 * Time: 16:21
 */
?>
<div class="panel panel-default">
    <form method="post" action="{url link='current'}" onsubmit="return checkReasonAndMulti(this);">
        <input type="hidden" name="val[commission_id]" value="{$iCommissionId}">
        <input type="hidden" name="val[status]" value="{$sStatus}">
        <input type="hidden" name="val[is_multi]" id="ynaf_is_multi" value="{$bIsMultiple}">
        <div class="panel-body">
            <div class=" form-group">
                <div class="error_message" style="display: none;">{_p var='reason_is_required'}</div>
                <label for="">
                    {required}{_p var='reason'}
                </label>
                <textarea name="val[reason]" id="ynaf_reason" cols="20" rows="5" class="form-control"></textarea>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
            <input type="button" value="{_p var='Cancel'}" class="btn btn-default" onclick="if($('.js_box_content').length){l}js_box_remove(this);{r}else{l}window.location='{url link='admincp.yncaffiliate.manage-commissions'}'{r}" />
        </div>
    </form>
</div>
{literal}
<script type="text/javascript">
    function checkReasonAndMulti(ele)
    {
        var data = $('#ynaf_reason').val();
        if(data == "")
        {
            $(ele).find('.error_message').css('display','block');
            return false;

        }
        if($('#ynaf_is_multi').val() == 1)
        {
            if($('#ynaf_reason_for_multi').length)
            {
                $('#ynaf_reason_for_multi').val(data);
                $('#ynaf_is_multi_submit').val(1);
                js_box_remove(ele);
                $('#yncaffiliate_commission_list').submit();
                return false;
            }
        }
        return true;
    }

</script>
{/literal}