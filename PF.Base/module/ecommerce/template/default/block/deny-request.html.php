<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form action="" onsubmit="$(this).ajaxCall('ecommerce.denyRequestMoney'); return false;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">

            </div>
        </div>
        <div class="panel-body">
            <div>
                <input type="hidden" name="val[id]" value="{$iRequestId}">
            </div>
            <div class="form-group">
                <label>{_p('reason_to_deny')}</label>
                <textarea class="form-control" title="{_p('reason_to_deny')}" name="val[reason]" id="deny_reason" cols="50"
                          rows="8"></textarea>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-danger">
                {phrase var='deny'}
            </button>
            <button type="button" class="btn btn-default" onclick="js_box_remove(this)">
                {phrase var='cancel'}
            </button>
        </div>
    </div>
</form>